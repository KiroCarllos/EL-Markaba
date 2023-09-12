@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.user_student_details')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li><a href="{{ route('dashboard.student_details.index') }}"> @lang('site.user_student_details')</a>
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

                    <form action="{{ route("dashboard.student_details.update",$userStudentDetail->id) }}" method="post"
                          enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('put') }}
                        <input type="hidden" name="user_id" value="{{$userStudentDetail->id}}">

                        <div class="form-group">
                            <label>@lang('site.name')</label>
                            <input type="text" name="name" class="form-control" value="{{ $userStudentDetail->name }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.status_account')</label>
                            <select name="status" class="form-control">
                                <option
                                    value="active" {{ $userStudentDetail->status == "active" ? "selected" :"" }}>@lang("site.active")</option>
                                <option
                                    value="inProgress" {{ $userStudentDetail->status == "inProgress" ? "selected" :"" }}>@lang("site.inProgress")</option>
                                <option
                                    value="pending" {{ $userStudentDetail->status == "pending" ? "selected" :"" }}>@lang("site.pending")</option>
                                <option
                                    value="blocked" {{ $userStudentDetail->status == "blocked" ? "selected" :"" }}>@lang("site.blocked")</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.email')</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ $userStudentDetail->email }}">
                        </div>


                        <div class="form-group">
                            <label>@lang('site.mobile')</label>
                            <input type="text" name="mobile" class="form-control"
                                   value="{{ $userStudentDetail->mobile }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.national_id')</label>
                            <input type="text" name="national_id" maxlength="14" minlength="14" class="form-control"
                                   value="{{ $userStudentDetail->student_details->national_id }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.age')</label>
                            <input readonly type="text"  maxlength="14" minlength="14" class="form-control"
                                   value="{{ $userStudentDetail->age }}">
                        </div>
                        @if(!is_null($userStudentDetail->student_details->faculty))
                        <div class="form-group">
                            <label>@lang('site.university')</label>
                            <select id="university_select" class="form-control">
                                <option></option>
                                @isset($universities)
                                    @if(is_null($userStudentDetail->student_details->faculty))
                                        <option></option>
                                        @foreach($universities as $university)
                                            <option
                                              value="{{$university->id  }}">{{ $university->name }}</option>
                                        @endforeach
                                    @else
                                    @foreach($universities as $university)
                                        <option
                                            {{ $university->id == $userStudentDetail->student_details->faculty->university_id ?"Selected":"" }} value="{{$university->id  }}">{{ $university->name }}</option>
                                    @endforeach
                                    @endif
                                @endisset
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.faculty')</label>
                            <select id="faculty_select" name="faculty_id" class="form-control">
                                <option></option>

                                @isset($faculties)
                                    @if(is_null($userStudentDetail->student_details->faculty))
                                        <option></option>
                                        @foreach($faculties as $faculty)
                                            <option
                                                 value="{{$faculty->id  }}">{{ $faculty->name }}</option>
                                        @endforeach
                                    @else
                                    @foreach($faculties as $faculty)
                                        <option
                                            {{ $faculty->id == $userStudentDetail->student_details->faculty->id ?"Selected":"" }} value="{{$faculty->id  }}">{{ $faculty->name }}</option>
                                    @endforeach
                                    @endif
                                @endisset
                            </select>
                        </div>
                        <div id="major" class="form-group">
                            <label>@lang('site.major')</label>
                            <input id="major" type="text" name="major" class="form-control"
                                   value="{{ $userStudentDetail->student_details->major }}">
                        </div>
                        @else
                            <div id="major" class="form-group">
                                <label>@lang('site.major')</label>
                                <input id="major" type="text" name="else_education" class="form-control"
                                       value="{{ $userStudentDetail->student_details->else_education }}">
                            </div>

                        @endif

                        <div class="form-group">
                            <label>@lang('site.graduated_at')</label>
                            <input type="text" name="graduated_at" class="form-control"
                                   value="{{ $userStudentDetail->student_details->graduated_at }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.address')</label>
                            <input type="text" name="address" class="form-control"
                                   value="{{ $userStudentDetail->student_details->address }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.gender')</label>
                            <select name="gender" class="form-control">
                                <option
                                    {{ $userStudentDetail->student_details->gender == "male" ?"selected" :'' }} value="male">@lang('site.male')</option>
                                <option
                                    {{ $userStudentDetail->student_details->gender == "female" ?"selected" :'' }}  value="female">@lang('site.female')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.prior_experiences')</label>
                            <select name="prior_experiences[]" class="form-control js-enable-tags" multiple="multiple">
                                @if(!is_null($userStudentDetail->student_details->prior_experiences))
                                    @foreach($userStudentDetail->student_details->prior_experiences as $prior_experience)
                                        <option selected value="{{$prior_experience}}">{{$prior_experience}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.courses')</label>
                            <select class="form-control js-enable-tags" name="courses[]" multiple="multiple">
                                @if(!is_null($userStudentDetail->student_details->courses))
                                @foreach($userStudentDetail->student_details->courses as $course)
                                    <option selected value="{{$course}}">{{$course}}</option>
                                @endforeach
                                    @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.image')</label>
                            <input type="file" name="image" class="form-control image">
                        </div>
                        <div class="form-group">
                            <img src="{{ $userStudentDetail->image }}" style="width: 100px"
                                 class="img-thumbnail image-preview" alt="">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.password')</label>
                            <input type="password" name="password"
                                   placeholder="@lang("site.fill password if need to reset only")" class="form-control">
                        </div>
                        @if(!is_null($userStudentDetail->device_token))
                        <div class="form-group">
                            <label>@lang('site.notify')</label>
                            <input type="text" name="notify"
                                   placeholder="@lang("site.fill notify if need to send notification only")" class="form-control">
                        </div>
                        @endif

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


@push("scripts")
    <script>
        // university
        {{--$(document).ready(function() {--}}
        {{--    var universitySelect = $('#university_select');--}}
        {{--    // AJAX request to fetch universities--}}
        {{--    $.ajax({--}}
        {{--        url: '{{ route("getUniversities") }}', // Replace with the actual endpoint to fetch universities--}}
        {{--        method: 'POST',--}}
        {{--        headers:{--}}
        {{--          lang:"{{app()->getLocale()}}"--}}
        {{--        },--}}
        {{--        dataType: 'json',--}}
        {{--        success: function(response) {--}}
        {{--            var universities = response.data; // Assuming the response contains an array of universities--}}
        {{--            // Populate select element with universities--}}
        {{--            var selectElement = $('#university_select');--}}
        {{--            selectElement.empty(); // Clear existing options--}}
        {{--            var defaultOption = $('<option>').val('').text("{{__("site.select_student_university")}}");--}}
        {{--            selectElement.append(defaultOption);--}}
        {{--            $.each(universities, function(index, university) {--}}
        {{--                var option = $('<option>').val(university.id).text(university.name);--}}
        {{--                selectElement.append(option);--}}
        {{--            });--}}
        {{--        },--}}
        {{--        error: function(xhr, status, error) {--}}
        {{--            console.error(error);--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}
        //faculty
        $(document).ready(function () {
            var universitySelect = $('#university_select');
            var facultySelect = $('#faculty_select');

            universitySelect.on('change', function () {
                var universityId = $(this).val();
                facultySelect.empty();
                var defaultOption = $('<option>').val('').text("{{__("site.select_student_faculty")}}");
                facultySelect.append(defaultOption);
                $.ajax({
                    url: '{{ route("getFacultyByUniversity") }}', // Replace with the actual endpoint to fetch faculties
                    method: 'POST',
                    data: {university_id: universityId},
                    dataType: 'json',
                    headers: {
                        lang: "{{app()->getLocale()}}"
                    },
                    success: function (response) {
                        var faculties = response.data; // Assuming the response contains an array of faculties
                        // Populate faculty select element with faculties
                        $.each(faculties, function (index, faculty) {
                            var option = $('<option>').val(faculty.id).text(faculty.name);
                            facultySelect.append(option);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });
        // major
        {{--$(document).ready(function() {--}}
        {{--    var facultySelect = $('#faculty_select');--}}
        {{--    var majorSelect = $('#major_select');--}}
        {{--    facultySelect.on('change', function() {--}}
        {{--        var facultyId = $(this).val();--}}
        {{--        majorSelect.empty();--}}
        {{--        $.ajax({--}}
        {{--            url: '{{ route("getMajorByFaculty") }}',--}}
        {{--            method: 'POST',--}}
        {{--            data: { faculty_id: facultyId },--}}
        {{--            dataType: 'json',--}}
        {{--            success: function(response) {--}}
        {{--                var majors = response.data;--}}
        {{--                var defaultOption = $('<option>').val('').text("{{__("site.select_student_major")}}");--}}
        {{--                majorSelect.append(defaultOption);--}}
        {{--                $.each(majors, function(index, major) {--}}
        {{--                    var option = $('<option>').val(major.id).text(major.name_{{app()->getLocale()}});--}}
        {{--                    majorSelect.append(option);--}}
        {{--                });--}}
        {{--                var defaultOption = $('<option>').val('not_from_above').text("{{__("site.not_from_above")}}");--}}
        {{--                majorSelect.append(defaultOption);--}}
        {{--            },--}}
        {{--            error: function(xhr, status, error) {--}}
        {{--                console.error(error);--}}
        {{--            }--}}
        {{--        });--}}
        {{--    });--}}
        {{--});--}}
        // $(document).ready(function(){
        //     $("#else_major").hide();
        //     $("#else_major_input").val("");
        //     var majorSelect = $('#major_select');
        //     majorSelect.on('change', function() {
        //         var majorId = $(this).val();
        //         if(majorId == "not_from_above"){
        //             $("#else_major").show();
        //         }else{
        //             $("#else_major").hide();
        //             $("#else_major_input").val("");
        //         }
        //     });
        // });
    </script>
@endpush

