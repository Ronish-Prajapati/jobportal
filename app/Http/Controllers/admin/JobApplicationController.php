<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function index(){
        $jobapplications=JobApplication::orderBy("created_at",'DESC')->with('job','user','employer')->paginate(10);
        return view('admin.job-applications.list',['jobapplications'=>$jobapplications]);
    }

    public function destroy(Request $request){
        $id=$request->id;


        $jobapplication=JobApplication::find($id);
        if($jobapplication==null){
            session()->flash('error','Appplication not found');
            return response()->json([
                'status'=>false,
            ]);
        }
        $jobapplication->delete();
        session()->flash('error','Appplication deleted ');
            return response()->json([
                'status'=>true,
            ]);

        }
}
