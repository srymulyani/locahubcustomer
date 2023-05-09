<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.email.header')
</head>

<body>
    <div class="header">
        @yield('title')
    </div>
    <div class="body">
        @yield('content')
    </div>
    @include('layouts.email.footer')
</body>

</html>
