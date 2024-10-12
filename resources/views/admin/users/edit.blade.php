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
                        <form action="{{ route('admin.users.update',$user->id) }}" method="post" id="userForm" name="userForm">
                            @csrf
                            @method('PUT')
                            <!-- This specifies the PUT method -->
                            <div class="card border-0 shadow mb-4">
                                <div class="card-body  p-4">
                                    <h3 class="fs-4 mb-1">Edit User</h3>
                                    <div class="mb-4">
                                        <label for="" class="mb-2">Name*</label>
                                        <input type="text" placeholder="Enter Name" name="name" id="name"
                                            class="form-control" value="{{ $user->name }}">
                                        <p></p>
                                    </div>
                                    <div class="mb-4">
                                        <label for="" class="mb-2">Email</label>
                                        <input type="text" placeholder="Enter Email" name="email" id="email"
                                            class="form-control" value="{{ $user->email }}" readonly>
                                    </div>
                                    <div class="mb-4">
                                        <label for="" class="mb-2">Designation</label>
                                        <input type="text" placeholder="Designation" name="designation" id="designation"
                                            class="form-control" value="{{ $user->designation }}">
                                    </div>
                                    <div class="mb-4">
                                        <label for="" class="mb-2">Mobile</label>
                                        <input type="text" placeholder="Mobile" name="mobile" id="mobile"
                                            class="form-control" value="{{ $user->mobile }}">
                                    </div>
                                </div>
                                <div class="card-footer  p-4">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                        </form>
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
        $('#userForm').submit(function(e) {
            e.preventDefault(); // Prevent form from submitting traditionally

            $.ajax({
                url: '{{ route("admin.users.update",$user->id) }}', // Use correct route
                type: 'PUT', // Correct method for update
                dataType: 'json',
                data: $('#userForm').serialize(), // Correct form ID and serialize data
                success: function(response) {
                    if (response.status === true) {
                        alert('Profile updated successfully.');
                    } else {
                        var errors = response.errors;
                        if (errors.name) {
                            $('#name').addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback')
                                .html(errors.name);
                        } else {
                            $('#name').removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback')
                                .html('');
                        }
                    }
                },
                error: function(xhr) {
                    // Handle errors from the server side
                    console.log('An error occurred:', xhr.responseText);
                }
            });
        });
        </script>
@endsection
