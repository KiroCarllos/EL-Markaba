@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.trainings')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li class="{{ route("dashboard.jobs.index") }}">@lang('site.jobs')</li>
                <li class="active">@lang('site.areas')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.applications')
                        <small>{{ $areas->count() }}</small></h3>



                </div><!-- end of box header -->
                <div class="col-md-4">
                    {{--                                <button type="submit" class="btn btn-primary"><i--}}
                    {{--                                        class="fa fa-search"></i> @lang('site.search')</button>--}}
                    @if (auth()->user()->hasRole('super_admin') )
                        <a href="{{ route('dashboard.areas.create') }}" class="btn btn-primary"><i
                                class="fa fa-plus"></i> @lang('site.add')</a>
                    @else
                        <a href="#" class="btn btn-primary disabled"><i
                                class="fa fa-plus"></i> @lang('site.add')</a>
                    @endif

                </div>
                <div class="box-body">
                    @isset($areas)
                        @if ($areas->count() > 0)
                            <table class="table table-hover dataTable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('site.name_ar')</th>
                                    <th>@lang('site.name_en')</th>
                                    <th>@lang('site.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($areas as $index=>$area)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{  $area->name_ar }}</td>
                                        <td>{{  $area->name_en }}</td>

                                        <td>

                                            @if (auth()->user()->hasRole('super_admin') )
                                                <a href="{{ route('dashboard.areas.edit', $area->id) }}"
                                                   class="btn btn-success btn-sm"><i
                                                        class="fa fa-edit"></i> @lang('site.edit')</a>
                                            @else
                                                <a href="#" class="btn btn-success btn-sm disabled"><i
                                                        class="fa fa-edit"></i> @lang('site.edit')</a>
                                            @endif

                                            @if (auth()->user()->hasRole('super_admin') )
                                                <form
                                                    action="{{ route('dashboard.areas.destroy', $area->id) }}"
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


                        @else

                            <h2>@lang('site.no_data_found')</h2>

                        @endif
                    @endisset

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
