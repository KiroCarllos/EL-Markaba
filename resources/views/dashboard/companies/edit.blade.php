@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.companies')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.companies.index') }}"> @lang('site.companies')</a></li>
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
                    <form action="{{ route('dashboard.companies.update', $jobCompany->id) }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('put') }}
                        <input type="hidden" name="user_id" value="{{$jobCompany->id}}">
                        <div class="form-group">
                            <label>@lang('site.status_account')</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $jobCompany->status == "active" ? "selected" :"" }}>@lang("site.active")</option>
                                <option value="inProgress" {{ $jobCompany->status == "inProgress" ? "selected" :"" }}>@lang("site.inProgress")</option>
                                <option value="pending" {{ $jobCompany->status == "pending" ? "selected" :"" }}>@lang("site.pending")</option>
                                <option value="blocked" {{ $jobCompany->status == "blocked" ? "selected" :"" }}>@lang("site.blocked")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.name')</label>
                            <input type="text" name="name" class="form-control" value="{{ $jobCompany->name }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.mobile')</label>
                            <input type="text" name="mobile" class="form-control" value="{{ $jobCompany->mobile }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.email')</label>
                            <input type="email" name="email" class="form-control" value="{{ $jobCompany->email }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.password')</label>
                            <input type="password" name="password" placeholder="@lang("site.fill password if need to reset only")" class="form-control" >
                        </div>
                        <div class="form-group">
                            <label>@lang('site.administrator_name')</label>
                            <input type="text" name="administrator_name" class="form-control" value="{{ $jobCompany->company_details->administrator_name }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.administrator_mobile')</label>
                            <input type="text" name="administrator_mobile" class="form-control" value="{{ $jobCompany->company_details->administrator_mobile }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.bio')</label>
                            <input type="text" name="bio" class="form-control" value="{{ $jobCompany->company_details->bio }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.created_date')</label>
                            <input type="date" name="created_date" class="form-control" value="{{ $jobCompany->company_details->created_date }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.address')</label>
                            <textarea class="form-control" name="address" id="address" cols="1" rows="2">{{ $jobCompany->company_details->address }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>@lang('site.image_company')</label>
                            <input type="file" name="logo" class="form-control image">
                        </div>
                        <div class="form-group">
                            <img src="{{ asset($jobCompany->image ??'default.png') }}"  style="width: 100px" class="img-thumbnail image-preview" alt="">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.image_commercial_record')</label>
                            <input type="file" name="commercial_record_image" class="form-control image_commercial_record">
                        </div>
                        <div class="form-group">
                            <img src="{{ asset($jobCompany->company_details->commercial_record_image ??'default.png') }}"  style="width: 100px" class="img-thumbnail image_commercial_record-preview" alt="">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.image_tax_card')</label>
                            <input type="file" name="tax_card_image" class="form-control image_tax_card">
                        </div>
                        <div class="form-group">
                            <img src="{{ asset($jobCompany->company_details->tax_card_image ??'default.png') }}"  style="width: 100px" class="img-thumbnail image_tax_card-preview" alt="">
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
