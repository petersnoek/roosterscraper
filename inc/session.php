<?php

session_start();

if ( !isset($_SESSION['reload']) ) $_SESSION['reload'] = false;
if ( !isset($_SESSION['debugbar']) ) $_SESSION['debugbar'] = false;


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


/**
 * Function: sanitize
 * Returns a sanitized string, typically for URLs.
 *
 * Parameters:
 *     $string - The string to sanitize.
 *     $force_lowercase - Force the string to lowercase?
 *     $remove_nonalpha - If set to *true*, will remove all non-alphanumeric characters.
 */
function Sanitize($string, $force_lowercase = true, $remove_nonalpha = false) {
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
        "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
        "â€”", "â€“", ",", "<", ".", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($string)));
    $clean = preg_replace('/\s+/', "-", $clean);
    $clean = ($remove_nonalpha) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
    return ($force_lowercase) ?
        (function_exists('mb_strtolower')) ?
            mb_strtolower($clean, 'UTF-8') :
            strtolower($clean) :
        $clean;
}