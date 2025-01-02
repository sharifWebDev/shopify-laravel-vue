<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite('resources/frontend/css/style.css') 
</head>
<body class="font-poppins">
 
    <div class="content">
        <h2 class="mt-24 text-colorText text-2xl lg:text-3xl xl:text-[40px] font-bold leading-loose text-center  max-md:mt-8 max-md:max-w-full px-6"
            role="heading" aria-level="1">
         Wellcome to Vite app
        </h2> 
    </div> 
</body>

</html>
