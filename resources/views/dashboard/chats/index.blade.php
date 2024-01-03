@extends('layouts.dashboard.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1> @lang('site.chats')</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.welcome') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a>
                </li>
                <li><a href="{{ route('dashboard.chats.index') }}"> @lang('site.all_chats')</a></li>
            </ol>
        </section>
        <section class="content">
            <div class="box">
{{--                <div class="box-header">--}}
{{--                    <div class="ibox-title">--}}
{{--                        <div class="box-header with-border">--}}
{{--                            <h3 class="box-title">@lang('site.chats')</h3>--}}
{{--                        </div>--}}
{{--                        <div class="ibox-tools">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="box-body">
                    <table style="table-layout:fixed" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>@lang("site.user_image")</th>
                            <th style="overflow: hidden;">@lang('site.user_name')</th>
                            <th style="overflow: hidden;">@lang('site.created_from')</th>
{{--                            <th style="overflow: hidden;">@lang('site.un_read_messages')</th>--}}
                            <th style="overflow: hidden;">@lang('site.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @isset($chats)
                            @foreach($chats as $key => $chat)
                                <tr>
                                    <td><img src="{{ $chat->fromUser->image }}" style="width: 100px;" class="img-thumbnail" alt=""></td>
                                    @php
                                        $countUnReadMessages = App\Models\ChatMessage::where("from_user_id", $chat->fromUser->id )->where("status", "notReaded")
                                                ->count();
                                    @endphp
                                    @if($countUnReadMessages > 0)
                                        <td>{{ $chat->fromUser->name }}      <span class="label label-danger">{{ $countUnReadMessages }}</span> </td>

                                    @else
                                        <td>{{ $chat->fromUser->name }} </td>
                                    @endif

                                    @php
                                        $lastMessage = App\Models\ChatMessage::where("from_user_id", $chat->fromUser->id )->latest()
                                          ->first();
                                    @endphp
                                    <td>{{!is_null($lastMessage) ?  Carbon\Carbon::parse($lastMessage->created_at)->diffForHumans():'' }} </td>

                                    {{-- <td>{{ $chat->fromUser->chats->where("status","notReaded")->count() }}</td>--}}
                                    <td class="d-inline-block" >

                                        <form style="display: inline" action="{{ route('dashboard.chats.massages') }}" method="GET">
                                            <input type="hidden" name="user_id" value="{{ $chat->fromUser->id }}">
                                            <button type="submit" class="tooltips btn btn-primary" >
                                                <i class="fa fa-send"></i>
                                            </button>
                                        </form>


                                    </td>

                                </tr>
                                {{--                                Modal--}}
                                <div class="modal fade" id="{{$chat->fromUser->id}}" page="dialog" aria-labelledby="confirmDeleteLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" aria-hidden="true"></button>
                                                <h4 class="modal-title">{{trans('site.delete_complain')}}</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p> {{trans('backend.realy_deletet')}} </p>
                                            </div>

                                            <div class="modal-footer">
                                                <form action="" method="post"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-primary">{{trans('site.confirm')}}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endisset
                        </tbody>
                    </table>
                    {{ $chats->appends(request()->query())->links() }}
                </div>
            </div>
        </section>
    </div>

@endsection
@isset($complains)
    @foreach($complains as $key => $complain)
        <div class="modal fade" id="_{{$complain->id}}" page="dialog" aria-labelledby="confirmDeleteLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" aria-hidden="true"></button>
                        <h4 class="modal-title">{{trans('site.complains_detail')}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                    <tr>
                                        <th>
                                            {{ trans('site.complain_number') }}
                                        </th>
                                        <td>
                                            {{ $complain->id.'#' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            {{ trans('site.complain_status') }}
                                        </th>
                                        <td>
                                            {{ $complain->status }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            {{ trans('site.complain_title') }}
                                        </th>
                                        <td>
                                            {{ __('site.'.$complain->type) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            {{ trans('site.complain_content') }}
                                        </th>
                                        <td>
                                            <b><strong> {{ $complain->reason }}</strong></b>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>
                                            {{ trans('site.complain_author_name') }}
                                        </th>
                                        <td>
                                            {{ $complain->user->username }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            {{ trans('site.complain_author_mobile') }}
                                        </th>
                                        <td>
                                            {{ $complain->user->mobile }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            {{ trans('site.complain_comments') }}
                                        </th>
                                        <td>
                                            <p>There are no comments.</p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endisset
