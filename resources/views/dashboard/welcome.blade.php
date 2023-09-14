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

                {{-- categories--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ \App\Models\User::where("role","student")->where("status","active")->count() }}</h3>

                            <p>@lang('site.students')</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <a href="{{ route("dashboard.student_details.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--products--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>{{ \App\Models\User::where("role","company")->where("status","active")->count() }}</h3>

                            <p>@lang('site.companies')</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ route("dashboard.companies.index") }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--clients--}}
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

                {{--users--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3>{{ \App\Models\Job::count() }}</h3>

                            <p>@lang('site.jobs')</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-suitcase"></i>
                        </div>
                        <a href="{{ route('dashboard.jobs.index') }}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
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
