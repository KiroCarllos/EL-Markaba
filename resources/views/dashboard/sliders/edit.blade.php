@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.sliders')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.sliders.index') }}"> @lang('site.sliders')</a></li>
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
                    <form action="{{ route('dashboard.sliders.update', $slider->id) }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('put') }}
                        <div class="form-group">
                            <label>@lang('site.status_slider')</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $slider->status == "active" ? "selected" :"" }}>@lang("site.active")</option>
                                <option value="disActive" {{ $slider->status == "disActive" ? "selected" :"" }}>@lang("site.disActive")</option>
                                <option value="disActive" {{ $slider->status == "deleted" ? "selected" :"" }}>@lang("site.deleted")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.user_views')</label>
                            <select name="role[]" multiple class="form-control js-enable-tags">
                                <option value="company" {{ $slider->role == "company" ? "selected" :"" }}>@lang("site.company")</option>
                                <option value="student" {{ $slider->role == "student" ? "selected" :"" }}>@lang("site.student")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.image')</label>
                            <input type="file" name="image" class="form-control image">
                        </div>
                        <div class="form-group">
                            <a href="{{ $slider->image ??'default.png' }}" class="img-preview">
                            <img src="{{ $slider->image ??'default.png' }}"  style="width: 400px" class="img-thumbnail image-preview" alt="">
                            </a>
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
