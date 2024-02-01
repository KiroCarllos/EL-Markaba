@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.job_offices')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li><a href="{{ route('dashboard.job_offices.index') }}"> @lang('site.job_offices')</a></li>
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

                    <form action="{{ route('dashboard.job_offices.store') }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('post') }}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('site.name')</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('site.mobile')</label>
                                    <input maxlength="11" type="text" name="mobile" class="form-control" value="{{ old('mobile') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('site.email')</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('site.password')</label>
                                    <input type="text" name="password" class="form-control">
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('site.father_name')</label>
                                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}">
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('site.father_mobile')</label>
                                    <input maxlength="11" type="text" name="father_mobile" class="form-control" value="{{ old('father_mobile') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('site.church_name')</label>
                                    <input type="text" name="church_name" class="form-control" value="{{ old('church_name') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('site.amen_name')</label>
                                    <input type="text" name="amen_name" class="form-control" value="{{ old('amen_name') }}">
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('site.amen_mobile')</label>
                                    <input maxlength="11" type="text" name="amen_mobile" class="form-control" value="{{ old('amen_mobile') }}">
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('site.image_office')</label>
                                    <input type="file" name="logo" class="form-control image_commercial_record">
                                </div>

                                <div class="form-group">
                                    <img src="{{ asset('uploads/user_images/default.png') }}" style="width: 200px;height: 200px"
                                         class="img-thumbnail image_commercial_record-preview" alt="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('site.ammen_national_image')</label>
                                    <input type="file" name="amen_national_image" class="form-control image_tax_card">
                                </div>
                                <div class="form-group">
                                    <img src="{{ asset('uploads/user_images/default.png') }}" style="width: 200px;height: 200px"
                                         class="img-thumbnail image_tax_card-preview" alt="">
                                </div>
                            </div>



                          <div class="col-md-12 ">
                              <div class="form-group">
                                  <button type="submit" class="btn btn-primary btn-block"><i
                                          class="fa fa-plus"></i> @lang('site.add')</button>
                              </div>
                          </div>
                      </div>

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
