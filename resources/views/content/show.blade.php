<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Just to show</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
    <div class="my-10">
        {{ json_encode($object->matter()) }}
    </div>
    <div class="my-10">
        @if(isset($dangerouslyAllowBladeRender) && $dangerouslyAllowBladeRender === true)
            {!! str($object->body())->markdown() !!}
        @else
            {{ str($object->body())->markdown() }}
        @endif
    </div>
</body>
</html>

