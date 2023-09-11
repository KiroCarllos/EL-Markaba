@extends('layouts.dashboard.app')
@section('content')

    <div class="content-wrapper">
        <section class="content-header">
{{--            <h1>@lang('site.chat_messages')</h1>--}}
            <ol class="breadcrumb">
{{--                <li class="active"><i class="fa fa-dashboard"></i>@lang('site.chat_messages')</li>--}}
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12 d-inline">

                    <div class="col-md-6">
                        <a style="width: 30%;float: left" href="{{route("dashboard.chats.index")}}" class="btn btn-block btn-primary btn-lg  d-inline">رجوع  <i class="fa fa-arrow-circle-left"> </i></a>
                    </div>

                </div>
            </div>
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- DIRECT CHAT -->
                            <div class="box box-primary direct-chat direct-chat-primary">
                                <div class="box-header with-border">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <h3 class="box-title"> @lang("site.chats") <b><strong>:-</strong></b>  </h3>
                                        </div>
                                        <div class="col-md-11">
                                            <h3 class="box-title"> </h3>
                                        </div>
                                    </div>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                @isset($chats)
                                    @foreach($chats as $msg)
                                        <div class="box-body">
                                            <div style="height: 100%" class="direct-chat-messages">
                                                 @if($msg->to_user_id != $user_id )
                                                        <div  class="direct-chat-msg">
                                                            <div class="direct-chat-info clearfix">
                                                                <span class="direct-chat-name pull-left">{{ $msg->fromUser->name }}</span>
                                                                <span
                                                                    class="direct-chat-timestamp pull-right">{{ $msg->sent_at }}</span>
                                                            </div>
                                                            <div class="direct-chat-text">
                                                                {{ $msg->message }}

                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="direct-chat-msg right">
                                                            <div class="direct-chat-info clearfix">
                                                                <span  class="direct-chat-name pull-right">{{ $msg->fromUser->name }}</span>
                                                                <span class="direct-chat-timestamp pull-left">{{ $msg->sent_at }}</span>
                                                            </div>
                                                            <div class="direct-chat-text">
                                                             {{ $msg->message }}
                                                            </div>
                                                        </div>
                                                    @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endisset
                                    <div class="box-footer">
                                        <form action="" method="post">
                                            @csrf
                                            <div class="input-group {{ $errors->has("text") ? ' has-error' : '' }}">
                                                <input type="hidden" name="complain_id" value="">
                                                <input class="form-control {{ $errors->has("text") ? ' has-error' : '' }}"
                                                       style="height: 42px;" type="text" name="text"
                                                       placeholder="Type Message ...">
                                                <span class="input-group-btn">
                                            <button type="submit" class="btn btn-primary btn-flat">Send</button>
                                          </span>
                                            </div>
                                        </form>
                                    </div>
                                <!-- /.box-footer-->
                            </div>
                            <!--/.direct-chat -->
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>

        </section>
    </div>

@endsection
