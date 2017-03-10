<?php

$_SESSION['debug'] = array();

function Debug($message) {
    $_SESSION['debug'][] = $message;
}

