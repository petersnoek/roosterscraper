<?php

require_once 'inc/session.php';

class DashboardScraper {
    public $url = 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/61?Code=MBO%20Gorinchem%20e.o.%20(MBW%2C%20HSF%20en%20PZK)&OrgId=15';
    public $docenten = array();
    public $docentroosterurls = array();
    
    function __construct($docenten) {
        $this->docenten = $docenten;

        // get page
        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $page = curl_exec($curl);
        if(curl_errno($curl)) // check for execution errors
        {
            AddError('Error while loading url: ' . curl_error($curl));
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
                AddError($error->message);
            }
            libxml_clear_errors();
            AddError("libxml errors:<br>$errors");
            return;
        }

        // query DOM object
        $xpath = new DOMXPath($DOM);

        $attendees = $xpath->query('//div[@class="AttendeeTypeBlock" and .//*[contains(text(), "Medewerker")]]/div/a');
        //$query = 'div';
        //$entries = $xpath->query($query, $case1);
        foreach ($attendees as $entry) {
            if (in_array($entry->nodeValue, $this->docenten)) {
                $docent = $entry->nodeValue;
                $docent_rooster = 'https://roosters.xedule.nl' . $entry->getAttribute('href');
                //echo $docent . ' | ' . $docent_rooster . '<br>';
                $this->docentroosterurls[] = ['docent'=>$docent, 'roosterurl'=>$docent_rooster];
            }
        }
    }
}