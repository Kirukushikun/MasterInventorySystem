@extends('layouts.app1')

@section('title')
  ACCESS
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><i class="fas fa-users"></i> @yield('title')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">ACCESS </a></li>
                            <li class="breadcrumb-item active"> @yield('title')</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        @if(!empty($success_message) || session('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ !empty($success_message) ? $success_message : session('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"> FARM CODE</h3>
                            </div>
                            <form action="{{ route('accessFarm') }}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="add_farm_code" class="col-sm-2 col-form-label text-right">{{ __('FARM CODE') }}<span class="text-danger"> *</span></label>
                                        <div class="col-sm-6">
                                            <select name="add_farm_code" id="add_farm_code" class="form-control text-uppercase text-center" autofocus autocomplete="off">
                                                <option class="text-uppercase text-center" value="" hidden selected>SELECT FARM CODE</option>
                                                <option class="text-uppercase text-center" value="PFC">PFC</option>
                                                <option class="text-uppercase text-center" value="BDL">BDL</option>
                                                <option class="text-uppercase text-center" value="SWINE">SWINE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-check"></i> Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

