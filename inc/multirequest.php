<?php
// found at http://www.phpied.com/simultaneuos-http-requests-in-php-with-curl/
// on 21-nov-2016

require_once 'FileCache.php';
require_once 'DownloadRequest.php';

function multiRequest($downloadRequests, $options = array(), $cachesubfolder = '') {

    $cache = new FileCache(__DIR__ . '/../cache/' . (empty($cachesubfolder)?'':$cachesubfolder . '/') );
    $cached = [];
    $download = [];

    // array of curl handles
    $curly = array();
    // data to be returned
    $result = array();

    // multi handle
    $mh = curl_multi_init();

    // loop through $data and create curl handles
    // then add them to the multi-handle
    foreach ($downloadRequests as $dr) {

        if ( $dr->ForceReload == false && $cache->hasItem($dr->Url, $dr->MaxAgeSeconds) ) {
            // found in cache. do not download it.
            $cached[$dr->Id] = $dr->Url;
        } else {
            // not found in cache. download it and return it.
            $download[$dr->Id] = $dr->Url;

            $curly[$dr->Id] = curl_init();

            $url = (is_array($dr->Url) && !empty($dr->Url['url'])) ? $dr->Url['url'] : $dr->Url;
            curl_setopt($curly[$dr->Id], CURLOPT_URL,            $url);
            curl_setopt($curly[$dr->Id], CURLOPT_HEADER,         0);
            curl_setopt($curly[$dr->Id], CURLOPT_RETURNTRANSFER, 1);

            // post?
            if (is_array($dr->Url)) {
                if (!empty($d['post'])) {
                    curl_setopt($curly[$dr->Id], CURLOPT_POST,       1);
                    curl_setopt($curly[$dr->Id], CURLOPT_POSTFIELDS, $d['post']);
                }
            }

            // extra options?
            if (!empty($options)) {
                curl_setopt_array($curly[$dr->Id], $options);
            }

            curl_multi_add_handle($mh, $curly[$dr->Id]);
        }

    }

    // execute the handles
    $running = null;
    do {
        curl_multi_exec($mh, $running);
    } while($running > 0);

    // get content and remove handles
    foreach($curly as $id => $c) {
        $result[$id] = curl_multi_getcontent($c);
        curl_multi_remove_handle($mh, $c);
        $cache->save($download[$id], $result[$id]);
    }

    foreach($cached as $id => $d) {
        $result[$id] = $cache->getItem($d);
    }

    // all done
    curl_multi_close($mh);

    return $result;
}
