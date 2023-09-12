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
                                <input class="form-control" id="next_url" value="{{ $next_page_url }}">
                                <input class="form-control" id="user_id" value="{{ $user_id }}">

                                <div id="chat_wrapper">
                                <div id="chat_screen" style="overflow: auto">
                                    <div class="box-body">
                                        <div style="height: 100%" class="direct-chat-messages">

                                        </div>
                                    </div>
                                </div>
                                </div>
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
@push("scripts")
    <script>
        // window.innerHeight
        $("#chat_screen").css("height",window.innerHeight -500 )

    </script>
    <script src="https://unpkg.com/infinite-scroll@4/dist/infinite-scroll.pkgd.min.js"></script>
    <script>
        $(document).ready(function () {
            var chatScreen = $('#chat_screen');

            chatScreen.scroll(function () {
                if (chatScreen.scrollTop() === 0) {
                    // Load more data when scrolling up
                    var s = $("#next_url").val();
                    if ( s != ""){
                        loadMoreMessages();
                    }
                }
            });

        });
        $(".direct-chat-messages").empty()
        loadMoreMessages();
        function loadMoreMessages() {
            $.ajax({
                url: $("#next_url").val(),
                method: 'GET',
                dataType: 'json', // Expect JSON response
                success: function (data) {

                    console.log(data.data)
                    if(data.data.length > 0){
                        data.data.forEach((e)=>{
                            if(e.direct == 'right'){
                                $(".direct-chat-messages").prepend(getRightChat(e.name,e.sent_at,e.message));
                            }else{
                                $(".direct-chat-messages").prepend(getLeftChat(e.name,e.sent_at,e.message));
                            }
                        })
                    }
                    console.log()
                    if(data.links.next == null){
                        $("#next_url").val("");
                    }else{
                        $("#next_url").val(data.links.next.split("?")[0] + "?user_id="+$("#user_id").val()+"&"+data.links.next.split("?")[1]);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error loading more data:', error);
                }
            });
        }
        function getLeftChat(name,sent_at,message){
            var html = `<div  class="direct-chat-msg" style="float: right">
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-name pull-left">${name}</span>
                            <span
                                class="direct-chat-timestamp pull-right">${sent_at}</span>
                        </div>
                        <div style="width: fit-content;" class="direct-chat-text">
                            ${message}
                </div>
            </div>
                                                        <div class="clearfix"></div>
`;
            return html;
        }
        function getRightChat(name,sent_at,message){
            var html = `<div  class="direct-chat-msg right"  style="float: left">
                    <div class="direct-chat-info clearfix">
                        <span  class="direct-chat-name pull-right">${name}</span>
                        <span class="direct-chat-timestamp pull-left">${sent_at}</span>
                    </div>
                    <div class="direct-chat-text" style="width: fit-content;">
                        ${message}
                </div>
            </div>
                                                        <div class="clearfix"></div>
`;
            return html;
        }
    </script>
@endpush
