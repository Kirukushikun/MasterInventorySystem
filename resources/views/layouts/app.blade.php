<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @vite(['resources/css/app.css', 'resources/js/app.js']) @include('layouts.head')
    </head>

    <body>
        <div class="wrapper">
            @if(\Auth::check())
                @include('layouts.side') 
            @endif
            <div class="main-panel">
                @if(\Auth::check()) 
                    @include('layouts.topnav')
                @endif 
                
                @yield('content')
                
                @if(\Auth::check()) 
                    @include('layouts.footer') 
                @endif
            </div>
        </div>
    </body>

    @if(\Auth::check()) 
        @include('layouts.scrpt')
    @endif
</html>
