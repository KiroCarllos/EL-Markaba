@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.trainings')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li class="active">@lang('site.trainings')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.trainings')
                        <small>{{ $trainings->total() }}</small></h3>

                    <form action="{{ route('dashboard.companies.index') }}" method="get">

                        <div class="row">

                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="@lang('site.search')"
                                       value="{{ request()->search }}">
                            </div>

                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i
                                        class="fa fa-search"></i> @lang('site.search')</button>
                                @if (auth()->user()->hasRole('super_admin') )
                                    <a href="{{ route('dashboard.trainings.create') }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i
                                            class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif
                            </div>

                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">
                    @isset($trainings)
                        @if ($trainings->count() > 0)
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('site.image')</th>
                                    <th>@lang('site.status_post')</th>
                                    <th>@lang('site.title')</th>
                                    <th>@lang('site.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($trainings as $index=>$training)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><img src="{{ $training->image }}" style="width: 100px;"
                                                 class="img-thumbnail" alt=""></td>
                                        <td>{{ $training->status }}</td>
                                        <td>{{ app()->getLocale() == "ar" ? $training->title_ar : $training->title_en }}</td>
                                        <td>
                                            @if (auth()->user()->hasRole('super_admin') )
                                                <a href="{{ route('dashboard.trainings.applications', $training->id) }}"
                                                   class="btn btn-info btn-sm"><i
                                                        class="fa fa-file"></i> @lang('site.applications')</a>
                                            @else
                                                <a href="#" class="btn btn-info btn-sm disabled"><i
                                                        class="fa fa-edit"></i> @lang('site.applications')</a>
                                            @endif
                                            @if (auth()->user()->hasRole('super_admin') )
                                                <a href="{{ route('dashboard.trainings.edit', $training->id) }}"
                                                   class="btn btn-success btn-sm"><i
                                                        class="fa fa-edit"></i> @lang('site.edit')</a>
                                            @else
                                                <a href="#" class="btn btn-success btn-sm disabled"><i
                                                        class="fa fa-edit"></i> @lang('site.edit')</a>
                                            @endif

                                            @if (auth()->user()->hasRole('super_admin') )
                                                <form
                                                    action="{{ route('dashboard.trainings.destroy', $training->id) }}"
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

                            {{ $trainings->appends(request()->query())->links() }}

                        @else

                            <h2>@lang('site.no_data_found')</h2>

                        @endif
                    @endisset

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
