<?php

function rrmdir($dir) {
    $_SESSION['debug'][] = "Removing " . $dir;
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir."/".$object))
                    rrmdir($dir."/".$object);
                else
                    if ( ! unlink($dir."/".$object) ) $_SESSION['debug'][] = "Failed removing " . $dir."/".$object;
            }
        }
        if ( ! rmdir($dir)) $_SESSION['debug'][] = "Failed removing " . $dir;
    }
}