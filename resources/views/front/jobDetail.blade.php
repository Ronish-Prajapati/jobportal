@extends('front.layouts.app')

@section('main')

    <section class="section-4 bg-2">
        <div class="container pt-5">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class="rounded-3 p-3">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('jobs') }}">
                                    <i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;Back to Jobs
                                </a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="container job_details_area">
            <div class="row pb-5">
                <!-- Job Details Section -->
                <div class="col-md-8">
                    @include('front.message')
                    <div class="card shadow border-0">
                        <div class="job_details_header">
                            <div class="single_jobs white-bg d-flex justify-content-between">
                                <div class="jobs_left d-flex align-items-center">
                                    <div class="jobs_conetent">
                                        <a href="#">
                                            <h4>{{ $job->title }}</h4>
                                        </a>
                                        <div class="links_locat d-flex align-items-center">
                                            <div class="location">
                                                <p><i class="fa fa-map-marker"></i> {{ $job->location }}</p>
                                            </div>
                                            <div class="location">
                                                <p><i class="fa fa-clock-o"></i> {{ $job->jobType->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="jobs_right">
                                    <div class="apply_now {{ $count == 1 ? 'saved-job' : '' }}">
                                        <a class="heart_mark" href="javascript:void();"
                                            onclick="saveJob({{ $job->id }})">
                                            <i class="fa fa-heart-o" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="descript_wrap white-bg">
                            <div class="single_wrap">
                                <h4>Job description</h4>
                                {!! nl2br(e($job->description)) !!}
                            </div>
                            @if ($job->responsibility)
                                <div class="single_wrap">
                                    <h4>Responsibility</h4>
                                    {!! nl2br(e($job->responsibility)) !!}
                                </div>
                            @endif
                            @if ($job->qualifications)
                                <div class="single_wrap">
                                    <h4>Qualifications</h4>
                                    {!! nl2br(e($job->qualifications)) !!}
                                </div>
                            @endif
                            @if ($job->benefits)
                                <div class="single_wrap">
                                    <h4>Benefits</h4>
                                    {!! nl2br(e($job->benefits)) !!}
                                </div>
                            @endif
                            <div class="border-bottom"></div>
                            <div class="pt-3 text-end">
                                @if (Auth::check())
                                    <a href="#" onclick="saveJob({{ $job->id }})"
                                        class="btn btn-secondary">Save</a>
                                @endif
                                @if (Auth::check())
                                    <a href="#" onclick="applyJob({{ $job->id }})"
                                        class="btn btn-primary">Apply</a>
                                @else
                                    <a href="javascript:void();" class="btn btn-primary disabled">Login To Apply</a>
                                @endif

                            </div>
                        </div>
                    </div>
                    @if (Auth::user()->id == $job->user_id)


                        <div class="card shadow border-0 mt-4">
                            <div class="job_details_header">
                                <div class="single_jobs white-bg d-flex justify-content-between">
                                    <div class="jobs_left d-flex align-items-center">
                                        <div class="jobs_conetent">
                                            <h4>Applicants</h4>
                                        </div>
                                    </div>
                                    <div class="jobs_right">
                                    </div>
                                </div>
                            </div>
                            <div class="descript_wrap white-bg">
                                <table class="table table-striped">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Applied Date</th>
                                    </tr>
                                    @if ($applications->isNotEmpty())
                                        @foreach ($applications as $application)
                                            <tr>
                                                <td>{{ $application->user->name }}</td>
                                                <td>{{ $application->user->email }}</td>
                                                <td>{{ $application->user->mobile }}</td>
                                                <td>{{ \Carbon\Carbon::parse($application->applied_Date)->format('d M ,Y') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4">
                                                <p class="px-2">No applicants</p>
                                            </td>
                                        </tr>
                                    @endif


                                </table>

                            </div>
                        </div>
                    @endif
                </div>

                <!-- Job Summary and Company Details Section -->
                <div class="col-md-4">
                    <!-- Job Summary -->
                    <div class="card shadow border-0">
                        <div class="job_sumary">
                            <div class="summery_header pb-1 pt-4">
                                <h3>Job Summary</h3>
                            </div>
                            <div class="job_content pt-3">
                                <ul>
                                    <li>Published on:
                                        <span>{{ \Carbon\Carbon::parse($job->created_at)->format('d M,Y') }}</span></li>
                                    <li>Vacancy: <span>{{ $job->vacancy }}</span></li>
                                    <li>Salary: <span>{{ $job->salary }}</span></li>
                                    <li>Location: <span>{{ $job->location }}</span></li>
                                    <li>Job Nature: <span>{{ $job->jobType->name }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Company Details -->
                    <div class="card shadow border-0 my-4">
                        <div class="job_sumary">
                            <div class="summery_header pb-1 pt-4">
                                <h3>Company Details</h3>
                            </div>
                            <div class="job_content pt-3">
                                <ul>
                                    <li>Name: <span>{{ $job->company_name }}</span></li>
                                    <li>Location: <span>{{ $job->company_location }}</span></li>
                                    <li>Website: <span><a href="#">{{ $job->company_website }}</a></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection


@section('customJs')
    <script>
        function applyJob(id) {
            if (confirm("Are you sure you want to apply?")) {
                $.ajax({
                    url: "{{ route('applyJob') }}",
                    type: 'post',
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}' // Ensure CSRF token is passed
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            alert(response.message);
                            window.location.reload(); // Reload page on success
                        } else {
                            alert(response.message); // Show error message if status is false
                        }
                    },
                    error: function(xhr, status, error) {
                        // This catches any server or network errors
                        alert('An error occurred: ' + xhr.responseText);
                    }
                });
            }
        }

        function saveJob(id) {
            $.ajax({
                url: "{{ route('savedJob') }}",
                type: 'post',
                data: {
                    id: id, // Pass the job ID or other relevant data
                    _token: '{{ csrf_token() }}' // Pass the CSRF token for security
                },
                success: function(response) {
                    if (response.status) {
                        // Success message
                        alert(response.message); // Replace with your preferred method of displaying the message
                    } else {
                        // Error message
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>
@endsection
