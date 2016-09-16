<!doctype html>
<html>
<head>
	<link href="css/style.css" rel="stylesheet">
	<title>Login</title>

	<!-- font awesome -->
	<script src="https://use.fontawesome.com/bf8ab24a40.js"></script>

	<!-- font roboto -->
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
</head>
<body>

<!-- show the topmenu bar -->
<div class="topbar">
	<span style="float:right;"/><span>No user logged in</span>
	<span style="float:left;"></span>
</div>

<!-- show errors, if present -->
@if(isset($errors))				{{-- does $errors exist? --}}
	@if($errors->any())			{{-- does $errors have any errors? --}}
	<div class="errors" >
	<ul>
		@foreach ($errors->all() as $error)		
			<li>{{ $error }}</li>
		@endforeach
	</ul>
	</div>
	@endif
@endif

<!-- content goes here -->
<div class="content">
@yield('content')
</div>


<div class="debugbar">
	<div class="debugbar-inner" style="padding:3px">

		<div class="col">
			<p>Debug statements: </p>
			@if(isset($_SESSION['debug']))
			@foreach($_SESSION['debug'] as $key)
				<li>{{ $key }} </li>
			@endforeach
			@endif
		</div>

		<div class="col">
			<p>Cookie contents: </p>
			@foreach($_COOKIE as $key => $value)
				<li>{{ $key }} -> {{ $value }}</li>
			@endforeach
		</div>

		<div class="col">
			<p>Session contents: </p>
			<p style="font-style:italic">Not shown: $_SESSION['debug']</p>
			<p style="font-style:italic">Not shown: $_SESSION['errors']</p>
			@foreach($_SESSION as $key => $value)
				@if ( $key != 'debug' && $key != 'errors')
				<li>{{ $key }} -> {{ var_export($value) }}</li>
				@endif
			@endforeach
		</div>

	</div>
</div>

</body>
</html>