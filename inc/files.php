<?php

function rrmdir($dir, $except = "") {

    $_SESSION['debug'][] = "Removing " . $dir . " except: " . $except;
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != ".." && $object != $except) {
                if (is_dir($dir."/".$object))
                    rrmdir($dir."/".$object, $except);
                else
                    if ( ! unlink($dir."/".$object) ) $_SESSION['debug'][] = "Failed removing " . $dir."/".$object;
            }
        }
        if ( is_dir_empty($dir))
        {
            if (rmdir($dir) == false) $_SESSION['debug'][] = "Failed removing " . $dir;
        }
    }
}

function is_dir_empty($dir) {
    if (!is_readable($dir)) return NULL;
    $handle = opendir($dir);
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            return FALSE;
        }
    }
    return TRUE;
}