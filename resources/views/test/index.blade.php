<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

    </head>
    <body>
        <div class="flex-center position-ref full-height">

    <form action="{{$upload_url}}" method="post" id="imgForm" enctype="multipart/form-data" accept-charset="UTF-8">
        <div class="form-group">
            <input name="token" type="hidden"  value="{{$token}}">
            <input name="random" type="hidden"  value="{{$random[0]}}">
            <input name="file" type="file">
            <input type="submit" name="submit" value="开始上传">

        </div>
    </form>

<div class="content">


</div>
</div>
</body>
</html>
