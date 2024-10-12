@extends('front.layouts.app')

@section('main')
    <section class="section-5 bg-2">
        <div class="container py-5">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    @include('admin.sidebar')
                </div>
                <div class="col-lg-9">
                    @include('front.message')
                    <div class="card border-0 shadow mb-4">
                        <div class="card-body card-form">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="fs-4 mb-1">Job Applicatiosn</h3>
                                </div>
                                <div style="margin-top: -10px;">

                                </div>

                            </div>
                            <div class="table-responsive">
                                <table class="table ">
                                    <thead class="bg-light">
                                        <tr>
                                            <th scope="col">Job Title</th>
                                            <th scope="col">User</th>
                                            <th scope="col">Employer</th>
                                            <th scope="col">Applied Date</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-0">
                                        @if ($jobapplications->isNotEmpty())
                                            @foreach ($jobapplications as $jobapplication)
                                                <tr class="active">
                                                    <td>
                                                        {{ $jobapplication->job->title }}</td>

                                                    <td>{{ $jobapplication->user->name }}</td>
                                                    <td>{{ $jobapplication->employer->name }}</td>

                                                    <td>{{ \Carbon\Carbon::parse($jobapplication->applied_date)->format('d M, Y') }}
                                                    </td>



                                                    <td>
                                                        <div class="action-dots">
                                                            <button href="#" class="btn" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">


                                                                <li>
                                                                    <a class="dropdown-item" href="javascript:void(0);"
                                                                        onclick="deleteApplication({{ $jobapplication->id }})">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                                        Delete
                                                                    </a>
                                                                </li>

                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script type="text/javascript">
        function deleteApplication(id) {
            if (confirm("Are you sure you want to delete this application?")) {
                $.ajax({
                    url: '{{ route('admin.jobApplications.destroy', ':id') }}'.replace(':id',
                    id), // Dynamically set the ID in the URL
                    type: 'DELETE', // Correct HTTP method
                    data: {
                        _token: '{{ csrf_token() }}', // Add CSRF token for protection
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === true) {
                            alert('Job deleted successfully');
                            window.location.href =
                            "{{ route('admin.jobapplications') }}"; // Redirect to the user list page
                        } else {
                            alert('Failed to delete job');
                        }
                    },
                    error: function(xhr) {
                        console.log('An error occurred:', xhr.responseText);
                    }
                });
            }
        }
    </script>
@endsection
