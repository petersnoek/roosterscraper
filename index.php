<?php

$url = 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/61?Code=MBO%20Gorinchem%20e.o.%20(MBW%2C%20HSF%20en%20PZK)&OrgId=15';

// get page
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $page = curl_exec($curl);
    if(curl_errno($curl)) // check for execution errors
    {
        echo 'Scraper error: ' . curl_error($curl);
        exit;
    }
    curl_close($curl);

// load page into DOM object
$DOM = new DOMDocument;
libxml_use_internal_errors(true);
if (!$DOM->loadHTML($page))
{
    $errors="";
    foreach (libxml_get_errors() as $error)  {
        $errors.=$error->message."<br/>";
    }
    libxml_clear_errors();
    print "libxml errors:<br>$errors";
    return;
}

// query DOM object
$xpath = new DOMXPath($DOM);

echo '<h1>Docenten</h1>';

$attendees = $xpath->query('//div[@class="AttendeeTypeBlock" and .//*[contains(text(), "Medewerker")]]/div/a');
//$query = 'div';
//$entries = $xpath->query($query, $case1);
foreach ($attendees as $entry) {
    echo " {$entry->nodeValue} | {$entry->nodeValue}<br /> ";
}

