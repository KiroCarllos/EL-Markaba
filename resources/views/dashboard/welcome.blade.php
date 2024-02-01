@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.dashboard')</h1>

            <ol class="breadcrumb">
                <li class="active"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</li>
            </ol>
        </section>

        <section class="content">

            <div class="row">

                {{-- Active Students--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ \App\Models\User::where("role","student")->where("status","active")->count() }}</h3>

                            <p>الطلاب المفعلين</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <a href="{{ route("dashboard.student_details.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                {{-- not Active Students--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-light-blue-active">
                        <div class="inner">
                            <h3>{{ \App\Models\User::where("role","student")->where("status","pending")->count() }}</h3>
                            <p>الطلاب الغير المفعلين</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <a href="{{ route("dashboard.student_details.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--Companies active--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>{{ \App\Models\User::where("role","company")->where("status","active")->count() }}</h3>

                            <p>الشركات المفعلين</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ route("dashboard.companies.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                {{--Companies not active--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-green-active">
                        <div class="inner">
                            <h3>{{ \App\Models\User::where("role","company")->where("status","pending")->count() }}</h3>

                            <p>الشركات الغير المفعلين</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ route("dashboard.companies.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--Posts active--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>{{ \App\Models\Post::where("status","active")->count() }}</h3>
                            <p>@lang('site.posts')</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-file"></i>
                        </div>
                        <a href="{{ route("dashboard.posts.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--Jobs active--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3>{{ \App\Models\Job::where("status","active")->count() }}</h3>

                            <p>الوظايف المفعلة</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-suitcase"></i>
                        </div>
                        <a href="{{ route('dashboard.jobs.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-yellow-active">
                        <div class="inner">
                            <h3>{{ \App\Models\Job::where("status","enough")->count() }}</h3>

                            <p>الوظايف المنتهيه</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-suitcase"></i>
                        </div>
                        <a href="{{ route('dashboard.jobs.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                {{--Jobs not active--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-yellow-gradient">
                        <div class="inner">
                            <h3>{{ \App\Models\Job::where("status","pending")->count() }}</h3>

                            <p>الوظايف الغير المفعلة</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-suitcase"></i>
                        </div>
                        <a href="{{ route('dashboard.jobs.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-purple-active">
                        <div class="inner">
                            <h3>{{ \App\Models\User::where("role","job_office")->where("status","active")->count() }}</h3>

                            <p>مكاتب التوظيف  المفعلة</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-desktop"></i>
                        </div>
                        <a href="{{ route('dashboard.job_offices.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                {{--Jobs not active--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-maroon-active">
                        <div class="inner">
                            <h3>{{ \App\Models\User::where("role","job_office")->where("status","pending")->count() }}</h3>
                            <p>مكاتب التوظيف الغير المفعلة</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-desktop"></i>
                        </div>
                        <a href="{{ route('dashboard.job_offices.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--Area active--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-red-gradient">
                        <div class="inner">
                            <h3>{{ \App\Models\Area::count() }}</h3>
                            <p>@lang('site.areas')</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-area-chart"></i>
                        </div>
                        <a href="{{ route("dashboard.areas.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--Fathers active--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-light-blue-gradient">
                        <div class="inner">
                            <h3>{{ \App\Models\User::where("role","father")->where("status","active")->count() }}</h3>
                            <p>الاباء المفعلين</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-user-secret"></i>
                        </div>
                        <a href="{{ route("dashboard.fathers.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--Fathers not active--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-light-blue">
                        <div class="inner">
                            <h3>{{ \App\Models\User::where("role","father")->where("status","pending")->count() }}</h3>
                            <p>الاباء الغير مفعلين</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-user-secret"></i>
                        </div>
                        <a href="{{ route("dashboard.fathers.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--Trainings active--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-gray-light">
                        <div class="inner">
                            <h3>{{ \App\Models\Training::where("status","active")->count() }}</h3>
                            <p>التدريبات المفعلين</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-trademark"></i>
                        </div>
                        <a href="{{ route("dashboard.trainings.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--Trainings not active--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-gray">
                        <div class="inner">
                            <h3>{{ \App\Models\Training::where("status","pending")->count() }}</h3>
                            <p>التدريبات الغير مفعلين</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-trademark"></i>
                        </div>
                        <a href="{{ route("dashboard.trainings.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                {{--Trainings not active--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-gray">
                        <div class="inner">
                            <h3>{{ \App\Models\Training::where("status","disActive")->count() }}</h3>
                            <p>التدريبات المنتهيه</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-trademark"></i>
                        </div>
                        <a href="{{ route("dashboard.trainings.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div><!-- end of row -->

{{--            <div class="box box-solid">--}}

{{--                <div class="box-header">--}}
{{--                    <h3 class="box-title">Sales Graph</h3>--}}
{{--                </div>--}}
{{--                <div class="box-body border-radius-none">--}}
{{--                    <div class="chart" id="line-chart" style="height: 250px;"></div>--}}
{{--                </div>--}}
{{--                <!-- /.box-body -->--}}
{{--            </div>--}}

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection

@push('scripts')

{{--    <script>--}}

{{--        //line chart--}}
{{--        var line = new Morris.Line({--}}
{{--            element: 'line-chart',--}}
{{--            resize: true,--}}
{{--            data: [--}}
{{--                @foreach ($sales_data as $data)--}}
{{--                {--}}
{{--                    ym: "{{ $data->year }}-{{ $data->month }}", sum: "{{ $data->sum }}"--}}
{{--                },--}}
{{--                @endforeach--}}
{{--            ],--}}
{{--            xkey: 'ym',--}}
{{--            ykeys: ['sum'],--}}
{{--            labels: ['@lang('site.total')'],--}}
{{--            lineWidth: 2,--}}
{{--            hideHover: 'auto',--}}
{{--            gridStrokeWidth: 0.4,--}}
{{--            pointSize: 4,--}}
{{--            gridTextFamily: 'Open Sans',--}}
{{--            gridTextSize: 10--}}
{{--        });--}}
{{--    </script>--}}

@endpush
