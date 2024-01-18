@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.user_student_details')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li><a href="{{ route('dashboard.fathers.index') }}"> @lang('site.fathers')</a>
                </li>
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

                    <form action="{{ route("dashboard.fathers.update",$father->id) }}" method="post"
                          enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('put') }}
                        <input type="hidden" name="user_id" value="{{$father->id}}">

                        <div class="form-group">
                            <label>@lang('site.name')</label>
                            <input type="text" name="name" class="form-control" value="{{ $father->name }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.status_account')</label>
                            <select name="status" class="form-control">
                                <option
                                    value="active" {{ $father->status == "active" ? "selected" :"" }}>@lang("site.active")</option>
                                <option
                                    value="inProgress" {{ $father->status == "inProgress" ? "selected" :"" }}>@lang("site.inProgress")</option>
                                <option
                                    value="pending" {{ $father->status == "pending" ? "selected" :"" }}>@lang("site.pending")</option>
                                <option
                                    value="blocked" {{ $father->status == "blocked" ? "selected" :"" }}>@lang("site.blocked")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.email')</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ $father->email }}">
                        </div>


                        <div class="form-group">
                            <label>@lang('site.mobile')</label>
                            <input type="text" name="mobile" class="form-control"
                                   value="{{ $father->mobile }}">
                        </div>

                        @if(!is_null($father->father_details->area_id))
                            <div class="form-group">
                                <label>@lang('site.areas')</label>
                                <select name="area_id" class="form-control">
                                    <option></option>
                                    @isset($areas)
                                        @foreach($areas as $area)
                                            <option {{ $area->id == $father->father_details->area_id ?"Selected":"" }} value="{{ $area->id  }}">{{ $area->name_ar }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        @endif


                        <div class="form-group">
                            <label>@lang('site.image')</label>
                            <input type="file" name="image" class="form-control image">
                        </div>
                        <div class="form-group">
                            <img src="{{ $father->image }}" style="width: 100px"
                                 class="img-thumbnail image-preview" alt="">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.national_image')</label>
                            <input type="file" name="national_image" class="form-control image_company">
                        </div>
                        <div class="form-group">
                            <img src="{{ $father->father_details->national_image }}" style="width: 100px"
                                 class="img-thumbnail image_company-preview" alt="">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.password')</label>
                            <input type="password" name="password"
                                   placeholder="@lang("site.fill password if need to reset only")" class="form-control">
                        </div>


                        @if(!is_null($father->device_token))
                        <div class="form-group">
                            <label>@lang('site.notify')</label>
                            <input type="text" name="notify"
                                   placeholder="@lang("site.fill notify if need to send notification only")" class="form-control">
                        </div>
                        @endif
                        <div class="form-group">
                            <label>@lang('site.message')</label>
                            <input type="text" name="message" placeholder="@lang("site.fill message if need to send notification only")" class="form-control">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> @lang('site.edit')
                            </button>
                        </div>
                    </form><!-- end of form -->
                </div><!-- end of box body -->
            </div><!-- end of box -->
        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection




