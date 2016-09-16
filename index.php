<?php

require_once 'inc/session.php';
require_once 'inc/DashboardScraper.php';
require_once 'inc/DocentroosterScraper.php';
require_once 'inc/blade.php';
require_once 'inc/errors.php';
require_once 'inc/debug.php';
require_once 'inc/tijden.php';
require_once 'inc/docenten.php';
require_once 'inc/lokalen.php';
require_once 'inc/klassen.php';


$docentroosterscrapers = array();

$lessenContainer = new LessenContainer();

// scrape het dashboard en verzamel de roosters voor de gezochte docenten
$ds = new DashboardScraper($docenten);

// haal urls op van de gevonden docentroosters
foreach($ds->docentroosterurls as $url) {
    $drs = new DocentroosterScraper($url['docent'], $url['roosterurl']);
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
    ->with('lokalen', $lokalen)
    ->with('klassen', $klassen)
    ->withErrors($errors)
    ->render();
