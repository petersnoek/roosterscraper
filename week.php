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

$alle_klassen = [
    ['MBICO16A0', '16A0'],
//    ['MBICO15A0', '15A0', 'bpv'],
//    ['MBICO15A1', '15A1'],
//    ['MBICO14A0', '14B0'],
//   ['MBICO13A0', '13A0', 'bpv'],
    ['MBICO13A1', '13A 14A 15A'],
    ['MBICO16B0', '16B0'],
    ['MBICO16M0', '16M0'],
    ['MBICO16R0', '16R0'],
//  ['MBICO15B0', '15B0', 'bpv'],
//  ['MBICO15M0', '15M0', 'bpv'],
//  ['MBICO15B1', '15B1'],
//  ['MBICO15M1', '15M1'],
    ['MBICO13B0', '13B 14B 14M'],
//  ['MBICO14B0', '14B0'],
//  ['MBICO14M0', '14M0'],
];

$alle_tijden = [
//    ['1', '07:30', '08:00'],
//    ['2', '08:00', '08:30'],
    ['3', '08:30', '09:00'],
    ['4', '09:00', '09:30'],
    ['5', '09:30', '10:00'],
    ['6', '10:00', '10:45'],
//    ['-', '-----', '-----'],
    ['7', '10:45', '11:15'],
    ['8', '11:15', '11:45'],
    ['9', '	11:45', '12:15'],
    ['10', '12:15', '12:45'],
    ['11', '12:45', '13:15'],
    ['12', '13:15', '13:45'],
    ['13', '13:45', '14:15'],
    ['14', '14:15', '15:00'],
//    ['--', '-----', '-----'],
    ['15', '15:00', '15:30'],
    ['16', '15:30', '16:00'],
    ['17', '16:00', '16:30'],
    ['18', '16:30', '17:00'],
    ['19', '17:00', '17:30'],
//    ['20', '17:30', '18:00'],
//    ['21', '18:00', '18:30'],
//    ['22', '18:30', '19:00'],
//    ['23', '19:00', '19:30'],
//    ['24', '19:30', '20:00'],
//    ['25', '20:00', '20:45'],
//    ['26', '20:45', '21:15'],
//    ['27', '21:15', '21:45'],
];

$docenten = [
    'ANS'=>'https://roosters.xedule.nl/Attendee/ScheduleCurrent/58496?Code=ANS&attId=2&OreId=61',
    'HRR'=>'https://roosters.xedule.nl/Attendee/ScheduleCurrent/23741?Code=HRR&attId=2&OreId=61',
    'ENU'=>'https://roosters.xedule.nl/Attendee/ScheduleCurrent/23710?Code=ENU&attId=2&OreId=61',
    'KWS'=>'https://roosters.xedule.nl/Attendee/ScheduleCurrent/23610?Code=KWS&attId=2&OreId=61',
    'VPP'=>'https://roosters.xedule.nl/Attendee/ScheduleCurrent/23739?Code=VPP&attId=2&OreId=61',
    'MRC'=>'https://roosters.xedule.nl/Attendee/ScheduleCurrent/23709?Code=MRC&attId=2&OreId=61',
    'SNP'=>'https://roosters.xedule.nl/Attendee/ScheduleCurrent/23715?Code=SNP&attId=2&OreId=61',
    'MSR'=>'https://roosters.xedule.nl/Attendee/ScheduleCurrent/23714?Code=MSR&attId=2&OreId=61',
    'HJW'=>'https://roosters.xedule.nl/Attendee/ScheduleCurrent/36249?Code=HJW&attId=2&OreId=61',
    'SLR'=>'https://roosters.xedule.nl/Attendee/ScheduleCurrent/72093?Code=SLR&attId=2&OreId=61',
    'SLR'=>'https://roosters.xedule.nl/Attendee/ScheduleCurrent/23305?Code=SLR&attId=2&OreId=58',
    'JDR'=>'https://roosters.xedule.nl/Attendee/ScheduleCurrent/72094?Code=JDR&attId=2&OreId=61'
];
$dagen = ['ma', 'di', 'wo', 'do', 'vr'];

$_SESSION['reload'] = ( isset($_GET['reload']) && $_GET['reload'] == 'true');
$_SESSION['debugbar'] = ( isset($_GET['debugbar']) && $_GET['debugbar'] == 'true');
$_SESSION['week'] = ( isset($_GET['week']) );


// scrape docentroosters
foreach($docenten as $docent=>$rooster) {
    $drs = new DocentroosterScraper($docent, $rooster, $alle_klassen, $alle_tijden);
    foreach($drs->lessenContainer->lessen as $lesObj) {
        $lessenContainer->lessen[] = $lesObj;
    }
}

// tell blade to create HTML from the template "login.blade.php"
echo $blade->view()->make('main')
    ->with('docenten', $ds->docentroosterurls)
    ->with('lessenContainer', $lessenContainer)
    ->with('dagen', $dagen)
    ->with('tijden', $alle_tijden)
    ->with('lokalen', $lessenContainer->allelokalen)
    ->with('klassen', $alle_klassen)
    ->render();
