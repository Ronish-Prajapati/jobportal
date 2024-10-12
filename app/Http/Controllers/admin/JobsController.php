<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobType;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function index(){
        $jobs=Job::orderBy('created_at','DESC')->with('user','applications')->paginate(10);
        return view('admin.jobs.list',[
            'jobs'=>$jobs,
        ]);
    }
    public function edit($id ,Request $request){
        $job=Job::findOrFail($id);
        $categories=Category::orderBy('name','ASC')->get();
        $jobTypes=JobType::orderBy('name','ASC')->get();
        return view('admin.jobs.edit',[
            'job'=>$job,
            'categories'=>$categories,
            'jobTypes'=>$jobTypes
        ]);
    }
     public function update($id,Request $request){
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
            $job->status = $request->status;
            $job->isFeatured = (!empty($request->isFeatured))?$request->isFeatured:0;
            
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

     public function destroy(Request $request){
        $id=$request->id;
        $job=Job::findOrFail($id);
        if(!$job){
            session()->flash('error','JOb not found');
            return response()->json([
                'status'=>false
            ]);
        }
        $job->delete();
        session()->flash('error','JOb deleted successfully');
            return response()->json([
                'status'=>true
            ]);
     }
}
