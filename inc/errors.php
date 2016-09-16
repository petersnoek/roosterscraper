<?php

// provides an $errors variable if there are errors in the session


// if session contains errors, copy them to $errors variable
if ( isset ($_SESSION['errors'])) {
	$errors = $_SESSION['errors'];
	$_SESSION['errors'] = array();	// remove all errors
}