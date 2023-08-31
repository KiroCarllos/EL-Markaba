@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.sliders')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li><a href="{{ route('dashboard.sliders.index') }}"> @lang('site.sliders')</a></li>
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

                    <form action="{{ route('dashboard.sliders.store') }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('post') }}
                        <div class="form-group">
                            <label>@lang('site.status_slider')</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ old("status") == "active" ? "selected" :"" }}>@lang("site.active")</option>
                                <option value="disActive" {{ old("status") == "disActive" ? "selected" :"" }}>@lang("site.disActive")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.user_views')</label>
                            <select multiple name="role[]" class="form-control js-enable-tags">
                                <option value="company" {{ old("status") == "company" ? "selected" :"" }}>@lang("site.company")</option>
                                <option value="student" {{ old("status") == "student" ? "selected" :"" }}>@lang("site.student")</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label>@lang('site.image')</label>
                            <input type="file" name="image" class="form-control image">
                        </div>
                        <div class="form-group">
                            <img src="{{ asset('default.png') }}"  style="width: 400px" class="img-thumbnail image-preview" alt="">
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
