<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@if ( isset($header_title) ) {{ $header_title }} @else BioMonitor @endif</title>

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
    @yield('header_css')
    @yield('header_js')
</head>
<body id="app-layout">

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" style="padding-top:0.5em" href="http://www.solarbiocells.com"><img class="img-rounded" src="/SolarBioCells_Logo_V1.png" alt="SolarBioCells.com" style="width:80px;margin:0;padding:0"></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
	    @if (!Auth::guest())
         <li @if ( isset($route) && $route == "mybio") class="active" @endif><a href="{{ url('/mybio') }}">My BioReactor</a></li>
         <li @if ( isset($route) && $route == "global") class="active" @endif><a href="{{ url('/global') }}">Global</a></li>
		@endif
        <li @if ( isset($route) && $route == "about") class="active" @endif><a href="{{ url('/about') }}">About</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <!-- Authentication Links -->
		@if (Auth::guest())
		 <li><a href="{{ url('/login') }}">Login</a></li>
         @if ( false ) <li><a href="{{ url('/register') }}">Register</a></li> @endif
        @else
         <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
           <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
          </ul>
         </li>
        @endif

      </ul>
    </div>
  </div>
</nav>
	<div class="container">
    @yield('content')
	</div>
    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    @yield('footer_js')

    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
</body>
</html>
