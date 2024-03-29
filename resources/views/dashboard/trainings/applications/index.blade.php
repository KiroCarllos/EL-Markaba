@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.trainings')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')
                    </a></li>
                <li class="{{ route("dashboard.trainings.index") }}">@lang('site.trainings')</li>
                <li class="active">@lang('site.applications')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.applications')
                        <small>{{ $applications->count() }}</small></h3>

                    <form action="{{ route('dashboard.companies.index') }}" method="get">

                        <div class="row">

                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="@lang('site.search')"
                                       value="{{ request()->search }}">
                            </div>

                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i
                                        class="fa fa-search"></i> @lang('site.search')</button>
                            </div>

                        </div>
                    </form><!-- end of form -->

                </div><!-- end of box header -->

                <div class="box-body">
                    @isset($applications)
                        @if ($applications->count() > 0)
                            <table class="table table-bordered table-striped dataTable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('site.student_name')</th>
                                    <th>@lang('site.student_mobile')</th>
                                    <th>@lang('site.training')</th>
                                    <th>@lang('site.paid_or_not')</th>
                                    <th>@lang('site.receipt_image')</th>
                                    <th>@lang('site.application_status')</th>
                                    <th>@lang('site.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($applications as $index=>$application)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{  $application->user->name }}</td>
                                        <td>{{  $application->user->mobile }}</td>


                                        <td>{{ app()->getLocale() == "ar" ? $application->training->title_ar : $application->training->title_en }}</td>
                                        <td>{{  $application->training->paid }}</td>
                                        @if(!is_null($application->receipt_image))
                                            <td><img src="{{ asset($application->receipt_image) }}" style="width: 100px;"
                                                     class="img-thumbnail" alt=""></td>
                                        @else
                                            <td>@lang("site.not_found")</td>
                                        @endif


                                        <td>{{ $application->status }}</td>
                                        <td>

                                            @if (auth()->user()->hasRole('super_admin') )
                                                <a href="{{ route('dashboard.trainings.applications.edit', $application->id) }}"
                                                   class="btn btn-success btn-sm"><i
                                                        class="fa fa-edit"></i> @lang('site.edit')</a>
                                            @else
                                                <a href="#" class="btn btn-success btn-sm disabled"><i
                                                        class="fa fa-edit"></i> @lang('site.edit')</a>
                                            @endif

                                            @if (auth()->user()->hasRole('super_admin') )
                                                <form
                                                    action="{{ route('dashboard.trainings.applications.destroy', $application->id) }}"
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
