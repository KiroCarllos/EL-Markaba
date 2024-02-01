@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.job_offices')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.job_offices.index') }}"> @lang('site.job_offices')</a></li>
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
                    <form action="{{ route('dashboard.job_offices.update', $jobOffice->id) }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('put') }}
                        <input type="hidden" name="user_id" value="{{$jobOffice->id}}">
                        <div class="form-group">
                            <label>@lang('site.status_account')</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $jobOffice->status == "active" ? "selected" :"" }}>@lang("site.active")</option>
                                <option value="inProgress" {{ $jobOffice->status == "inProgress" ? "selected" :"" }}>@lang("site.inProgress")</option>
                                <option value="pending" {{ $jobOffice->status == "pending" ? "selected" :"" }}>@lang("site.pending")</option>
                                <option value="blocked" {{ $jobOffice->status == "blocked" ? "selected" :"" }}>@lang("site.blocked")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.name')</label>
                            <input type="text" name="name" class="form-control" value="{{ $jobOffice->name }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.mobile')</label>
                            <input type="text" name="mobile" class="form-control" value="{{ $jobOffice->mobile }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.email')</label>
                            <input type="email" name="email" class="form-control" value="{{ $jobOffice->email }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.password')</label>
                            <input type="password" name="password" placeholder="@lang("site.fill password if need to reset only")" class="form-control" >
                        </div>

                        <div class="form-group">
                            <label>@lang('site.father_name')</label>
                            <input type="text" name="father_name" class="form-control" value="{{ $jobOffice->office_details->father_name }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.father_mobile')</label>
                            <input type="text" name="father_mobile" class="form-control" value="{{ $jobOffice->office_details->father_mobile }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.church_name')</label>
                            <input type="text" name="church_name" class="form-control" value="{{ $jobOffice->office_details->church_name }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.amen_name')</label>
                            <input type="text" name="amen_name" class="form-control" value="{{ $jobOffice->office_details->amen_name }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.amen_mobile')</label>
                            <input type="text" name="amen_mobile" class="form-control" value="{{ $jobOffice->office_details->amen_mobile }}">
                        </div>



                        <div class="form-group">
                            <label>@lang('site.image_office')</label>
                            <input type="file" name="logo" class="form-control image">
                        </div>
                        <div class="form-group">
                            <img src="{{ asset($jobOffice->image ??'default.png') }}"  style="width: 400px" class="img-thumbnail image-preview" alt="">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.ammen_national_image')</label>
                            <input type="file" name="amen_national_image" class="form-control image_commercial_record">
                        </div>
                        <div class="form-group">
                            <img src="{{ asset($jobOffice->office_details->amen_national_image ??'default.png') }}"  style="width: 400px" class="img-thumbnail image_commercial_record-preview" alt="">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.notify')</label>
                            <input type="text" name="notify"
                                   placeholder="@lang("site.fill notify if need to send notification only")" class="form-control">
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
<script>
    import Index from "../../../../public/dashboard_files/plugins/ckeditor/samples/toolbarconfigurator/index.html";
    export default {
        components: {Index}
    }
</script>
