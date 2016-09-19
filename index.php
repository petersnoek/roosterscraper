<?php

require_once 'inc/session.php';
require_once 'inc/DashboardScraper.php';
require_once 'inc/DocentroosterScraper.php';
require_once 'inc/blade.php';
require_once 'inc/errors.php';
require_once 'inc/debug.php';
require_once 'inc/tijden.php';
require_once 'inc/docenten.php';
require_once 'inc/klassen.php';

$dashboards = [
    'MBO Gorinchem' => 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/61?Code=MBO%20Gorinchem%20e.o.%20(MBW%2C%20HSF%20en%20PZK)&OrgId=15',
//    'Techniek en Media' => 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/58?Code=Techniek%20%26%20Media&OrgId=15',
//    'Gezondheidszorg en Welzijn' => 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/59?Code=Gezondheidszorg%20en%20Welzijn&OrgId=15',
//    'Economie en Ondernemerschap' => 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/57?Code=Economie%20%26%20Ondernemerschap&OrgId=15',
//    'Entree' => 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/60?Code=Entree%20en%20Helpende&OrgId=15',
//    'Vavo' => 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/72?Code=VAVO&OrgId=15',
];

$docentroosterscrapers = array();

$lessenContainer = new LessenContainer();



// scrape het dashboard en verzamel de roosters voor de gezochte docenten
$ds = new DashboardScraper($dashboards, $docenten);

// haal urls op van de gevonden docentroosters
foreach($ds->docentroosterurls as $url) {
    $drs = new DocentroosterScraper($url['docent'], $url['roosterurl'], $alle_klassen);
    foreach($drs->lessenContainer->lessen as $lesObj) {
        $lessenContainer->lessen[] = $lesObj;
    }
    //echo $url['docent'] . " | " . $url['roosterurl'] . '<br>';
}

$dagen = ['ma', 'di', 'wo', 'do', 'vr'];

// tell blade to create HTML from the template "login.blade.php"
echo $blade->view()->make('main')
    ->with('docenten', $ds->docentroosterurls)
    ->with('lessenContainer', $lessenContainer)
    ->with('dagen', $dagen)
    ->with('tijden', $tijden)
    ->with('lokalen', $lessenContainer->allelokalen)
    ->with('klassen', $alle_klassen)
    ->withErrors($errors)
    ->render();
