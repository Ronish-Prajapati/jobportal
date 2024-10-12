<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobType;
use App\Models\SavedJob;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Job;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AccountController extends Controller
{
    public function registration(){
        return view('front.account.registration');
    }
    public function processRegistration(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5',
            'confirm_password' => 'required'
        ]);
        
        if($validator->passes()){
            User::create([
                "name"=>$request->name,
                "email"=>$request->email,
                "password"=>Hash::make($request->password)
            ]);
            
        

            session()->flash('success','You have registered successfully');
            return response()->json([
                'status'=>true,
                'errors'=>[]
            ]);


        }

        else
        {
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }
    public function login(){
        return view('front.account.login');
        
    }

    public function authenticate(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);
        if($validator->passes()){
            if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
                return redirect()->route('account.profile');
            }
            else{
                return redirect()->route('account.login')->with('error','Credentials donot match');
            }
        }
        else{   
            return redirect()->route('account.login')->withErrors($validator)->withInput($request->only('email'));

        }
    } 

    public function profile(){

        $id=Auth::user()->id;
        $user=User::where('id',$id)->first();
        return view('front.account.profile',[
            'user'=>$user
        ]);
    }

    public function updateProfile(Request $request)
{
    $id = Auth::user()->id;
    $validator = Validator::make($request->all(), [
        'name' => 'required|min:5',
    ]);

    if ($validator->passes()) {
        $user = User::find($id);
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->designation = $request->designation;
        $user->save();

        return response()->json([
            'status' => true,
            'errors' => []
        ]);
    } else {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}


    public function logout(){
        Auth::logout();
        return redirect()->route('account.login');
    }


    public function updateProfilePic(Request $request){
        $id=Auth::user()->id;
        $validator=Validator::make($request->all(),[
            'image'=>'required|image'
        ]);
        if($validator->passes()){
            $image=$request->image;
            $ext=$image->getClientOriginalExtension();
            $imageName=$id.'-'.time().'.'.$ext;
            $image->move(public_path('/profile_pic'),$imageName);

            $sourcePath=public_path('/profile_pic/'.$imageName);
             $manager=new ImageManager(Driver::class);
            $image=$manager->read($sourcePath);

            $image->cover(150,150);
             $image->toPng()->save(public_path('/profile_pic/thumb/'.$imageName));

            User::where('id',$id)->update([
                'image'=>$imageName,
            ]);

            session()->flash('success','picture updated successfully');
            return response()->json([
                'status'=>true,
                'errors'=>[]
            ]);

        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }


    public function createJob(){

       $categories= Category::orderBy('name','ASC')->where('status',1)->get();
       $jobTypes=JobType:: orderBy('name','ASC')->where('status',1)->get();
       return view('front.account.job.create',[
            "categories"=>$categories,
            "jobTypes"=>$jobTypes,

            ]);
    }

    public function saveJob(Request $request){

        $rules=[
            'title'=>'required',
            'category'=>'required',
            'jobType'=>'required',
            'vacancy'=>'required',
            'location'=>'required',
            'description'=>'required',
            'company_name'=>'required',
            
        ];
        $validator=Validator::make($request->all(),$rules);

        if($validator->passes()){

            $job= new Job();
            $job->title=$request->title;
            $job->category_id=$request->category;
            $job->job_type_id=$request->jobType;
            $job->vacancy=$request->vacancy;
            $job->user_id=Auth::user()->id;
            $job->salary=$request->salary;
            $job->location=$request->location;
            $job->description=$request->description;
            $job->benefits=$request->benefits;
            $job->responsibility=$request->responsibility;
            $job->qualifications=$request->qualifications;
            $job->keywords=$request->keywords;
            $job->experience=$request->experience;
            $job->company_name=$request->company_name;
            $job->company_location=$request->company_location;
            $job->company_website=$request->company_website;
            $job->save();

            session()->flash('success','Job Added');

            return response()->json([
                'status'=>true,
                'errors'=>[]
            ]);

        }

        else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }

    public function myJob(){

        $jobs=Job::where('user_id',Auth::user()->id)->with('jobType')->get();
       return view('front.account.job.my-jobs',['jobs'=>$jobs]);

    }

    public function editJob(Request $request,$id){
        $categories= Category::orderBy('name','ASC')->where('status',1)->get();
       $jobTypes=JobType:: orderBy('name','ASC')->where('status',1)->get();
       $job=Job::where([
            'user_id'=>Auth::user()->id,
            'id'=>$id
       ])->first();
        if($job==null){
            abort(404);
        }
       
       return view('front.account.job.edit',[
            'categories'=>$categories,
            'jobTypes'=>$jobTypes,
            'job'=>$job
        ]);
    }

    public function updateJob(Request $request, $id)
{
    $rules = [
        'title' => 'required',
        'category' => 'required',
        'jobType' => 'required',
        'vacancy' => 'required',
        'location' => 'required',
        'description' => 'required',
        'company_name' => 'required',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->passes()) {

        // Check if the job exists
        $job = Job::find($id);
        if (!$job) {
            return response()->json([
                'status' => false,
                'errors' => ['Job not found.']
            ]);
        }

        // Assign new values
        $job->title = $request->title;
        $job->category_id = $request->category;
        $job->job_type_id = $request->jobType;
        $job->vacancy = $request->vacancy;
        $job->user_id = Auth::user()->id;
        $job->salary = $request->salary;
        $job->location = $request->location;
        $job->description = $request->description;
        $job->benefits = $request->benefits;
        $job->responsibility = $request->responsibility;
        $job->qualifications = $request->qualifications;
        $job->keywords = $request->keywords;
        $job->experience = $request->experience;
        $job->company_name = $request->company_name;
        $job->company_location = $request->company_location;
        $job->company_website = $request->company_website;
        
        // Save the job update
        $job->save();

        // Return success response
        session()->flash('success', 'Job updated');
        return response()->json([
            'status' => true,
            'errors' => []
        ]);

    } else {
        // Return validation errors
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}


public function deleteJob(Request $request){

   $job=Job::where([
    'user_id'=>Auth::user()->id,
    'id'=>$request->jobId
   ])->first();

  if($job==null){
    session()->flash('error','job not found');
    return response()->json([
        'status'=>true,
       
      ]);
  }

  Job::where('id',$request->jobId)->delete();
  session()->flash('success','Job deleted successfully');
    return response()->json([
        'status'=>true,
       
      ]);
}

public function myJobApplications(){
    $jobApplications=JobApplication::where('user_id',Auth::user()->id)
    ->with('job','job.jobType','job.applications')
    ->get();
    return view('front.account.my-job-applications',[
        'jobApplications'=>$jobApplications
    ]);
}

public function removeJobs(Request $request)
{
    // Fetch the job application based on the ID and the authenticated user
    $jobApplication = JobApplication::where([
        'id' => $request->id,
        'user_id' => Auth::user()->id
    ])->first();

    // If the application is not found, return an error response
    if ($jobApplication == null) {
        session()->flash('error', 'Application not found');
        return response()->json([
            'status' => false,
            'message' => 'Application not found',
        ], 404);  // Return a 404 status for "Not Found"
    }

    // If found, delete the application
    $jobApplication->delete();

    session()->flash('success', 'Application removed successfully');
    return response()->json([
        'status' => true,
        'message' => 'Application removed successfully',
    ]);
}

public function savedJobs(){
    $savedJobs=SavedJob::where('user_id',Auth::user()->id)
    ->with('job','job.jobType','job.applications')
    ->get();
    return view('front.account.job.savedJobs',[
        'savedJobs'=>$savedJobs
    ]);
}
public function removeSavedJob(Request $request)
{
    // Fetch the job application based on the ID and the authenticated user
    $savedJob = SavedJob::where([
        'id' => $request->id,
        'user_id' => Auth::user()->id
    ])->first();

    // If the application is not found, return an error response
    if ($savedJob == null) {
        session()->flash('error', 'Saved job not found');
        return response()->json([
            'status' => false,
            'message' => 'Application not found',
        ], 404);  // Return a 404 status for "Not Found"
    }

    // If found, delete the application
    SavedJob::find($request->id)->delete();
   

    session()->flash('success', 'Saved Job removed successfully');
    return response()->json([
        'status' => true,
        'message' => 'Application removed successfully',
    ]);
}
public function updatePassword(Request $request){
    $validator=Validator::make($request->all(),[
        'old_password'=>'required',
        'new_password'=>'required|min:6',
        'confirm_password'=>'required|same:new_password',
    ]);
    if($validator->fails()){
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors(),
        ]);
    }

    if(Hash::check($request->old_password,Auth::user()->password)==false){
        session()->flash('error','Your old password is incorrect');
        return response()->json([
            'status'=>true,
           
        ]);

    }
    $user=User::find(Auth::user()->id);
    $user->password=Hash::make($request->new_password);
    $user->save();
    session()->flash('success','Password updated successfully');
    return response()->json([
        'status'=>true,
       
    ]);
    
}

}
