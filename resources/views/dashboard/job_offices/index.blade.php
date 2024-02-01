@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.job_offices')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li class="active">@lang('site.job_offices')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.job_offices')
                        <small>{{ count($job_offices) }}</small></h3>

                    <form action="{{ route('dashboard.job_offices.index') }}" method="get">

                        <div class="row">

                            {{--                            <div class="col-md-4">--}}
                            {{--                                <input type="text" name="search" class="form-control" placeholder="@lang('site.search')"--}}
                            {{--                                       value="{{ request()->search }}">--}}
                            {{--                            </div>--}}

                            <div class="col-md-4">

                                @if (auth()->user()->hasRole('super_admin'))
                                    <a href="{{ route('dashboard.job_offices.create') }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i
                                            class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif
                                {{--                                @if (auth()->user()->hasRole('super_admin'))--}}
                                {{--                                    <a href="{{ route('dashboard.job_offices.export') }}" class="btn btn-primary"><i--}}
                                {{--                                            class="fa fa-file-excel-o"></i> @lang('site.export')</a>--}}
                                {{--                                @else--}}
                                {{--                                    <a href="#" class="btn btn-primary disabled"><i--}}
                                {{--                                            class="fa fa-file-excel-o"></i> @lang('site.export')</a>--}}
                                {{--                                @endif--}}
                            </div>

                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">
                    @isset($job_offices)
                        @if ($job_offices->count() > 0)
                            <table class="table table-bordered table-striped dataTable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('site.image')</th>
                                    <th>@lang('site.status_account')</th>
                                    <th>@lang('site.name')</th>
                                    <th>@lang('site.email')</th>
                                    <th>@lang('site.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($job_offices as $index=>$job_office)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <a href="{{ $job_office->image }}" class="img-preview">
                                                <img src="{{ $job_office->image }}" style="width: 100px;"
                                                     class="img-thumbnail" alt="">
                                            </a>
                                        </td>
                                        <td>{{ $job_office->status }}</td>
                                        <td>{{ $job_office->name }}</td>
                                        <td>{{ $job_office->email }}</td>
                                        <td>
                                            @if (auth()->user()->hasRole('super_admin') )
                                                <a href="{{ route('dashboard.job_offices.edit', $job_office->id) }}"
                                                   class="btn btn-info btn-sm"><i
                                                        class="fa fa-edit"></i> @lang('site.edit')</a>
                                            @else
                                                <a href="#" class="btn btn-info btn-sm disabled"><i
                                                        class="fa fa-edit"></i> @lang('site.edit')</a>
                                            @endif
                                            @if (auth()->user()->hasRole('super_admin') )
                                                <form
                                                    action="{{ route('dashboard.job_offices.updateStatus') }}"
                                                    method="post" style="display: inline-block">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="user_id" value="{{ $job_office->id }}">
                                                    <input type="hidden" name="status"
                                                           value="{{ $job_office->status == "active" ? "pending" : "active" }}">
                                                    <button type="submit"
                                                            class="btn btn-primary approveIndexCompanies btn-sm"
                                                            data-status="{{ $job_office->status }}"><i
                                                            class="fa fa-check"></i> {{ $job_office->status == "active" ? __("site.pending") : __("site.approve") }}
                                                    </button>
                                                </form><!-- end of form -->
                                            @else
                                                <a href="#" class="btn btn-primary btn-sm disabled"><i
                                                        class="fa fa-check"></i> @lang('site.approve')</a>
                                            @endif

                                            @if (auth()->user()->hasRole('super_admin') )
                                                <form
                                                    action="{{ route('dashboard.job_offices.destroy', $job_office->id) }}"
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

                            {{--                            {{ $job_offices->appends(request()->query())->links() }}--}}

                        @else

                            <h2>@lang('site.no_data_found')</h2>

                        @endif
                    @endisset

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
