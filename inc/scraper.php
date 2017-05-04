<?php

require_once 'inc/db.php';      // gives us a $db mysqli connection;
require_once 'inc/Les.php';      // gives us a $db mysqli connection;

function FillOrganisationsTable(\mysqli $mysqli, $dashboardurl) {

    $curl = curl_init($dashboardurl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $page = curl_exec($curl);
    if (curl_errno($curl)) $errors[] = 'Curl error while loading url: ' . curl_error($curl);
    curl_close($curl);

    $DOM = new DOMDocument;
    libxml_use_internal_errors(true);
    if (!$DOM->loadHTML($page)) {
        $error = "";
        foreach (libxml_get_errors() as $error)  { $error .= 'Libxml parsing error: ' . $error->message; }
        libxml_clear_errors();
        $errors[] = "libxml errors:<br>" . $error;
    }

    $xpath = new DOMXPath($DOM);
    $attendees = $xpath->query('//div[@class="organisatie" ]/a');
    // <a href="https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/58?Code=Techniek%20%26%20Media&amp;OrgId=15" target="_blank">
    $count = 0;
    foreach ($attendees as $entry) {
        $name = $entry->nodeValue;  // Techniek & Media
        $id = explode("?", explode("/", $entry->getAttribute('href'))[3])[0];  // 3rd part after Attendees is the id
        $url = 'https://roosters.xedule.nl' . $entry->getAttribute('href');
        // echo $url . ' : ' . $name . ' : (' . $id . ')<br>';
        insertOrganisation($mysqli, $id, $name, $url);
        $count++;
    }
    return $count;
}

function FillDocentGroepLokaalTables(\mysqli $mysqli, $org_code, $org_name, $dashboardurl) {
    echo '<h3 style="color:red">' . $org_name . '</h3>';

    $curl = curl_init($dashboardurl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $page = curl_exec($curl);
    if (curl_errno($curl)) // check for execution errors
    {
        $errors[] = 'Error while loading url: ' . curl_error($curl);
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
        $errors[] = "libxml errors:<br>" . $errors;
        return;
    }

    // query DOM object
    $xpath = new DOMXPath($DOM);

    $docenten = $xpath->query('//div[@class="AttendeeTypeBlock" and .//*[contains(text(), "Medewerker")]]/div/a');
    $count = 0;
    foreach ($docenten as $entry) {
        $docent = $entry->nodeValue;
        $docent_rooster = 'https://roosters.xedule.nl' . $entry->getAttribute('href');
        insertDocentRooster($mysqli, $docent, $org_code, $docent_rooster);
        echo '<span>' . $docent . '</span> ';
        $count++;
    }
    echo '<br>Docenten: ' . $count . '<br>';
    $_SESSION['docenten'] = $_SESSION['docenten'] + $count;

    $count = 0;
    $groepen = $xpath->query('//div[@class="AttendeeTypeBlock" and .//*[contains(text(), "Studentgroep")]]/div/a');
    foreach ($groepen as $entry) {
        $groep = $entry->nodeValue;
        $groep_rooster = 'https://roosters.xedule.nl' . $entry->getAttribute('href');
        insertGroepRooster($mysqli, $groep, $org_code, $groep_rooster);
        echo '<span>' . $groep . '</span> ';
        $count++;
    }
    echo '<br>Groepen: ' . $count . '<br>';
    $_SESSION['groepen'] = $_SESSION['groepen'] + $count;

    $count = 0;
    $lokalen = $xpath->query('//div[@class="AttendeeTypeBlock" and .//*[contains(text(), "Faciliteit")]]/div/a');
    foreach ($lokalen as $entry) {
        $lokaal = $entry->nodeValue;
        $rooster = 'https://roosters.xedule.nl' . $entry->getAttribute('href');
        insertLokaalRooster($mysqli, $lokaal, $org_code, $rooster);
        echo '<span>' . $lokaal . '</span> ';
        $count++;
    }
    echo '<br>Lokalen: ' . $count . '<br>';
    $_SESSION['lokalen'] = $_SESSION['lokalen'] + $count;


    return $count;
}

function DownloadDocentRoosters(\mysqli $mysqli) {

    $where = ' WHERE docent_code in ("ANS","DUP","HJW",
    "KAG","SLR","JDR","MRC","HRR","MSR","ENU","KWS","VPP","SNP","ESA","ARAS","MET","RSP","KHA") ';
    $docentroosters = getDocentRoosters($mysqli);

    //dump_formatted($docentroosters);

    // array of curl handles
    $curly = array();
    // data to be returned
    $result = array();
    // multi handle
    $mh = curl_multi_init();

    // loop through $data and create curl handles then add them to the multi-handle
    foreach ($docentroosters as $dr) {
        $result[$dr['id']] = $dr;

        $download[$dr['id']] = $dr['url'];

        $curly[$dr['id']] = curl_init();

        curl_setopt($curly[$dr['id']], CURLOPT_URL,            $dr['url']);
        curl_setopt($curly[$dr['id']], CURLOPT_HEADER,         0);
        curl_setopt($curly[$dr['id']], CURLOPT_RETURNTRANSFER, 1);

        curl_multi_add_handle($mh, $curly[$dr['id']]);
    }

    // execute the handles
    $running = null;
    do {
        curl_multi_exec($mh, $running);
        flush();
    } while($running > 0);

    // get content and remove handles
    foreach($curly as $id => $c) {
        $result[$id]['output'] = curl_multi_getcontent($c);
        curl_multi_remove_handle($mh, $c);
    }

    curl_multi_close($mh);

    // now process all results one by one
    foreach($result as $r) {
        echo '<span style="color:red;">' . $r['docent_code'] . '</span>';
        $lesObjects = ScrapeLessenFromHTML($r['output'], $mysqli, $r['organisation_id']);
        if (empty($lesObjects)) break;
        foreach ($lesObjects as $les) {
        //    echo '<li>' . $les->__toString(). '</li>';
            flush();
        }
    }

    return null;
}

function ScrapeLessenFromHTML($html, $mysqli, $organisation_id)
{
    $lesObjects = array();
    if (empty($html)) return null;

    $DOM = new DOMDocument;
    libxml_use_internal_errors(true);
    if (!$DOM->loadHTML($html)) {
        foreach (libxml_get_errors() as $error) {
            //AddError("LibXML error for ' . $url . ': " . $error->message);
        }
        libxml_clear_errors();
        return "libxml error";
    }

    // query DOM object
    $xpath = new DOMXPath($DOM);

    $datums = [];
    $docent = '';

    // get the five dates
    /* <div class="Rooster width5showTimedays">
        <div class="dagContainer">
            <div class="dag width1cell" style="height: 31px;">Maandag<br />03-04-2017</div>
    */
    $lesdagen = [];
    $datumobjects = $xpath->query('//div[@class="Rooster width5showTimedays"]/div[@class="dagContainer"]/div[@class="dag width1cell"]');
    foreach ($datumobjects as $datum) {
        $raw = $datum->nodeValue;
        $raw = str_replace(' ', '', $raw);
        $raw = str_replace('Maandag', '', $raw);
        $raw = str_replace('Dinsdag', '', $raw);
        $raw = str_replace('Woensdag', '', $raw);
        $raw = str_replace('Donderdag', '', $raw);
        $raw = str_replace('Vrijdag', '', $raw);
        $date = DateTime::createFromFormat('d-m-Y', trim($raw))->setTime(0,0,0);
        $lesdagen[] = $date;
    }

    // check that this page has a title (if it doesn't, scraping is pointless)
    if (!empty($xpath->query('//div[@class="breadcrumbs"]')->item(0)->nodeValue)) {
        // last item in the breadcrumb is the subject of this rooster (either a docent, groep or lokaal)
        $docentraw = $xpath->query('//div[@class="breadcrumbs"]')->item(0)->nodeValue;
        $raw = explode('>>', $docentraw);
        $docent = trim(end($raw));

        // process each les
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
            $lesobj->docent = $docent;
            switch ($p) {
                case '160':
                    $lesobj->dag = 'ma';
                    $lesobj->datum = $lesdagen[0];
                    break;
                case '318':
                    $lesobj->dag = 'di';
                    $lesobj->datum = $lesdagen[1];
                    break;
                case '476':
                    $lesobj->dag = 'wo';
                    $lesobj->datum = $lesdagen[2];
                    break;
                case '554':
                    $lesobj->dag = 'wo';    // when two lessons are planned at the same time
                    $lesobj->datum = $lesdagen[2];
                    break;

                case '634':
                    $lesobj->dag = 'do';
                    $lesobj->datum = $lesdagen[3];
                    break;
                case '792':
                    $lesobj->dag = 'vr';
                    $lesobj->datum = $lesdagen[4];
                    break;
                default:
                    $lesobj->dag = '?';
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
                //if (array_key_exists($lokaal->nodeValue, $this->lessenContainer->allelokalen) == false) {
                    // voeg lokaal toe als het nog niet bestaat
                    //$lok = $lokaal->nodeValue;
                    //$this->lessenContainer->allelokalen[$lok] = $lok;
                //}
                $lesobj->lokalen_array[] = $lokaal->nodeValue;
            }

            $lesObjects[] = $lesobj;

            insertLes($mysqli, $organisation_id, $lesobj->docent, $lesobj->dag, $lesobj->datum, $lesobj->starttijd, $lesobj->eindtijd,
                $lesobj->minuten, $lesobj->halfuren, $lesobj->lescode, implode(" ", $lesobj->klassen_array), implode(" ", $lesobj->lokalen_array));

//function insertLes(\mysqli $mysqli, $docent, $dag, $datum, $starttijd, $eindtijd, $minuten, $halfuren, $lescode, $klassen_array, $lokalen_array) {

        }
        return $lesObjects;
    }

}