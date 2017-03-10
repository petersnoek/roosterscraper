<?php

class FileCache {

    private $cacheLocation;

    public function __construct($CacheLocation) {
        if (!file_exists($CacheLocation)) {
            mkdir($CacheLocation, 0777, true);
        }
        $this->cacheLocation = $CacheLocation;
    }

    public function getItem($key) {
        $filename = $this->cacheLocation . $this->Sanitize($key);
        if ( file_exists($filename) ) {
            return file_get_contents($filename);
        } else {
            return false;
        }
    }

    public function hasItem($key, $max_age_seconds = 0) {
        $filename = $this->cacheLocation . $this->Sanitize($key);
        if ( file_exists($filename) ) {
            if ( $max_age_seconds == 0 ) return true;    // file found and no aging limit
            else if ( time()-filemtime($filename) > $max_age_seconds ) return false;    // file found but too old
                else return true;   // file found, age ok
        } else return false;   // file not found
    }

    public function save($key, $value) {
        $filename = $this->cacheLocation . $this->Sanitize($key);
        file_put_contents($filename, $value);
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
    private function Sanitize($string, $force_lowercase = true, $remove_nonalpha = false)
    {
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = ($remove_nonalpha) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean;
        return ($force_lowercase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }

}