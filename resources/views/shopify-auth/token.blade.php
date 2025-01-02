<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <title>{{env('APP_NAME')}}</title> 
    @vite('resources/js/app.js')
</head>

<body>

    <div>
        <div class="Polaris-SkeletonPage__Page" role="status" aria-label="Page loading">
            <div class="Polaris-SkeletonPage__Header">
                <div class="Polaris-SkeletonPage__TitleAndPrimaryAction">
                    <div class="Polaris-SkeletonPage__TitleWrapper">
                        <div class="Polaris-SkeletonPage__SkeletonTitle"></div>
                    </div>
                </div>
            </div>
            <div class="Polaris-SkeletonPage__Content">
                <div class="Polaris-Layout">
                    <div class="Polaris-Layout__Section">
                        <div class="Polaris-Card">
                            <div class="Polaris-Card__Section">
                                <div class="Polaris-SkeletonBodyText__SkeletonBodyTextContainer">
                                    <div class="Polaris-SkeletonBodyText"></div>
                                    <div class="Polaris-SkeletonBodyText"></div>
                                    <div class="Polaris-SkeletonBodyText"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>let vm = { host: '{{ \Request::get('host') }}' }</script>
    <script>
        const host = new URLSearchParams(location.search).get("host") ?? {{ \Request::get('host') }};
        const separator = '{!! Str::contains($target, "?") ? "&" : "?" !!}';
        const hostParam = '{!! Str::contains($target, "host") ? "" : "?host=" !!}';
        window.location.href = `{!! $target !!}${separator}token=${window.sessionToken}${hostParam}${host}`;


        {{--utils.getSessionToken(app).then((token) => {--}}
        {{--    window.location.href = `{!! $target !!}{!! Str::contains($target, '?') ? '&' : '?' !!}token=${token}{{ Str::contains($target, 'host')? '' : '&host=${host}'}}`;--}}
        {{--});--}}
    </script>

</body>
</html>
