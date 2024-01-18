@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.fathers')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li>
                    <a href="{{ route('dashboard.fathers.index') }}"> @lang('site.fathers')</a>
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

                    <form action="{{ route('dashboard.fathers.store') }}" method="post"
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
                            <label>@lang('site.password')</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>@lang('site.area')</label>
                            <select required name="area_id" class="form-control">
                                <option></option>
                                @foreach($areas as $area)
                                    <option  value="{{ $area->id }}" >
                                        {{ $area->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>



{{--                             image--}}
                            <div class="form-group">
                                <label>@lang('site.image')</label>
                                <input type="file" name="image" class="form-control image_commercial_record">
                            </div>

                            <div class="form-group">
                                <img src="{{ asset('uploads/user_images/default.png') }}" style="width: 400px"
                                     class="img-thumbnail image_commercial_record-preview" alt="">
                            </div>

{{--                            national id image--}}
                            <div class="form-group">
                                <label>@lang('site.national_image')</label>
                                <input type="file" name="national_image" class="form-control image_company">
                            </div>
                            <div class="form-group">
                                <img src="{{ asset('uploads/user_images/default.png') }}" style="width: 400px"
                                     class="img-thumbnail image_company-preview" alt="">
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
        // university
        $(document).ready(function() {
            // AJAX request to fetch universities
            $.ajax({
                url: '{{ route("getUniversities") }}', // Replace with the actual endpoint to fetch universities
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    var universities = response.data; // Assuming the response contains an array of universities
                    // Populate select element with universities
                    var selectElement = $('#university_select');
                    selectElement.empty(); // Clear existing options
                    var defaultOption = $('<option>').val('').text("{{__("site.select_student_university")}}");
                    selectElement.append(defaultOption);
                    $.each(universities, function(index, university) {
                        var option = $('<option>').val(university.id).text(university.name_{{app()->getLocale()}});
                        selectElement.append(option);
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
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
                    data: { university_id : universityId },
                    dataType: 'json',
                    success: function(response) {
                        var faculties = response.data; // Assuming the response contains an array of faculties

                        // Populate faculty select element with faculties
                        $.each(faculties, function(index, faculty) {
                            var option = $('<option>').val(faculty.id).text(faculty.name_{{app()->getLocale()}});
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
             $("#else_major").hide();
             $("#else_major_input").val("");
            var majorSelect = $('#major_select');
            majorSelect.on('change', function() {
                var majorId = $(this).val();
                if(majorId == "not_from_above"){
                    $("#else_major").show();
                }else{
                    $("#else_major").hide();
                    $("#else_major_input").val("");
                }
            });
        });
    </script>
@endpush
