<div class="weui-cells" style="margin-top: 0px;">
    @if(isset($messages))
        @foreach($messages as $msg)
            <div class="weui-cell">
                <div class="weui-cell__hd" style="position: relative;margin-right: 10px;">
                    <img src="{{ $msg->head or '/img/head.png' }}" style="width: 50px;display: block"/>
                    @if($msg->unread_count)
                        <span class="weui-badge" style="position: absolute;top: -.4em;right: -.4em;">{{ $msg->unread_count }}</span>
                    @endif
                </div>
                <div class="weui-cell__bd">
                    <a href="/view/chat?cid={{ $msg->uid }}"><p style="color: black">{{ $msg->name }}</p>
                    <a href="/view/chat?cid={{ $msg->uid }}"><p style="font-size: 13px;color: #888888;">{{ $msg->content }}</p></a>
                </div>
            </div>
        @endforeach
        <div class="weui-loadmore weui-loadmore_line weui-loadmore_dot">
            <span class="weui-loadmore__tips"></span>
        </div>

    @else
        <div class="weui-loadmore weui-loadmore_line">
            <span class="weui-loadmore__tips">暂无消息</span>
        </div>
    @endif
</div>