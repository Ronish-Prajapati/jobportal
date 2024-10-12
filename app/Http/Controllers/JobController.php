<?php

namespace App\Http\Controllers;

use App\Mail\JobNotificationEmail;
use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use App\Models\JobApplication;
use App\Models\JobType;
use App\Models\SavedJob;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('status', '1')->get();
        $jobTypes = JobType::where('status', '1')->get();
        $jobs = Job::where('status', 1);

        if (!empty($request->keyword)) {
            $jobs = $jobs->where(function ($query) use ($request) {
                $query->orWhere('title', 'like', '%' . $request->keyword . '%');
                $query->orWhere('keywords', 'like', '%' . $request->keyword . '%');

            });
        }

        if (!empty($request->location)) {
            $jobs = $jobs->where('location', $request->location);
        }

        if (!empty($request->category)) {
            $jobs = $jobs->where('category_id', $request->category);
        }

        $jobTypeArray = [];
        if (!empty($request->jobType)) {
            $jobTypeArray = explode(',', $request->jobType);
            $jobs = $jobs->whereIn('jobType_id', $jobTypeArray);
        }
        if (!empty($request->experience)) {
            $jobs = $jobs->where('experience', $request->experience);
        }

        $jobs = $jobs->with(['jobType', 'category']);
        if ($request->sort == 0) {
            $jobs = $jobs->orderBy('created_at', 'ASC');
        } else {
            $jobs = $jobs->orderBy('created_at', 'DESC');
        }
        $jobs = $jobs->paginate(9);


        return view('front.jobs', [
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'jobs' => $jobs,
            'jobTypeArray' => $jobTypeArray
        ]);
    }

    public function detail($id)
    {

        $job = Job::where(['id' => $id, 'status' => 1,])->with('jobType', 'category')->first();

        if ($job == null) {
            abort(404);
        }
        $count=0;
        if(Auth::user()){
            $count = SavedJob::where([
                'user_id' => Auth::user()->id,
                'job_id' => $id
            ])->count();

        }

        $applications=JobApplication::where('job_id',$id)->with('user')->get();
        return view('front.jobDetail', [
            'job' => $job,
            'count' => $count,
            'applications'=>$applications
        ]);

    }

    public function applyJob(Request $request)
    {
        try {
            $id = $request->id;
            $job = Job::find($id);

            if (!$job) {
                session()->flash('error', 'Job does not exist');
                return response()->json([
                    'status' => false,
                    'message' => 'Job does not exist',
                ]);
            }

            $employer_id = $job->user_id;

            if ($employer_id == Auth::user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You cannot apply for your own job',
                ]);
            }

            // Check if the user has already applied for the job
            $existingApplication = JobApplication::where('job_id', $id)
                ->where('user_id', Auth::user()->id)
                ->first();

            if ($existingApplication) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have already applied for this job',
                ]);
            }

            // Proceed with job application creation
            $application = new JobApplication();
            $application->job_id = $id;
            $application->user_id = Auth::user()->id;
            $application->employer_id = $employer_id;
            $application->applied_date = now();
            $application->save();

            //send email
            $employer = User::where('id', $employer_id)->first();
            $mailData = [
                'employer' => $employer,
                'user' => Auth::user(),
                'job' => $job,
            ];

            if ($employer && $employer->email) {
                Mail::to($employer->email)->send(new JobNotificationEmail($mailData)); // Pass employer's email
                session()->flash('success', 'You have successfully applied');
                return response()->json([
                    'status' => true,
                    'message' => 'You have successfully applied',
                ]);
            } else {
                session()->flash('error', 'Employer email not found');
                return response()->json([
                    'status' => false,
                    'message' => 'Employer email not found',
                ]);
            }


        } catch (\Exception $e) {
            \Log::error('Job application error: ' . $e->getMessage()); // Log the error for debugging
            return response()->json([
                'status' => false,
                'message' => 'There was an error while applying. Please try again later.',
            ]);
        }
    }

    public function savedJob(Request $request)
    {
        $id = $request->id;
        $job = Job::find($id);

        if ($job == null) {
            return response()->json([
                'status' => false,
                'message' => 'Job not found'
            ]);
        }

        // Check if the user has already saved the job
        $count = SavedJob::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();

        if ($count > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Job already saved'
            ]);
        }

        // Save the job
        $savedJob = new SavedJob();
        $savedJob->job_id = $id;
        $savedJob->user_id = Auth::user()->id;
        $savedJob->save();

        return response()->json([
            'status' => true,
            'message' => 'Job saved successfully'
        ]);
    }

  




}
