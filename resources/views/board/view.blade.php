@extends('layouts.app')
@section('title', '内容')
@section('style')
  
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
  <style>
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.76563rem;
        line-height: 1.5;
        border-radius: 1px
    }
    .btn-cyan {
        color: #fff;
        background-color: #27a9e3;
        border-color: #27a9e3
    }
    .btn-cyan:hover {
        color: #fff;
        background-color: #1a93ca;
        border-color: #198bbe
    }
    .btn-green {
        color: #fff;
        background-color: #1cb335;
        border-color: #1cb335
    }
    .btn-green:hover {
        color: #fff;
        background-color: #11c72f;
        border-color: #11b12c
    }
    .comment-widgets .comment-row:hover {
        background: rgba(0, 0, 0, 0.05)
    }
  </style>
@endsection

@section('content')
<div class="container" data-aos="zoom-in" data-aos-delay="100">
    <h1>Wiz Board</h1>
    <div class="w3-container table-responsive table--no-card m-b-40" style="margin-top:4%;">
        <div class="w3-panel w3-card-4">
            <div class="card-body">
                <div class="title" style="font-size:30px; color:gray; margin-botton:5%;">
                    <h4>
                    {{$wiz_boards->title}}
                </h4>
                </div>
    
                    <h6 class="card-subtitle text-muted mb-4" style="margin-top:5%; margin-left:3%;">
                        <i> ID : {{$wiz_boards->user_id}}</i>
                        · 
                        <i> CREAT_DATE : {{ $wiz_boards->updated_at ? date("F j, Y, g:i a", strtotime($wiz_boards->updated_at)) : date("F j, Y, g:i a", strtotime($wiz_boards->created_at))}}</i>
                        
                    </h6>
                    <p class="" style="font-family: 'Do Hyeon', sans-serif; color:gray; font-size:30px; margin-left:3%;">{{$wiz_boards->content}}</p>
             
            </div>
            <div class="card-body" style="margin-left:3%; margin-bottom:3%;">
                @if (Auth::user()->id == $wiz_boards->getUserName->id)
                    <button class="btn btn-green btn-sm" onclick="window.location='{{ route('board.edit', ['id'=>$wiz_boards->id]) }}'">Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="window.location='{{route('board.destroy', ['id'=>$wiz_boards->id]) }}'">Delete</button>
                @endif
                <a href="{{ url('list')}}" class="btn btn-light btn-sm" role="button" style="float:right; margin-right:3%;">List</a>
            </div>
        </div>
    </div>   
    <div class="w3-container table-responsive table--no-card m-b-40" >
        <div class="w3-panel w3-card-4">
            <div class="comment-widgets m-3">
                 {{-- Comment Row --}}
                @if ($wiz_boards->comments->count() == 0)
                <div class="d-flex flex-row comment-row m-t-0" id="empty-comment">
                    <div class="comment-text w-100 text-center">
                        <h4>The comments are empty.</h4>
                    </div>
                </div> 
                @else 
                @foreach ($wiz_boards->comments as $item)
                <div style="padding-top: 1rem; padding-bottom: 1rem; border: 0; border-bottom: 1px solid rgba(0,0,0,.1);" id="comment-table-{{$item->id}}">
                    <div class="d-flex flex-row comment-row m-t-0 p-3">
                        <div class="comment-text w-100">
                            <h6 class="font-medium">{{ $item->getUserName->name }}</h6> 
                            <span class="m-b-15" id="comment-{{ ($item->id) }}">{{ $item->content }} </span>
                            <textarea type="text" name="content-{{ ($item->id) }}" id="content-{{ ($item->id) }}" class="form-control" rows="3" style="display: none;">{{ $item->content }}</textarea>
                            <div class="comment-footer pt-3"> 
                                <span class="text-muted float-right">{{ date("F j, Y, g:i a", strtotime($item->created_at)) }}</span> 
                                @if ($item->comment_writer_id == Auth::user()->id)
                                <button type="button" class="btn btn-green btn-sm" id="comment-edit-{{ $item->id }}" onClick="comment_edit({{ $item->id }})">Edit</button> 
                                <button type="button" class="btn btn-cyan btn-sm" id="comment-save-{{ $item->id }}" onClick="comment_save({{ $item->id }})" style="display: none;">Publish</button> 
                                <button type="button" class="btn btn-danger btn-sm" id="comment-delete-{{ $item->id }}" onClick="comment_delete({{ $item->id }})" >Delete</button> 
                                @endif
                            </div>
                        </div>
                    </div>
                </div> 
                @endforeach
                @endif
                <div id="comment-table-add" style="display: none;">
                </div>
                <div class="d-flex flex-row comment-row m-t-0 p-3">
                    <div class="comment-text w-100">
                        <h6 class="font-medium">{{ Auth::user()->name }}様</h6> 
                        <form id="form-comment">
                            @csrf
                            <div class="form-group">
                                <input type="hidden" value="{{ $wiz_boards->id }}" name="post_id" id="post_id">
                                <textarea type="text" name="content" id="content" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <button id="btn-comment"type="submit" class="btn btn-cyan btn-sm">Create</button> 
                            </div>
                        </form>
                    </div>
                </div> 
            </div> 
            {{-- Card  --}}
        </div>
    </div>
