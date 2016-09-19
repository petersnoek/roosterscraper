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
    public $tijden;
    public $klassen_array;
    public $lokalen_array;

    function __construct() {
        $this->klassen_array = array();
        $this->lokalen_array = array();
        $this->tijden = array();
    }

    public $replaceklassen = [
        'MBICO' => '',
        'MBADO' => '',
    ];

    function GetStartTS() {
        if ( isset ($this->starttijd)) {
            $parts = explode(':', $this->starttijd);
            return mktime($hour = $parts[0], $minute = $parts[1]);
        } else return null;
    }

    function GetEindTS() {
        if (isset($this->eindtijd)) {
            $parts = explode(':', $this->eindtijd);
            return mktime($hour = $parts[0], $minute = $parts[1]);
        } else return null;
    }

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
        if ( isset($this->klassen_array) && sizeof($this->klassen_array)>0) {
            $out = "";
            foreach ($this->klassen_array as $k) {
                $klas = str_replace(array_keys($this->replaceklassen), array_values($this->replaceklassen), $k);
                $klas = rtrim($klas, "0");
                $klas = rtrim($klas, "1");
                $out .= $klas . ' ';
            }
            return rtrim($out, ' ');
        }
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
    public $lesalias = [
        'Engels' => 'ENG',
        'Rekenen' => 'REK',
        'Engels in de beroepscontext A2' => 'ENG',
        'KEUZEDEEL' => 'KEUZ',
        'Keuzedeel' => 'KEUZ',
        'keuzedeel' => 'KEUZ',
        'DTP vakken' => 'DTP',
        'projecten' => 'PROJ',
        'Projecten' => 'PROJ',
        'Nederlands' => 'NED',
        'Vormgeving' => 'VGV',
        'SLB/BS' => 'SLBS',
    ];

    public $allelokalen = [
        'MBW.0a160' => 'a160',
        'MBW.0a170' => 'a170',
        'MBW.0a170B' => 'a170 vide',
        'MBW.0a173' => 'a173',
        'MBW.0a180' => 'a180 REF',
        'MBW.0a180B' => 'a180 Vide',
        'MBW.0d180' => 'd180 DTP',

        'MBW.0d190' => 'd190 lab',
        'MBW.1d170' => '1d170',
    ];

    function __construct()
    {
        $this->lessen = array();
    }


    function ZoekLesEnKlas($dag, $tijd, $docent)
    {
        $timeparts = explode(':', $tijd);
        $tijdTS = mktime($hour = $timeparts[0], $minute=$timeparts[1]);
        foreach ($this->lessen as $les) {
            if ($les->dag == $dag && $les->docent == $docent && $les->GetStartTS() <= $tijdTS && $les->GetEindTS() > $tijdTS) {
                $lesCode = (array_key_exists($les->lescode, $this->lesalias) ? $this->lesalias[$les->lescode] : $les->lescode);
                return '<span style="color: blue;">' . $lesCode . '</span> ' . $les->GetKlassenShort();

            }

        }
        return "-";
    }

    function ZoekKlas($dag, $tijd, $docent)
    {
        $timeparts = explode(':', $tijd);
        $tijdTS = mktime($hour = $timeparts[0], $minute=$timeparts[1]);

        foreach ($this->lessen as $les) {
            if ($les->dag == $dag && $les->docent == $docent && $les->GetStartTS() <= $tijdTS && $les->GetEindTS() > $tijdTS)
                return $les->GetKlassen();
        }
        return "-";
    }

    function ZoekLokaalLes($dag, $tijd, $lokaal)
    {
        $timeparts = explode(':', $tijd);
        $tijdTS = mktime($hour = $timeparts[0], $minute=$timeparts[1]);

        foreach ($this->lessen as $les) {
            if (isset($les->lokalen_array)==false) $les->lokalen_array = Array();
            if ($les->dag == $dag && in_array($lokaal, $les->lokalen_array) && $les->GetStartTS() <= $tijdTS && $les->GetEindTS() > $tijdTS)
                return "<span style='color: blue;'>" . $les->docent . "</span> " . $les->GetKlassenShort();
        }
        return "-";
    }

    function ZoekDocentEnLes($dag, $tijd, $klas) {
        $timeparts = explode(':', $tijd);
        $tijdTS = mktime($hour = $timeparts[0], $minute=$timeparts[1]);

        foreach ($this->lessen as $les) {
            if ($les->dag == $dag && $les->GetStartTS() <= $tijdTS && $les->GetEindTS() > $tijdTS && in_array($klas, $les->klassen_array)  ) {
                $lesCode = (array_key_exists($les->lescode, $this->lesalias) ? $this->lesalias[$les->lescode]
                    : $les->lescode);

                return "<span style='color: blue;'>" . $lesCode . "</span> " . $les->docent;
            }
        }
        //if ( $dag == 'ma') Debug(sprintf('%s %s %s -', $dag, $tijd, $klas));

        return "-";
    }
}

class DocentroosterScraper {

    public $url;
    public $docent;
    public $rawdata;
    public $lessenContainer;
    public $alle_klassen;

    function __construct($docent, $url, $alleklassen) {
        $this->lessenContainer = new LessenContainer();
        $this->alle_klassen = $alleklassen;

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

                $klassen_in_les = $xpath->query('div[@class="AttendeeBlockColumn_1"]/div/a', $les);
                foreach ($klassen_in_les as $klas) {
                    //$lesobj->AddKlasIfInList($klas->nodeValue, $this->alle_klassen);

                    $lesobj->klassen_array[] = $klas->nodeValue;
                }

                $lokalen = $xpath->query('div[@class="AttendeeBlockColumn_2"]/div/a', $les);
                foreach ($lokalen as $lokaal) {
                    if ( array_key_exists($lokaal->nodeValue, $this->lessenContainer->allelokalen) == false ) {
                        // voeg lokaal toe als het nog niet bestaat
                        $lok = $lokaal->nodeValue;
                        $this->lessenContainer->allelokalen[$lok] = $lok;
                    }
                    $lesobj->lokalen_array[] = $lokaal->nodeValue;
                }

                $this->lessenContainer->lessen[] = $lesobj;

        }



    }


}