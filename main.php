<?php 

require_once 'inc/session.php';
require_once 'inc/blade.php';
require_once 'inc/errors.php';

// tell blade to create HTML from the template "login.blade.php"
echo $blade->view()->make('main')->withErrors($errors)->render();