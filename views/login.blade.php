@extends('_layout')

@section('content')
<h1>Login</h1>
<form method="POST" action="login_action.php" class="loginform">
	<label for="email">Email</label>  
	<input name="email" type="email" ><br>
	
	<label for="pass">Password</label>  
	<input name="pass" type="password" ><br>

	<input name="remember" type="checkbox" value="checked"> Onthoud mij <br>

	<input type="submit"><br>
</form>
@endsection

