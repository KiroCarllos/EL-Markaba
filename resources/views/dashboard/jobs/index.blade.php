@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.jobs')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li class="active">@lang('site.jobs')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.jobs')
                        <small>{{ count($jobs) }}</small></h3>

                    <form action="{{ route('dashboard.jobs.index') }}" method="get">

                        <div class="row">



                            <div class="col-md-4">
{{--                                <button type="submit" class="btn btn-primary"><i--}}
{{--                                        class="fa fa-search"></i> @lang('site.search')</button>--}}
                                @if (auth()->user()->hasRole('super_admin') )
                                    <a href="{{ route('dashboard.jobs.create') }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i
                                            class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif

                                @if (auth()->user()->hasRole('super_admin') )
                                    <a href="{{ route('dashboard.jobs.exports') }}" class="btn btn-primary"><i
                                            class="fa fa-file-excel-o"></i> @lang('site.export')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i
                                            class="fa fa-plus"></i> @lang('site.export')</a>
                                @endif
                            </div>

                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">
                    @isset($jobs)
                        @if ($jobs->count() > 0)

                            <table class="table table-bordered table-striped dataTable">

                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('site.image')</th>
                                    <th>@lang('site.company_name')</th>
                                    <th>@lang('site.job_title')</th>
                                    <th>@lang('site.status_job')</th>
                                    <th>@lang('site.job_type')</th>
                                    <th>@lang('site.job_applications_count')</th>
                                    <th>@lang('site.action')</th>
                                </tr>
                                </thead>

                                <tbody>

                                @foreach ($jobs as $index=>$job)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>

                                        <td>
                                            <a href="{{ $job->company->image }}" class="img-preview">
                                                <img src="{{ $job->company->image }}"  style="width: 100px" class="img-thumbnail image-preview" alt="">
                                            </a>
                                        </td>
                                        <td>{{ $job->company->name }}</td>
                                        <td>{{ $job->title }}</td>
                                        <td>{{ $job->status }}</td>
                                        <td>{{ $job->work_type }}</td>
                                        <td>{{ $job->applications->count() }}</td>
                                        <td>
                                            @if (auth()->user()->hasRole('super_admin') )
                                                <a href="{{ route('dashboard.jobs.applications.export',$job->id) }}"
                                                   class="btn btn-primary btn-sm"><i
                                                        class="fa fa-file-excel-o"></i> @lang('site.export')</a>
                                            @else
                                                <a href="#" class="btn btn-primary btn-sm disabled"><i
                                                        class="fa fa-file-excel-o"></i> @lang('site.applications')</a>
                                            @endif
                                                @if (auth()->user()->hasRole('super_admin') )
                                                <a href="{{ route('dashboard.jobs.applications', $job->id) }}"
                                                   class="btn btn-info btn-sm"><i
                                                        class="fa fa-file"></i> @lang('site.applications')</a>
                                            @else
                                                <a href="#" class="btn btn-info btn-sm disabled"><i
                                                        class="fa fa-edit"></i> @lang('site.applications')</a>
                                            @endif
                                            @if (auth()->user()->hasRole('super_admin') )
                                                <a href="{{ route('dashboard.jobs.edit', $job->id) }}"
                                                   class="btn btn-success btn-sm"><i
                                                        class="fa fa-edit"></i> @lang('site.edit')</a>
                                            @else
                                                <a href="#" class="btn btn-success btn-sm disabled"><i
                                                        class="fa fa-edit"></i> @lang('site.edit')</a>
                                            @endif
                                            @if (auth()->user()->hasRole('super_admin') )
                                                <form action="{{ route('dashboard.jobs.destroy', $job->id) }}"
                                                      method="post" style="display: inline-block">
                                                    {{ csrf_field() }}
                                                    {{ method_field('delete') }}
                                                    <button type="submit" class="btn btn-danger delete btn-sm"><i
                                                            class="fa fa-trash"></i> @lang('site.delete')</button>
                                                </form><!-- end of form -->
                                            @else
                                                <button class="btn btn-danger btn-sm disabled"><i
                                                        class="fa fa-trash"></i> @lang('site.delete')</button>
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach
                                </tbody>

                            </table><!-- end of table -->

{{--                            {{ $jobs->appends(request()->query())->links() }}--}}

                        @else
                            <h2>@lang('site.no_data_found')</h2>
                        @endif
                    @endisset
                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection

