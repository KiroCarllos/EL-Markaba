@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.jobs')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li><a href="{{ route('dashboard.jobs.index') }}"> @lang('site.jobs')</a></li>
                <li class="active">@lang('site.add')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">@lang('site.add')</h3>
                </div><!-- end of box header -->

                <div class="box-body">

                    @include('partials._errors')

                    <form action="{{ route('dashboard.jobs.store') }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('post') }}

                        <div class="form-group">
                            <label>@lang('site.job_title')</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.description')</label>
                            <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.job_type')</label>
                            <input type="text" name="type" class="form-control" value="{{ old('type') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.contact_email')</label>
                            <input type="text" name="contact_email" class="form-control" value="{{ old('contact_email') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.job_company_id')</label>
                            <select name="job_company_id" class="form-control" >
                                @isset($job_companies)
                                    @foreach($job_companies as $job_company)
                                        <option {{ old('job_company_id') == $job_company->id ? "selected":'' }} value="{{ $job_company->id }}"> {{ $job_company->user->name }}</option>
                                    @endforeach
                                @endisset

                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.address')</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.location')</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.salary')</label>
                            <input type="text" name="salary" class="form-control" value="{{ old('salary') }}">
                        </div>


                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')
                            </button>
                        </div>

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
