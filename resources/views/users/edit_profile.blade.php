@extends('layout.master')

@section('main-content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Profile</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"> <i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Users</li>
                        <li class="breadcrumb-item active"> Edit Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="edit-profile">
            <div class="row">
                <div class="col-xl-4 col-lg-5">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h2 class="card-title mb-0">My Profile</h2>
                            <div class="card-options"><a class="card-options-collapse" href="javascript:void(0)"
                                    data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a
                                    class="card-options-remove" href="javascript:void(0)" data-bs-toggle="card-remove"><i
                                        class="fe fe-x"></i></a></div>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row mb-2">
                                    <div class="profile-title">
                                        <div class="d-flex"> <img class="img-70 rounded-circle" alt=""
                                                src="{{ asset('assets/images/user/7.jpg') }}">
                                            <div class="flex-grow-1"><a href="{{ route('user-profile')}}">
                                                    <h3 class="mb-1 f-20 txt-primary">MARK JECNO</h3>
                                                </a>
                                                <p class="f-12">DESIGNER</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <h4 class="form-label">Bio</h4>
                                    <textarea class="form-control" rows="5">On the other hand, we denounce with righteous indignation</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email-Address</label>
                                    <input class="form-control" placeholder="your-email@domain.com">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input class="form-control" type="password" value="password">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Website</label>
                                    <input class="form-control" placeholder="http://Uplor .com">
                                </div>
                                <div class="form-footer">
                                    <button class="btn btn-primary btn-block">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-7">
                    <form class="card">
                        <div class="card-header pb-0">
                            <h2 class="card-title mb-0">Edit Profile</h2>
                            <div class="card-options"><a class="card-options-collapse" href="javascript:void(0)"
                                    data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a
                                    class="card-options-remove" href="javascript:void(0)" data-bs-toggle="card-remove"><i
                                        class="fe fe-x"></i></a></div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label class="form-label">Company</label>
                                        <input class="form-control" type="text" placeholder="Company">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input class="form-control" type="text" placeholder="Username">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Email address</label>
                                        <input class="form-control" type="email" placeholder="Email">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name</label>
                                        <input class="form-control" type="text" placeholder="Company">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input class="form-control" type="text" placeholder="Last Name">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input class="form-control" type="text" placeholder="Home Address">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <input class="form-control" type="text" placeholder="City">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Postal Code</label>
                                        <input class="form-control" type="number" placeholder="ZIP Code">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label class="form-label">Country</label>
                                        <select class="form-control btn-square">
                                            <option value="0">--Select--</option>
                                            <option value="1">Germany</option>
                                            <option value="2">Canada</option>
                                            <option value="3">Usa</option>
                                            <option value="4">Aus</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label class="form-label">About Me</label>
                                        <textarea class="form-control" rows="5" placeholder="Enter About your description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-primary" type="submit">Update Profile </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
