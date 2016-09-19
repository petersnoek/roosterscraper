<?php

session_start();
date_default_timezone_set('Europe/Amsterdam');

function AddError($errormsg) {
    $_SESSION['errors'][] = $errormsg;
}

function GetErrorsThenRemoveErrors() {
    $out = '<div class="errors"><ul>';

    if ( isset($_SESSION['errors']) && sizeof($_SESSION['errors'])>0 ) {
        foreach ($_SESSION['errors'] as $err) {
            $out .= '<li>' . $err . '</li>';
        }
    }

    // clear all errors
    $_SESSION['errors'] = Array();

    $out .= '</ul></div>';
    return $out;
}