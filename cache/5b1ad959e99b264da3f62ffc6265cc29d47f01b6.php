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
<?php if(isset($errors)): ?>				<?php /* does $errors exist? */ ?>
	<?php if($errors->any()): ?>			<?php /* does $errors have any errors? */ ?>
	<div class="errors" >
	<ul>
		<?php foreach($errors->all() as $error): ?>		
			<li><?php echo e($error); ?></li>
		<?php endforeach; ?>
	</ul>
	</div>
	<?php endif; ?>
<?php endif; ?>

<!-- content goes here -->
<div class="content">
<?php echo $__env->yieldContent('content'); ?>
</div>


<div class="debugbar">
	<div class="debugbar-inner" style="padding:3px">

		<div class="col">
			<p>Debug statements: </p>
			<?php if(isset($_SESSION['debug'])): ?>
			<?php foreach($_SESSION['debug'] as $key): ?>
				<li><?php echo e($key); ?> </li>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<div class="col">
			<p>Cookie contents: </p>
			<?php foreach($_COOKIE as $key => $value): ?>
				<li><?php echo e($key); ?> -> <?php echo e($value); ?></li>
			<?php endforeach; ?>
		</div>

		<div class="col">
			<p>Session contents: </p>
			<p style="font-style:italic">Not shown: $_SESSION['debug']</p>
			<p style="font-style:italic">Not shown: $_SESSION['errors']</p>
			<?php foreach($_SESSION as $key => $value): ?>
				<?php if( $key != 'debug' && $key != 'errors'): ?>
				<li><?php echo e($key); ?> -> <?php echo e(var_export($value)); ?></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

	</div>
</div>

</body>
</html>
