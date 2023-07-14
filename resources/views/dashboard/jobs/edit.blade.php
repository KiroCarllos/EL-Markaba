@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.jobs')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.jobs.index') }}"> @lang('site.users')</a></li>
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

                    <form action="{{ route('dashboard.jobs.update', $job) }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('put') }}
                        <div class="form-group">
                            <label>@lang('site.status_job')</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $job->status == "active" ? "selected" :"" }}>@lang("site.active")</option>
                                <option value="inProgress" {{ $job->status == "inProgress" ? "selected" :"" }}>@lang("site.inProgress")</option>
                                <option value="pending" {{ $job->status == "pending" ? "selected" :"" }}>@lang("site.pending")</option>
                                <option value="pending" {{ $job->status == "enough" ? "selected" :"" }}>@lang("site.enough")</option>
                                <option value="pending" {{ $job->status == "deleted" ? "selected" :"" }}>@lang("site.deleted")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.title_ar')</label>
                            <input type="text" name="title_ar" class="form-control" value="{{ $job->title_ar }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.title_en')</label>
                            <input type="text" name="title_en" class="form-control" value="{{ $job->title_en }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.description_ar')</label>
                            <input type="text" name="description_ar" class="form-control" value="{{ $job->description_ar }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.description_en')</label>
                            <input type="text" name="description_en" class="form-control" value="{{ $job->description_en }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.job_type')</label>
                            <select name="work_type" id="work_type" class="form-control">
                                <option {{ $job->work_type == "full_time" ? "selected" :"" }} value="full_time">@lang("site.full_time")</option>
                                <option {{ $job->work_type == "part_time" ? "selected" :"" }} value="part_time">@lang("site.part_time")</option>
                            </select>
                        </div>

                        <div id="work_hours" class="form-group">
                            <label>@lang('site.work_hours')</label>
                            <input max="10" type="number" name="work_hours" class="form-control" value="{{ $job->work_hours }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.contact_email')</label>
                            <input type="text" name="contact_email" class="form-control" value="{{ $job->contact_email }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.job_company_id')</label>
                            <select name="user_id" class="form-control" >
                                @isset($companies)
                                    @foreach($companies as $job_company)
                                        <option {{ $job->user_id == $job_company->id ? "selected":'' }} value="{{ $job_company->id }}"> {{ $job_company->name }}</option>
                                    @endforeach
                                @endisset

                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.address')</label>
                            <input type="text" name="address" class="form-control" value="{{ $job->address }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.location')</label>
                            <input type="text" name="location" class="form-control" value="{{ $job->location }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('site.expected_salary_from')</label>
                                    <input type="number" name="expected_salary_from" class="form-control" value="{{ $job->expected_salary_from }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('site.expected_salary_to')</label>
                                    <input type="number" name="expected_salary_to" class="form-control" value="{{ $job->expected_salary_to }}">
                                </div>
                            </div>
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

