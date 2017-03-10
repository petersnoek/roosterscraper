<!doctype html>
<html>
<head>

	<meta http-equiv="language" content="NL">
	<meta lang="NL">

	<link href="css/style.css" rel="stylesheet">
	<title>Rooster</title>

	<!-- font awesome -->
	<script src="https://use.fontawesome.com/bf8ab24a40.js"></script>

	<!-- font roboto -->
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

</head>
<body>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

<!-- show the topmenu bar -->
<div class="topbar">
	<span style="float:left;">{{ $dagen[0] }}</span>
</div>

<!-- show errors, if present -->
@if(isset($_SESSION['errors']))				{{-- does $errors exist? --}}
	@if(sizeof($_SESSION['errors'])>0)			{{-- does $errors have any errors? --}}
		<div class="errors" >
		<ul>
			@foreach ($_SESSION['errors'] as $error)
				<li>{{ $error }}</li>
			@endforeach
			<?php $_SESSION['errors'] = []; ?>
		</ul>
		</div>
	@endif
@endif

<!-- content goes here -->
<div class="content">
@yield('content')
</div>

@if(isset($_SESSION['debugbar']) && $_SESSION['debugbar'] )
<div class="debugbar">
	<div class="debugbar-inner" style="padding:3px">

		<div class="col">
			<p>Debug statements: </p>
			@if(isset($_SESSION['debug']))
			@foreach($_SESSION['debug'] as $key)
				<li>{!! $key !!} </li>
			@endforeach
			@endif
		</div>

		<div class="col">
			<p>Cookie contents: </p>
			@foreach($_COOKIE as $key => $value)
				<li>{!! $key !!} -> {!! $value !!} </li>
			@endforeach
		</div>

		<div class="col">
			<p>Session contents: </p>
			<p style="font-style:italic">Not shown: $_SESSION['debug']</p>
			<p style="font-style:italic">Not shown: $_SESSION['errors']</p>
			@foreach($_SESSION as $key => $value)
				@if ( $key != 'debug' && $key != 'errors')
				<li>{!! $key !!} -> {!! var_export($value) !!}</li>
				@endif
			@endforeach
		</div>

	</div>
</div>
@endif

</body>
</html>
