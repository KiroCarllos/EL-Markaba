@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.jobs')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.jobs.index') }}"> @lang('site.jobs')</a></li>
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
                    <form action="{{ route('dashboard.jobs.applications.update', $application->id) }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('put') }}
                        <div class="form-group">
                            <label>@lang('site.application_status')</label>
                            <select name="status" class="form-control">
                                <option  disabled {{ $application->status == "confirmed" ? "selected" :"" }}>@lang("site.confirmed")</option>
                                <option disabled  {{ $application->status == "notConfirmed" ? "selected" :"" }}>@lang("site.notConfirmed")</option>
                                <option  disabled {{ $application->status == "canceled" ? "selected" :"" }}>@lang("site.canceled")</option>
                                <option value="pending" {{ $application->status == "pending" ? "selected" :"" }}>@lang("site.pending")</option>
                                <option value="inProgress" {{ $application->status == "inProgress" ? "selected" :"" }}>@lang("site.inProgress")</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>@lang('site.email')</label>
                            <input type="email" readonly class="form-control"
                                   value="{{ $application->user->email }}">
                        </div>


                        <div class="form-group">
                            <label>@lang('site.mobile')</label>
                            <input type="text" readonly class="form-control"
                                   value="{{ $application->user->mobile }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.national_id')</label>
                            <input type="text" readonly maxlength="14" minlength="14" class="form-control"
                                   value="{{ $application->user->student_details->national_id }}">
                        </div>
                        @if(!is_null($application->user->student_details->faculty))
                            <div class="form-group">
                                <label>@lang('site.university')</label>
                                <select id="university_select" readonly class="form-control">
                                    <option></option>
                                    @isset($universities)
                                        @if(is_null($application->user->student_details->faculty))
                                            <option></option>
                                            @foreach($universities as $university)
                                                <option
                                                    value="{{$university->id  }}">{{ $university->name }}</option>
                                            @endforeach
                                        @else
                                            @foreach($universities as $university)
                                                <option
                                                    {{ $university->id == $application->user->student_details->faculty->university_id ?"Selected":"" }} value="{{$university->id  }}">{{ $university->name }}</option>
                                            @endforeach
                                        @endif
                                    @endisset
                                </select>
                            </div>
                            <div class="form-group">
                                <label>@lang('site.faculty')</label>
                                <select id="faculty_select" readonly class="form-control">
                                    <option></option>

                                    @isset($faculties)
                                        @if(is_null($application->user->student_details->faculty))
                                            <option></option>
                                            @foreach($faculties as $faculty)
                                                <option
                                                    value="{{$faculty->id  }}">{{ $faculty->name }}</option>
                                            @endforeach
                                        @else
                                            @foreach($faculties as $faculty)
                                                <option
                                                    {{ $faculty->id == $application->user->student_details->faculty->id ?"Selected":"" }} value="{{$faculty->id  }}">{{ $faculty->name }}</option>
                                            @endforeach
                                        @endif
                                    @endisset
                                </select>
                            </div>
                            <div id="major" class="form-group">
                                <label>@lang('site.major')</label>
                                <input id="major" type="text" readonly class="form-control"
                                       value="{{ $application->user->student_details->major }}">
                            </div>
                        @else
                            <div id="major" class="form-group">
                                <label>@lang('site.major')</label>
                                <input id="major" type="text" readonly class="form-control"
                                       value="{{ $application->user->student_details->else_education }}">
                            </div>

                        @endif

                        <div class="form-group">
                            <label>@lang('site.graduated_at')</label>
                            <input type="text" readonly class="form-control"
                                   value="{{ $application->user->student_details->graduated_at }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.address')</label>
                            <input type="text" readonly class="form-control"
                                   value="{{ $application->user->student_details->address }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.gender')</label>
                            <select readonly class="form-control">
                                <option
                                    {{ $application->user->student_details->gender == "male" ?"selected" :'' }} value="male">@lang('site.male')</option>
                                <option
                                    {{ $application->user->student_details->gender == "female" ?"selected" :'' }}  value="female">@lang('site.female')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.prior_experiences')</label>
                            <select name="prior_experiences[]" class="form-control js-enable-tags" multiple="multiple">
                                @if(!is_null($application->user->student_details->prior_experiences))
                                    @foreach($application->user->student_details->prior_experiences as $prior_experience)
                                        <option selected value="{{$prior_experience}}">{{$prior_experience}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.courses')</label>
                            <select class="form-control js-enable-tags" readonly multiple="multiple">
                                @if(!is_null($application->user->student_details->courses))
                                    @foreach($application->user->student_details->courses as $course)
                                        <option selected value="{{$course}}">{{$course}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <img src="{{ $application->user->image }}" style="width: 400px"
                                 class="img-thumbnail image-preview" alt="">
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
