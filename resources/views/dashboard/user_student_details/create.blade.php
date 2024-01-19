@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.user_student_details')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li>
                    <a href="{{ route('dashboard.student_details.index') }}"> @lang('site.user_student_details')</a>
                </li>
                <li class="active">@lang('site.add')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">@lang('site.add')</h3>
                </div><!-- end of box header -->

                <div class="box-body">

                    @include('partials._errors')

                    <form action="{{ route('dashboard.student_details.store') }}" method="post"
                          enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('post') }}

                        <div class="form-group">
                            <label>@lang('site.name')</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.email')</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.mobile')</label>
                            <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.national_id')</label>
                            <input type="text" name="national_id" maxlength="14" minlength="14" class="form-control"
                                   value="{{ old('national_id') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.education_level')</label>
                            <select id="education_level" name="education" class="form-control ">
                                <option ></option>
                                <option {{ old("education_level" == "high") ? "selected" :"" }} value="high">عالي</option>
                                <option  {{ old("education_level" == "else") ? "selected" :"" }}value="else">اخري</option>
                            </select>
                        </div>
                        <div id="university" class="form-group">
                            <label>@lang('site.university')</label>
                            <select id="university_select" class="form-control">
                                <option></option>
                                @foreach($universities as $university)
                                    <option value="{{ $university->id }}" > {{ $university->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="faculty" class="form-group">
                            <label>@lang('site.faculty')</label>
                            <select id="faculty_select" name="faculty_id" class="form-control">
                                <option></option>
                            </select>
                        </div>

                        <div id="else_education" class="form-group">
                            <label>@lang('site.else_education')</label>
                            <input id="else_education_input" type="text" name="else_education" class="form-control" value="{{ old('else_education') }}">
                        </div>
                        <div  class="form-group">
                            <label>@lang('site.major')</label>
                            <input type="text" name="major" class="form-control" value="{{ old('major') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.graduated_at')</label>
                            <input type="text" placeholder="@lang("site.write year only as 2020 or 2021 ....")" name="graduated_at" class="form-control"
                                   value="{{ old('graduated_at') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.address')</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('site.gender')</label>
                            <select name="gender" class="form-control">
                                <option value="male">@lang('site.male')</option>
                                <option value="female">@lang('site.female')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.area')</label>
                            <select required name="area_id" class="form-control select2">
                                <option></option>
                                @foreach($areas as $area)
                                    <option  value="{{ $area->id }}" >
                                        {{ $area->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.prior_experiences')</label>
                            <select name="prior_experiences[]" class="form-control js-enable-tags" multiple="multiple">
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('site.courses')</label>
                            <select class="form-control js-enable-tags" name="courses[]" multiple="multiple">
                            </select>
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
                            <label>@lang('site.image')</label>
                            <input type="file" name="image" class="form-control image">
                        </div>
                        <div class="form-group">
                            <img src="{{ asset('uploads/user_images/default.png') }}" style="width: 100px"
                                 class="img-thumbnail image-preview" alt="">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')
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


        //faculty
        $(document).ready(function() {
            var universitySelect = $('#university_select');
            var facultySelect = $('#faculty_select');
            universitySelect.on('change', function() {
                var universityId = $(this).val();
                facultySelect.empty();
                var defaultOption = $('<option>').val('').text("{{__("site.select_student_faculty")}}");
                facultySelect.append(defaultOption);
                $.ajax({
                    url: '{{ route("getFacultyByUniversity") }}', // Replace with the actual endpoint to fetch faculties
                    method: 'POST',
                    data: { university_id : universityId ,"lang":"ar" },
                    dataType: 'json',
                    success: function(response) {
                        var faculties = response.data; // Assuming the response contains an array of faculties
                        // Populate faculty select element with faculties
                        $.each(faculties, function(index, faculty) {
                            var option = $('<option>').val(faculty.id).text(faculty.name);
                            facultySelect.append(option);
                        });
                    },
                    error: function(xhr, status, error) {
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
        $(document).ready(function(){
             $("#else_education").hide();
             $("#else_education_input").val("");
            $("#university").hide();
            $("#faculty").hide();
            var majorSelect = $('#education_level');
            majorSelect.on('change', function() {
                var majorId = $(this).val();
                if(majorId == "else"){
                    $("#else_education").show();
                    $("#university").hide();
                    $("#faculty").hide();
                }else{
                    $("#else_education").hide();
                    $("#university").show();
                    $("#faculty").show();
                }
            });
        });
    </script>
@endpush
