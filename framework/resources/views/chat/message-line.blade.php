@php
// $message->fromUser->load('metas');    
@endphp
@if($message->from_user == auth()->id())

    <div class="row msg_container base_sent" data-message-id="{{ $message->id }}">
        <div class="col-md-10 col-10">
            <div class="messages msg_sent pb-2 text-right">
                <p class="mb-0">{!! $message->content !!}</p>
                <time datetime="{{ date("Y-m-dTH:i", strtotime($message->created_at->toDateTimeString())) }}">{{ $message->fromUser->name }} • {{ $message->created_at->diffForHumans() }}</time>
            </div>
        </div>
        <div class="col-md-2 col-2 avatar">
            @php
                $src = asset('assets/chats/images/no-user.png');  
                if($message->fromUser->profile_image != null){
                    $src = asset('./uploads/'.$message->fromUser->profile_image);
                }
            @endphp
            <img src="{{ $src }}" width="50" height="50" class="img img-fluid">
        </div>
    </div>
@else
    <div class="row msg_container base_receive" data-message-id="{{ $message->id }}">
        <div class="col-md-2 col-2 avatar">
            @php
                $src = asset('assets/chats/images/no-user.png');  
                if($message->fromUser->profile_image != null){
                    $src = asset('./uploads/'.$message->fromUser->profile_image);
                }
            @endphp
            <img src="{{ $src }}" width="50" height="50" class="img img-fluid">
        </div>
        <div class="col-md-10 col-10">
            <div class="messages msg_receive pb-2 text-left">
                <p class="mb-0">{!! $message->content !!}</p>
                <time datetime="{{ date("Y-m-dTH:i", strtotime($message->created_at->toDateTimeString())) }}">{{ $message->fromUser->name }} • {{ $message->created_at->diffForHumans() }}</time>
            </div>
        </div>
    </div>
@endif