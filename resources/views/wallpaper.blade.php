<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <link href="https://cdn.bootcss.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .wallpaper-block{
            float: left;
            margin-left: 5px;
        }
        .wallpaper-block img{
            width: 180px;
            margin-top: 5px;
            margin-bottom: 5px;
        }
    </style>
    <!-- Styles -->
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-1">
        </div>
        <div class="col-md-10">
            <div class="row">
                <div class="col w-100 text-center mt-2 mb-3">
                    <button type="button" class="btn btn-outline-success">Success</button>
                    <button type="button" class="btn btn-outline-danger">Danger</button>
                    <button type="button" class="btn btn-outline-warning">Warning</button>
                </div>
            </div>

            <form class="form-inline" method="post" action="/haha/test/gogo" enctype="multipart/form-data">

                <div class="row">
                    <div class="col">
                        <input type="password" name="pwd" class="form-control" id="inputPassword2" placeholder="Password">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="type" value="{{$type}}">
                    </div>
                    <div class="col">
                        <input type="file" class="form-control-file" id="wallpaper" name="wallpaper">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">上传</button>
                </div>

            </form>

            <div class="row">
                <div class="col-md-12">
                    @if($wallpapers)
                        @foreach ($wallpapers as $wallpaper)

                            <div class="text-center wallpaper-block">
                                <img src="{{ \Illuminate\Support\Facades\URL::asset('image/wallpaper/'.$type.'/' . $wallpaper->filename) }}" class="rounded" alt="...">
                                <br>
                                <button type="button" wp-id="{{$wallpaper->id}}" class="wallpaper-del-btn btn btn-primary btn-sm">删除</button>
                            </div>

                        @endforeach
                    @endif
                </div>
            </div>

        </div>
        <div class="col-md-1">
        </div>
    </div>
</div>

<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/vue/2.5.13/vue.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js"></script>

<script type="text/javascript">
    $(function(){
        $('.wallpaper-del-btn').on('click', function(){
            var that = $(this);
            var id = that.attr('wp-id');
            var type = "{{$type}}";
            $.get('/haha/test/papa', {'wid':id, 'type':type}, function(d){
                if(d.status == 'success'){
                    that.parents('.wallpaper-block').remove();
                }else{
                    alert('删除失败');
                }
            },'json');

        })
    })
</script>
</body>
</html>
