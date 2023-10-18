@extends('layouts.app', ['title' => 'Service Provider Info'])

@section('breadcrumb')
    <div class="col-sm-6">
        <h1 class="m-0">
            Service Provider Info
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Service Provider Info</li>
        </ol>
    </div>
@endsection

@section('content')
    <div class="px-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Service Provider Info
                </h3>
            </div>

            <div class="card-body">
                <form action="{{route('service-provider-info.update',$serviceProviderInfo->id)}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username" class="required">Username</label>
                                    <input type="text" name="username" id="username" required class="form-control"
                                        placeholder="Enter Username" value="{{$serviceProviderInfo->username}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="required">Password</label>
                                    <input type="text" name="password" id="password" required class="form-control"
                                        placeholder="Enter password" value="{{$serviceProviderInfo->password}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="url" class="required">URL</label>
                                    <input type="text" name="url" id="url" required class="form-control"
                                        placeholder="Enter url" value="{{$serviceProviderInfo->url}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
