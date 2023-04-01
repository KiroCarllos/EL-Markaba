@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.job_companies')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.job_companies.index') }}"> @lang('site.job_companies')</a></li>
                <li class="active">@lang('site.edit')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">@lang('site.edit')</h3>
                </div><!-- end of box header -->

                <div class="box-body">

                    @include('partials._errors')

                    <form action="{{ route('dashboard.job_companies.update', $jobCompany) }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('put') }}


                        <div class="form-group">
                            <label>@lang('site.name')</label>
                            <input type="text" name="name" class="form-control" value="{{ $jobCompany->user->name }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.email')</label>
                            <input type="email" name="email" class="form-control" value="{{ $jobCompany->user->email }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.mobile')</label>
                            <input type="text" name="mobile" class="form-control" value="{{ $jobCompany->user->mobile }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.bio')</label>
                            <input type="text" name="bio" class="form-control" value="{{ $jobCompany->bio }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.code')</label>
                            <input type="text" name="code" class="form-control" value="{{ $jobCompany->code }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.fax')</label>
                            <input type="text" name="fax" class="form-control" value="{{ $jobCompany->fax }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.commercial_record')</label>
                            <input type="text" name="commercial_record" class="form-control" value="{{ $jobCompany->commercial_record }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.tax_card')</label>
                            <input type="text" name="tax_card" class="form-control" value="{{ $jobCompany->tax_card }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.address')</label>
                            <input type="text" name="address" class="form-control" value="{{ $jobCompany->address }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.created_date')</label>
                            <input type="date" name="created_date" class="form-control" value="{{ $jobCompany->created_date }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.password')</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.password_confirmation')</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <div class="form-group">
                            <img src="{{ $jobCompany->user->image_path }}" style="width: 100px" class="img-thumbnail image-preview" alt="">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.image')</label>
                            <input type="file" name="image" class="form-control image">
                        </div>
                        <div class="form-group">
                            <img src="{{ asset('uploads/job_company/default.png') }}"  style="width: 100px" class="img-thumbnail image-preview" alt="">
                        </div>



                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> @lang('site.edit')</button>
                        </div>

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
