@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.trainings')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.trainings.index') }}"> @lang('site.trainings')</a></li>
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
                    <form action="{{ route('dashboard.trainings.update', $training->id) }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('put') }}
                        <div class="form-group">
                            <label>@lang('site.status_post')</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $training->status == "active" ? "selected" :"" }}>@lang("site.active")</option>
                                <option value="disActive" {{ $training->status == "disActive" ? "selected" :"" }}>@lang("site.disActive")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.paid')</label>
                            <select name="paid" class="form-control">
                                <option value="yes" {{ $training->paid == "yes" ? "selected" :"" }}>@lang("site.yes")</option>
                                <option value="no" {{ $training->paid == "no" ? "selected" :"" }}>@lang("site.no")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.title_en')</label>
                            <input type="text" name="title_en" class="form-control" value="{{ $training->title_en }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.title_ar')</label>
                            <input type="text" name="title_ar" class="form-control" value="{{ $training->title_ar }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.description_ar')</label>
                            <textarea class="form-control" name="description_ar" id="address" cols="1" rows="3">{{ $training->description_ar }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.description_en')</label>
                            <textarea class="form-control" name="description_en" id="address" cols="1" rows="3">{{ $training->description_en }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>@lang('site.image')</label>
                            <input type="file" name="image" class="form-control image">
                        </div>
                        <div class="form-group">
                            <a href="{{ asset($training->image ??'default.png') }}" class="img-preview">
                            <img src="{{ asset($training->image ??'default.png') }}"  style="width: 100px" class="img-thumbnail image-preview" alt="">
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
