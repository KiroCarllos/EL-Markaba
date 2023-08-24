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
                    <form action="{{ route('dashboard.trainings.applications.update', $application->id) }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('put') }}
                        <div class="form-group">
                            <label>@lang('site.application_status')</label>
                            <select name="status" class="form-control">
                                <option value="confirmed" {{ $application->status == "confirmed" ? "selected" :"" }}>@lang("site.confirmed")</option>
                                <option value="notConfirmed" {{ $application->status == "notConfirmed" ? "selected" :"" }}>@lang("site.notConfirmed")</option>
                                <option value="canceled" {{ $application->status == "canceled" ? "selected" :"" }}>@lang("site.canceled")</option>
                                <option value="pending" {{ $application->status == "pending" ? "selected" :"" }}>@lang("site.pending")</option>
                                <option value="enough" {{ $application->status == "enough" ? "selected" :"" }}>@lang("site.enough")</option>
                                <option value="inProgress" {{ $application->status == "inProgress" ? "selected" :"" }}>@lang("site.inProgress")</option>
                            </select>
                        </div>
                        @if(!is_null($application->receipt_image))
                            <div class="form-group">
                                <label>@lang('site.receipt_image')</label>
                                <input type="file" name="receipt_image" class="form-control image">
                            </div>
                            <div class="form-group">
                                <img src="{{ asset($application->receipt_image ??'default.png') }}"  style="width: 400px" class="img-thumbnail image-preview" alt="">
                            </div>
                        @endif
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
