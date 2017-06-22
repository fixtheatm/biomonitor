<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@if ( isset( $header_title )){{ $header_title }}@else @lang('messages.def_title')@endif</title>

  <!-- Fonts -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
  <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

  <!-- Styles -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/app.css" rel="stylesheet">
  <!-- need to figure out correct way of building / referencing path through gulp / elixir -->
  {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

@yield('header_css')
@yield('header_js')
</head>
<body id="app-layout">

<nav class="navbar navbar-inverse">
  <div class="navbar-header">
    <button type="button" data-target="#myNavbar" data-toggle="collapse" class="navbar-toggle">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="http://www.solarbiocells.com"><img class="img-rounded" src="/SolarBioCells_Logo_V1.png" alt="SolarBioCells.com logo"></a>
  </div>
  <div class="collapse navbar-collapse" id="myNavbar">
    <ul class="nav navbar-nav">
@if (!Auth::guest())
      @if ( isset($route) && $route == "mybio")<li class="active">@else<li>@endif<a href="{{ url('/mybio') }}">@lang('menus.user_bio_r')</a></li>
@endif
      @if ( isset($route) && $route == "global")<li class="active">@else<li>@endif<a href="{{ url('/global') }}">@lang('menus.global')</a></li>
@if (!Auth::guest() && Auth::user()->isadmin)
      @if ( isset($route) && $route == "users")<li class="active">@else<li>@endif<a href="{{ url('/users') }}">@lang('menus.users')</a></li>
      @if ( isset($route) && $route == "bioreactors")<li class="active">@else<li>@endif<a href="{{ url('/bioreactors') }}">@lang('menus.all_bio_r')</a></li>
@endif
      @if ( isset($route) && $route == "about")<li class="active">@else<li>@endif<a href="{{ url('/about') }}">@lang('menus.about')</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <!-- Authentication Links -->
@if (Auth::guest())
      <li><a href="{{ url('/login') }}">@lang('menus.login')</a></li>
@if ( false )
      <li><a href="{{ url('/register') }}">@lang('menus.register')</a></li>
@endif
@else
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
          {{ Auth::user()->name }}<span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
          <li><a href="{{ url('/logout') }}"><i class="glyphicon glyphicon-log-out"></i> @lang('menus.logout')</a></li>
          <li><a href="{{ url('/password') }}"><i class="glyphicon glyphicon-lock"></i> @lang('menus.chg_pass')</a></li>
        </ul>
      </li>
@endif
    </ul>
  </div>
</nav>
<div class="container">
@yield('content')
</div>
@yield('modal_insert')
<!-- JavaScripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
@yield('footer_js')

{{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
</body>
</html>
