<?php

class DownloadRequest {
    public $Id;
    public $Url;
    public $Options;
    public $MaxAgeSeconds;
    public $ForceReload;

    public function __construct($id, $url, $options, $maxAgeSeconds, $forcereload = false) {
        $this->Id = $id;
        $this->Url = $url;
        $this->Options = $options;
        $this->MaxAgeSeconds = $maxAgeSeconds;
        $this->ForceReload = $forcereload;
    }

}