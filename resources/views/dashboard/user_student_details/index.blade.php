@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.user_student_details')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">@lang('site.user_student_details')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.user_student_details') <small>{{ count($user_student_details) }}</small></h3>

                    <form action="{{ route('dashboard.student_details.index') }}" method="get">

                        <div class="row">
{{--                            <div class="col-md-4">--}}
{{--                                <input type="text" name="search" class="form-control" placeholder="@lang('site.search')" value="{{ request()->search }}">--}}
{{--                            </div>--}}
                            <div class="col-md-4">
{{--                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>--}}
                                @if (auth()->user()->hasPermission('create_student_details') ||auth()->user()->hasRole('super_admin') )
                                    <a href="{{ route('dashboard.student_details.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif
                                @if (auth()->user()->hasPermission('create_student_details') ||auth()->user()->hasRole('super_admin') )
                                    <a href="{{ route('dashboard.student_details.export') }}" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> @lang('site.export')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.export')</a>
                                @endif
                            </div>

                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">
                    @isset($user_student_details)
                    @if ($user_student_details->count() > 0)

                        <table class="table table-bordered table-striped dataTable">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('site.image')</th>
                                <th>@lang('site.status_account')</th>
                                <th>@lang('site.name')</th>
                                <th>@lang('site.email')</th>
                                <th>@lang('site.area')</th>
                                <th>@lang('site.action')</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($user_student_details as $index=>$user_student_detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><img src="{{ $user_student_detail->image }}" style="width: 100px;" class="img-thumbnail" alt=""></td>
                                    <td>{{ $user_student_detail->status }}</td>
                                    <td>{{ $user_student_detail->name}}</td>
                                    <td>{{ $user_student_detail->email }}</td>
                                    <td>{{ $user_student_detail->student_details->area->name_ar ?? null }}</td>
                                    <td>
                                        @if (auth()->user()->hasRole('super_admin'))
                                            <a href="{{ route('dashboard.student_details.edit', $user_student_detail->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @else
                                            <a href="#" class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @endif
                                        @if (auth()->user()->hasRole('super_admin'))
                                            <form action="{{ route('dashboard.student_details.destroy', $user_student_detail->id) }}" method="post" style="display: inline-block">
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
