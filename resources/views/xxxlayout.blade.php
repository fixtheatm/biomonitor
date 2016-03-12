<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{ $header_title }}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

  @yield('header_js')
  @yield('header_css')
</head>
<body>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="#">Logo</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li @if ($route == "mybio") class="active" @endif><a href="/mybio">My BioReactor</a></li>
        <li @if ($route == "global") class="active" @endif><a href="/global">Global</a></li>
        <li @if ($route == "about") class="active" @endif><a href="/about">About</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="/login"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
      </ul>
    </div>
  </div>
</nav>


<div class="container">
@yield('main_container')   
</div>

<div class="container text-center"> 
@yield('whatwedo')   
</div>

<div class="container text-center">    
@yield('partners')
</div><br>

<footer class="container-fluid text-center">
	@yield('footer')
</footer>
@yield('footer_js')

</body>
</html>