</div>
@endsection


@section('script')
<script>
function comment_edit(id) {
    // 하나를 숨기고 보여주고 
    $("#comment-"+id).hide();       // span 태그
    $("#content-"+id).show();       // textarea 태그
    $("#comment-edit-"+id).hide();  // 편집 버튼
    $("#comment-save-"+id).show();  // 발행 버튼
} 
$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
    
function comment_save(id){  // 발행 버튼이 가지고 있는 이벤트
    $.ajax({
        data: {
            id : id, // 댓글번호
            content : $("#content-"+id).val() // 댓글내용 
        },
        url: "{{ route('comment.update') }}",
        type: "POST",
        dataType: 'JSON',
        success: function (data) {
            // console.log(data['data']['content']);
            $("#comment-"+id).show(); 
            $("#comment-"+id).text(data['data']['content']);  
            $("#content-"+id).hide();
            $("#content-"+id).text(data['data']['content']); 
            $("#comment-edit-"+id).show();
            $("#comment-save-"+id).hide();
            messageWith('success', 'コメントが修正されました。');
            
        },
        error: function (data) {
            messageWith('danger', 'コメントの修正に失敗しました。');
        }
    });
}
function comment_delete(id){
    $.ajax({
        data: {
            id : id // 댓글 PK
        },
        url: "{{ route('comment.delete') }}",
        type: "POST",
        dataType: 'JSON',
        success: function (data) {
            // console.log(data);
            $("#comment-table-"+id).slideUp();
            messageWith('danger', 'コメントが削除されました。');
        },
        error: function (data) {
            messageWith('danger', 'コメントの削除に失敗しました。');
        }
    });
}
$("#btn-comment").click(function (event) {
    event.preventDefault();
    var form = $('#form-comment')[0];
    var data = new FormData(form);
    $("#btn-comment").prop("disabled", true);
    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: "{{ route('comment.create') }}",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        success: function (data) {
            console.log(data);
            // 데이터가 성공일 경우 
            if(data['success'] == true) {
                var id = data['data']['id'];
                var user_name = data['data']['user_name'];
                var time = data['data']['time'];
                var content = data['data']['content'];
                var html = "<div style='padding-top: 1rem; padding-bottom: 1rem; border: 0; border-bottom: 1px solid rgba(0,0,0,.1);' id='comment-table-"+id+"'>"
                        + "<div class='d-flex flex-row comment-row m-t-0 p-3'>"
                        + "<div class='comment-text w-100'>"
                        + "<h6 class='font-medium'>" +user_name+ "</h6>"
                        + "<span class='m-b-15' id='comment-" + id + "'> " + content +" </span>"
                        + "<textarea type='text' name='content-" + id + "' id='content-"+id+"' class='form-control' rows='3' style='display: none;'> " +content+" </textarea>"
                        + "<div class='comment-footer pt-3'> " 
                        + "<span class='text-muted float-right'>" + time + "</span> "
                        + "<button type='button' class='btn btn-cyan btn-sm' id='comment-edit-"+id+"' onClick='comment_edit("+id+")'>Edit</button>"
                        + "<button type='button' class='btn btn-success btn-sm' id='comment-save-"+id+"' onClick='comment_save("+id+")' style='display: none;'>Publish</button>" 
                        + "<button type='button' class='btn btn-danger btn-sm' id='comment-delete-"+id+"' onClick='comment_delete("+id+")' >Delete</button>"
                        + "</div>"
                        + "</div>"
                        + "</div>"
                        + "</div>";
                $('#comment-table-add').append(html); 
                $('#comment-table-add').slideDown();  
                $('#content').val('');              
                $("#btn-comment").prop("disabled", false); 
                $("#empty-comment").remove();    
                messageWith('success', 'コメントが作成されました。'); 
            } else {
                $("#btn-comment").prop("disabled", false);
                messageWith('danger', 'コメントの作成に失敗しました。');
            }
            
        },
        error: function(data){
            // console.log(data);
            messageWith('danger', 'コメントの作成に失敗しました。<br>管理者にお問い合わせください。');
            $("#btn-comment").prop("disabled", false);
        }
    });
});
// color : 색상 , message : 내용
function messageWith(color, message) {
        $.notify({
            icon: "nc-icon nc-bell-55",
            message: message
        }, {
            type: color,
            timer: 3000,
            placement: {
                from: 'top',
                align: 'right'
            },
            animate: {
                enter: 'animated fadeInUp',
                exit: 'animated fadeOutRight'
            },
            offset: 20,
            spacing: 10,
            z_index: 1000
        });
}
</script>
@endsection