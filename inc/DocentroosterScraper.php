<?php

require_once 'inc/session.php';
require_once 'vendor/autoload.php';
require_once 'inc/debug.php';
require_once 'inc/Les.php';
require_once 'inc/LessenContainer.php';

class DocentroosterScraper {

    public $url;
    public $docent;
    public $rawdata;
    public $lessenContainer;
    public $alle_klassen;

    function __construct($url, $alleklassen, $allelesblokken) {
        $this->lessenContainer = new LessenContainer();
        $this->alle_klassen = $alleklassen;

        $basefolder = __DIR__ . '/../cache/docentroosters/';
        $cachefolder = $basefolder . date('YmdH') . '/';
        $filename = $cachefolder . Sanitize(md5($url));
        $reload = (isset($_SESSION['reload']) && $_SESSION['reload'] );

        // get page from cache or from url
        if ( file_exists($filename) && !$reload) {
            $_SESSION['debug'][] = 'Getting <a href="' . $url . '">' . $url . '</a> from cache: ' . $filename;
            // get page from cache
            $page = file_get_contents($filename);
        }
        else {
            $_SESSION['debug'][] = "Can't find ". $filename . ', getting ' . $url . ' from URL';
            // get page from url, and write to cache
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            $page = curl_exec($curl);
            if (curl_errno($curl)) // check for execution errors
            {
                AddError('Scraper error for ' . $url . ':<br> ' . curl_error($curl));
                return;
            }
            curl_close($curl);

            // remove old folders from cachefolder
            rrmdir($basefolder, date('YmdH'));

            // create timestamped cache folder
            if (!file_exists($cachefolder)) mkdir($cachefolder, 0777, true);

            // generate random filename and write it (if doesn't exist)
            if (!file_exists($filename) ) file_put_contents($filename, $page);

        }

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

        $datums = [];
        $datumobjects = $xpath->query('//div[@class="Rooster width5showTimedays"]/div[@class="dagContainer"]/div[@class="dag width1cell"]');
        foreach($datumobjects as $datum) {
            $raw = $datum->nodeValue;
            $raw = str_replace(' ', '', $raw);
            $raw = str_replace('Maandag', 'ma/', $raw);
            $raw = str_replace('Dinsdag', 'di/', $raw);
            $raw = str_replace('Woensdag', 'wo/', $raw);
            $raw = str_replace('Donderdag', 'do/', $raw);
            $raw = str_replace('Vrijdag', 'vr/', $raw);
            $this->lessenContainer->lesdagen[] = trim($raw);
        }

        if (! empty($xpath->query('//div[@class="breadcrumbs"]')->item(0)->nodeValue)) {

            $docentraw = $xpath->query('//div[@class="breadcrumbs"]')->item(0)->nodeValue;
            $raw = explode('>>', $docentraw);
            $this->docent = trim(end($raw));

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
                        $lesobj->datum = $this->lessenContainer->lesdagen[0];
                        break;
                    case '318':
                        $lesobj->dag = 'di';
                        $lesobj->datum = $this->lessenContainer->lesdagen[1];
                        break;
                    case '476':
                        $lesobj->dag = 'wo';
                        $lesobj->datum = $this->lessenContainer->lesdagen[2];
                        break;
                    case '634':
                        $lesobj->dag = 'do';
                        $lesobj->datum = $this->lessenContainer->lesdagen[3];
                        break;
                    case '792':
                        $lesobj->dag = 'vr';
                        $lesobj->datum = $this->lessenContainer->lesdagen[4];
                        break;
                }

                $tijden = $xpath->query('div[@class="LesTijden"]/@title', $les)->item(0)->nodeValue;
                $start = explode("-", $tijden);
                $lesobj->starttijd = $start[0];
                $lesobj->eindtijd = $start[1];

                $to_time = strtotime($start[0]);
                $from_time = strtotime($start[1]);
                $lesobj->minuten = round(abs($to_time - $from_time) / 60, 2);

                switch ($lesobj->minuten) {
                    case 30:
                    case 45:
                        $lesobj->halfuren = 1;
                        break;
                    case 60:
                    case 75:
                        $lesobj->halfuren = 2;
                        break;
                    case 90:
                    case 105:
                        $lesobj->halfuren = 3;
                        break;
                    case 120:
                    case 135:
                        $lesobj->halfuren = 4;
                        break;
                    default:
                        $lesobj->halfuren = 1;
                        break;
                }

                // kijk voor elk lesblok of de les erin valt


                $lesobj->lescode = $xpath->query('div[@class="LesCode"]/@title', $les)->item(0)->nodeValue;

                $klassen_in_les = $xpath->query('div[@class="AttendeeBlockColumn_1"]/div/a', $les);
                foreach ($klassen_in_les as $klas) {
                    //$lesobj->AddKlasIfInList($klas->nodeValue, $this->alle_klassen);

                    $lesobj->klassen_array[] = $klas->nodeValue;
                }

                $lokalen = $xpath->query('div[@class="AttendeeBlockColumn_2"]/div/a', $les);
                foreach ($lokalen as $lokaal) {
                    if (array_key_exists($lokaal->nodeValue, $this->lessenContainer->allelokalen) == false) {
                        // voeg lokaal toe als het nog niet bestaat
                        $lok = $lokaal->nodeValue;
                        $this->lessenContainer->allelokalen[$lok] = $lok;
                    }
                    $lesobj->lokalen_array[] = $lokaal->nodeValue;
                }

                $this->lessenContainer->AddLesIfUnique($lesobj);


            }
        }
    }
}