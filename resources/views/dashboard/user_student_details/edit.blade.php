@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.user_student_details')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.user_student_details.index') }}"> @lang('site.user_student_details')</a></li>
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

                    <form action="{{ route('dashboard.user_student_details.update', $userStudentDetail) }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('put') }}

                        <div class="form-group">
                            <label>@lang('site.name')</label>
                            <input type="text" name="name" class="form-control" value="{{ $userStudentDetail->user->name }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.email')</label>
                            <input type="email" name="email" class="form-control" value="{{ $userStudentDetail->user->email }}">
                        </div>


                        <div class="form-group">
                            <label>@lang('site.mobile')</label>
                            <input type="text" name="mobile" class="form-control" value="{{ $userStudentDetail->user->mobile }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.national_id')</label>
                            <input type="text" name="national_id" maxlength="14" minlength="14" class="form-control"
                                   value="{{ $userStudentDetail->national_id }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.faculty')</label>
                            <input type="text" name="faculty" class="form-control" value="{{$userStudentDetail->faculty }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.university')</label>
                            <input type="text" name="university" class="form-control" value="{{ $userStudentDetail->university }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.graduated_at')</label>
                            <input type="date" name="graduated_at" class="form-control"
                                   value="{{ $userStudentDetail->graduated_at }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.address')</label>
                            <input type="text" name="address" class="form-control" value="{{ $userStudentDetail->address }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.gender')</label>
                            <select name="gender" class="form-control">
                                <option  {{ $userStudentDetail->gender == "male" ?"selected" :'' }} value="male">@lang('site.male')</option>
                                <option {{ $userStudentDetail->gender == "female" ?"selected" :'' }}  value="female">@lang('site.female')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.image')</label>
                            <input type="file" name="image" class="form-control image">
                        </div>
                        <div class="form-group">
                            <img src="{{ $userStudentDetail->user->image_path }}" style="width: 100px" class="img-thumbnail image-preview" alt="">
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
                            <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> @lang('site.edit')</button>
                        </div>

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
