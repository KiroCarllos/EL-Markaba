@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.posts')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li><a href="{{ route('dashboard.posts.index') }}"> @lang('site.posts')</a></li>
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

                    <form action="{{ route('dashboard.posts.store') }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('post') }}
                        <div class="form-group">
                            <label>@lang('site.status_post')</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ old("status") == "active" ? "selected" :"" }}>@lang("site.active")</option>
                                <option value="disActive" {{ old("status") == "disActive" ? "selected" :"" }}>@lang("site.disActive")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.title_en')</label>
                            <input type="text" name="title_en" class="form-control" value="{{ old("title_en") }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.title_ar')</label>
                            <input type="text" name="title_ar" class="form-control" value="{{ old("title_ar") }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.description_ar')</label>
                            <textarea class="form-control" name="description_ar" id="address" cols="1" rows="3">{{ old("description_ar") }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.description_en')</label>
                            <textarea class="form-control" name="description_en" id="address" cols="1" rows="3">{{ old("description_en") }}</textarea>
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
