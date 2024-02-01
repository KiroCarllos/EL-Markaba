@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.posts')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.posts.index') }}"> @lang('site.posts')</a></li>
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
                    <form action="{{ route('dashboard.posts.update', $post->id) }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('put') }}
                        <div class="form-group">
                            <label>@lang('site.status_post')</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $post->status == "active" ? "selected" :"" }}>@lang("site.active")</option>
                                <option value="disActive" {{ $post->status == "disActive" ? "selected" :"" }}>@lang("site.disActive")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.title_en')</label>
                            <input type="text" name="title_en" class="form-control" value="{{ $post->title_en }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.title_ar')</label>
                            <input type="text" name="title_ar" class="form-control" value="{{ $post->title_ar }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.description_ar')</label>
                            <textarea class="form-control" name="description_ar" id="address" cols="1" rows="3">{{ $post->description_ar }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.description_en')</label>
                            <textarea class="form-control" name="description_en" id="address" cols="1" rows="3">{{ $post->description_en }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>@lang('site.image')</label>
                            <input type="file" name="image" class="form-control image">
                        </div>
                        <div class="form-group">
                            <a href="{{ asset($post->image ??'default.png') }}" class="img-preview">
                            <img src="{{ asset($post->image ??'default.png') }}"  style="width: 400px" class="img-thumbnail image-preview" alt="">
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
