@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.fathers')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">@lang('site.fathers')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.user_student_details') <small>{{ count($fathers) }}</small></h3>

                    <form action="{{ route('dashboard.fathers.index') }}" method="get">

                        <div class="row">
{{--                            <div class="col-md-4">--}}
{{--                                <input type="text" name="search" class="form-control" placeholder="@lang('site.search')" value="{{ request()->search }}">--}}
{{--                            </div>--}}
                            <div class="col-md-4">
{{--                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>--}}
                                @if (auth()->user()->hasPermission('create_fathers') ||auth()->user()->hasRole('super_admin') )
                                    <a href="{{ route('dashboard.fathers.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif
                                @if (auth()->user()->hasPermission('create_fathers') ||auth()->user()->hasRole('super_admin') )
                                    <a href="{{ route('dashboard.fathers.export') }}" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> @lang('site.export')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.export')</a>
                                @endif
                            </div>

                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">
                    @isset($fathers)
                    @if ($fathers->count() > 0)

                        <table class="table table-bordered table-striped dataTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('site.image')</th>
                                <th>@lang('site.status_account')</th>
                                <th>@lang('site.name')</th>
                                <th>@lang('site.mobile')</th>
                                <th>@lang('site.area')</th>
                                <th>@lang('site.action')</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($fathers as $index=>$father)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><img src="{{ $father->image }}" style="width: 100px;" class="img-thumbnail" alt=""></td>
                                    <td>{{ $father->status }}</td>
                                    <td>{{ $father->name}}</td>
                                    <td>{{ $father->mobile }}</td>
                                    <td>{{ $father->father_details->area->name_ar ?? null }}</td>
                                    <td>
                                        @if (auth()->user()->hasRole('super_admin'))
                                            <a href="{{ route('dashboard.fathers.edit', $father->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @else
                                            <a href="#" class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @endif
                                        @if (auth()->user()->hasRole('super_admin'))
                                            <form action="{{ route('dashboard.fathers.destroy', $father->id) }}" method="post" style="display: inline-block">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" class="btn btn-danger delete btn-sm"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                            </form><!-- end of form -->
                                        @else
                                            <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                        @endif
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>

                        </table><!-- end of table -->

{{--                        {{ $user_student_details->appends(request()->query())->links() }}--}}

                    @else

                        <h2>@lang('site.no_data_found')</h2>

                    @endif
                    @endisset
                </div><!-- end of box body -->
            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection
