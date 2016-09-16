<?php

require_once 'inc/session.php';
require_once 'vendor/autoload.php';
require_once 'inc/debug.php';

class Les {
    public $docent;
    public $dag;            // ma di wo do vr
    //public $datum;
    public $starttijd;
    public $eindtijd;
    public $lescode;
    public $klassen_array;
    public $lokalen_array;

    function GetLokalen() {
        if ( isset($this->lokalen_array) && sizeof($this->lokalen_array)>0)
            return implode(', ', $this->lokalen_array);
        else
            return '';
    }
    function GetKlassen() {
        if ( isset($this->klassen_array) && sizeof($this->klassen_array)>0)
            return implode(' ', $this->klassen_array);
        else
            return '';
    }
    function GetKlassenShort() {
        if ( isset($this->klassen_array) && sizeof($this->klassen_array)>0)
            return str_replace('MBICO', '', implode('*', $this->klassen_array));
        else
            return '';
    }

    function __toString()
    {
        return sprintf('? ? ? ?-?', $this->docent, $this->dag, $this->lescode, $this->starttijd, $this->einddtijd);
    }
}

class LessenContainer
{
    public $lessen;

    function ZoekLes($dag, $tijd, $docent)
    {
        foreach ($this->lessen as $les) {
            if ($les->dag == $dag && $les->docent == $docent && $les->starttijd <= $tijd && $les->eindtijd > $tijd)
                return $les->lescode;
        }
        return "-";
    }

    function ZoekKlas($dag, $tijd, $docent)
    {
        foreach ($this->lessen as $les) {
            if ($les->dag == $dag && $les->docent == $docent && $les->starttijd <= $tijd && $les->eindtijd > $tijd)
                return $les->GetKlassen();
        }
        return "-";
    }

    function ZoekDocent($dag, $tijd, $klas) {
        foreach ($this->lessen as $les) {
            //Debug($les->GetKlassen() . '<>' . $klas);
            if ($les->dag == $dag &&
                $les->starttijd <= $tijd && $les->eindtijd > $tijd &&
                strpos($les->GetKlassen(), $klas)>0
                )
            return $les->docent;
        }
        return "-";
    }
}

class DocentroosterScraper {

    public $url;
    public $docent;
    public $rawdata;
    public $lessenContainer;
    
    function __construct($docent, $url) {
        $this->lessenContainer = new LessenContainer();

        $this->docent = $docent;
        //Debug('Scraping: ' . $url);
        // get page
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $page = curl_exec($curl);
        if(curl_errno($curl)) // check for execution errors
        {
            AddError('Scraper error for ' . $url . ':<br> ' . curl_error($curl));
            return;
        }
        curl_close($curl);

        // load page into DOM object
        $DOM = new DOMDocument;
        libxml_use_internal_errors(true);
        if (!$DOM->loadHTML($page))
        {
            foreach (libxml_get_errors() as $error)  {
                AddError("LibXML error for ' . $url . ': " . $error->message);
            }
            libxml_clear_errors();
            return;
        }

        // query DOM object
        $xpath = new DOMXPath($DOM);

        $lessen = $xpath->query('//div[@class="Rooster width5showTimedays"]/div[@class="Les"]');
        foreach ($lessen as $les) {
                // <div class="Les" style="width: 155px; height: 201px; top: 169px; left: 160px;" >
                //                                                                        ---
                // knip na "left: " en voor "px;"
                $style = $les->getAttribute("style");
                $arr = explode('left: ', $style);
                $left = $arr[1];
                $px = explode('px;', $left);
                $p = $px[0];

                $lesobj = new Les();
                $lesobj->docent = $this->docent;
                switch ($p) {
                    case '160':
                        $lesobj->dag = 'ma';
                        break;
                    case '318':
                        $lesobj->dag = 'di';
                        break;
                    case '476':
                        $lesobj->dag = 'wo';
                        break;
                    case '634':
                        $lesobj->dag = 'do';
                        break;
                    case '792':
                        $lesobj->dag = 'vr';
                        break;
                }

                $tijden = $xpath->query('div[@class="LesTijden"]/@title', $les)->item(0)->nodeValue;
                $start = explode("-", $tijden);
                $lesobj->starttijd=$start[0];
                $lesobj->eindtijd=$start[1];
                $lesobj->lescode = $xpath->query('div[@class="LesCode"]/@title', $les)->item(0)->nodeValue;

                $klassen = $xpath->query('div[@class="AttendeeBlockColumn_1"]/div/a', $les);
                foreach ($klassen as $klas) {
                    $lesobj->klassen_array[] = $klas->nodeValue;
                }

                $lokalen = $xpath->query('div[@class="AttendeeBlockColumn_2"]/div/a', $les);
                foreach ($lokalen as $lokaal) {
                    $lesobj->lokalen_array[] = $lokaal->nodeValue;
                }

                $this->lessenContainer->lessen[] = $lesobj;

        }



    }


}