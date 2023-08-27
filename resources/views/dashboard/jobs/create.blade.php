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
                            <label>@lang('site.title_ar')</label>
                            <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.title_en')</label>
                            <input type="text" name="title_en" class="form-control" value="{{ old('title_en') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.description_ar')</label>
                            <input type="text" name="description_ar" class="form-control" value="{{ old('description_ar') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.description_en')</label>
                            <input type="text" name="description_en" class="form-control" value="{{ old('description_en') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.job_type')</label>
                            <select name="work_type" id="work_type" class="form-control">
                                <option {{ old("work_type") == "full_time" ? "selected" :"" }} value="full_time">@lang("site.full_time")</option>
                                <option {{ old("work_type") == "part_time" ? "selected" :"" }} value="part_time">@lang("site.part_time")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.work_type')</label>
                            <select name="work_type" id="job_type" class="form-control">
                                <option {{ old("job_type") == "online" ? "selected" :"" }} value="online">@lang("site.online")</option>
                                <option {{ old("job_type") == "from_company" ? "selected" :"" }} value="from_company">@lang("site.from_company")</option>
                            </select>
                        </div>
                        <div id="work_hours" class="form-group">
                            <label>@lang('site.work_hours')</label>
                            <input max="10" type="number" name="work_hours" class="form-control" value="{{ old('work_hours') ?? 8 }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.contact_email')</label>
                            <input type="text" name="contact_email" class="form-control" value="{{ old('contact_email') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.job_company_id')</label>
                            <select name="user_id" class="form-control" >
                                @isset($job_companies)
                                    @foreach($job_companies as $job_company)
                                        <option {{ old('user_id') == $job_company->id ? "selected":'' }} value="{{ $job_company->id }}"> {{ $job_company->name }}</option>
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
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label>@lang('site.expected_salary_from')</label>
                                      <input type="number" name="expected_salary_from" class="form-control" value="{{ old('expected_salary_from') }}">
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label>@lang('site.expected_salary_to')</label>
                                      <input type="number" name="expected_salary_to" class="form-control" value="{{ old('expected_salary_to') }}">
                                  </div>
                              </div>
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

