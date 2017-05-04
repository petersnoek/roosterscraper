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

// $dashboards = [
//    'MBO Gorinchem' => 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/61?Code=MBO%20Gorinchem%20e.o.%20(MBW%2C%20HSF%20en%20PZK)&OrgId=15',
//    'Techniek en Media' => 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/58?Code=Techniek%20%26%20Media&OrgId=15',
//    'Gezondheidszorg en Welzijn' => 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/59?Code=Gezondheidszorg%20en%20Welzijn&OrgId=15',
//    'Economie en Ondernemerschap' => 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/57?Code=Economie%20%26%20Ondernemerschap&OrgId=15',
//    'Entree' => 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/60?Code=Entree%20en%20Helpende&OrgId=15',
//    'Vavo' => 'https://roosters.xedule.nl/OrganisatorischeEenheid/Attendees/72?Code=VAVO&OrgId=15',
//];

// $docentroosterscrapers = array();

$lessenContainer = new LessenContainer();

$alle_klassen = [

    ['MBICO16GIL', '16GIL'],
//
//    ['MBICO13A0', '13A0', 'bpv'],
    ['MBICO13A1', '13A(3)', null, '#FFF8DC'],
//    ['MBICO14A0', '14A0', 'bpv'],
    ['MBICO14A1', '14A1', null, '#FFF8DC'],
    ['MBICO15A0', '15A0', null, '#FFF8DC'],
    ['MBICO16A0', '16A0'],

//    ['MBICO13B0', '13B0', 'bpv'],
    ['MBICO13B1', '13B1', null, '#E0FFFF'],
//    ['MBICO14B0', '14B0', 'bpv'],
    ['MBICO14B1', '14B1', null, '#E0FFFF'],
    ['MBICO15B0', '15B0', null, '#E0FFFF'],
    ['MBICO16B0', '16B0'],

    ['MBICO14M0', '14M0', null, '#FFF8DC'],
    ['MBICO15M0', '15M0', null, '#FFF8DC'],
    ['MBICO16M0', '16M0'],
    ['MBICO16R0', '16R0'],
//  ['MBICO15B0', '15B0', 'bpv'],
//  ['MBICO15M0', '15M0', 'bpv'],
//  ['MBICO15B1', '15B1'],
//  ['MBICO15M1', '15M1'],
//  ['MBICO14B0', '14B0'],
//  ['MBICO14M0', '14M0'],
];

$alle_tijden = [
//    ['1', '07:30', '08:00'],
//    ['2', '08:00', '08:30'],
    ['3', '08:30', '09:00'],
    ['4', '09:00', '09:30'],
    ['5', '09:30', '10:00'],
    ['6', '10:00', '10:30'],
    ['p', '10:30', '10:45'],
    ['7', '10:45', '11:15'],
    ['8', '11:15', '11:45'],
    ['9', '	11:45', '12:15'],
    ['10', '12:15', '12:45'],
    ['p', '12:45', '13:15'],
    ['12', '13:15', '13:45'],
    ['13', '13:45', '14:15'],
    ['14', '14:15', '14:45'],
    ['p', '14:45', '15:00'],
    ['15', '15:00', '15:30'],
    ['16', '15:30', '16:00'],
    ['17', '16:00', '16:30'],
    ['18', '16:30', '17:00'],
//    ['19', '17:00', '17:30'],
//    ['20', '17:30', '18:00'],
//    ['21', '18:00', '18:30'],
//    ['22', '18:30', '19:00'],
//    ['23', '19:00', '19:30'],
//    ['24', '19:30', '20:00'],
//    ['25', '20:00', '20:45'],
//    ['26', '20:45', '21:15'],
//    ['27', '21:15', '21:45'],
];

if ( isset($_GET['week']) && intval($_GET['week'])>0 ) {
    $week = $_GET['week'];
} else
{
    $date = new DateTime();
    $week = $date->format("W");
}

if ( isset($_GET['year']) && intval($_GET['year'])>0 ) {
    $year = $_GET['year'];
} else
{
    $date = new DateTime();
    $year = $date->format("Y");
}

$weekyear = '&week=' . $week . '&year=' . $year;

$docenten = [
    'ANS'=>'https://roosters.xedule.nl/Attendee/Schedule/58496?Code=ANS&attId=2&OreId=61' . $weekyear,
    'DUP'=>'https://roosters.xedule.nl/Attendee/Schedule/75424?Code=DUP&attId=2&OreId=61' . $weekyear,
    'HJW'=>'https://roosters.xedule.nl/Attendee/Schedule/36249?Code=HJW&attId=2&OreId=61' . $weekyear,
    'KAG'=>'https://roosters.xedule.nl/Attendee/Schedule/75265?Code=KAG&attId=2&OreId=61' . $weekyear,
    'SLR'=>'https://roosters.xedule.nl/Attendee/Schedule/72093?Code=SLR&attId=2&OreId=61' . $weekyear,
    'JDR'=>'https://roosters.xedule.nl/Attendee/Schedule/72094?Code=JDR&attId=2&OreId=61' . $weekyear,
    'MRC'=>'https://roosters.xedule.nl/Attendee/Schedule/23709?Code=MRC&attId=2&OreId=61' . $weekyear,
    'HRR'=>'https://roosters.xedule.nl/Attendee/Schedule/23741?Code=HRR&attId=2&OreId=61' . $weekyear,
    'MSR'=>'https://roosters.xedule.nl/Attendee/Schedule/23714?Code=MSR&attId=2&OreId=61' . $weekyear,
    'ENU'=>'https://roosters.xedule.nl/Attendee/Schedule/23710?Code=ENU&attId=2&OreId=61' . $weekyear,
    'KWS'=>'https://roosters.xedule.nl/Attendee/Schedule/23610?Code=KWS&attId=2&OreId=61' . $weekyear,
    'VPP'=>'https://roosters.xedule.nl/Attendee/Schedule/23739?Code=VPP&attId=2&OreId=61' . $weekyear,

    //      https://roosters.xedule.nl/Attendee/ScheduleCurrent/23715?Code=SNP&attId=2&OreId=61
    'SNP'=>'https://roosters.xedule.nl/Attendee/Schedule/23715?Code=SNP&attId=2&OreId=61' . $weekyear,
    'ESA'=>'https://roosters.xedule.nl/Attendee/Schedule/75262?Code=ESA&attId=2&OreId=61' . $weekyear,
    'AZA'=>'https://roosters.xedule.nl/Attendee/Schedule/76693?Code=AZA&attId=2&OreId=61' . $weekyear,
    'MET'=>'https://roosters.xedule.nl/Attendee/Schedule/24448?Code=MET&attId=2&OreId=61' . $weekyear,
    'RSP'=>'https://roosters.xedule.nl/Attendee/Schedule/23609?Code=RSP&attId=2&OreId=61' . $weekyear,
    'KHA'=>'https://roosters.xedule.nl/Attendee/Schedule/23636?Code=KHA&attId=2&OreId=61' . $weekyear,

    ];
$dagen = ['ma', 'di', 'wo', 'do', 'vr'];

if (! isset($_GET['week']) ) {
    $vandaag = date('N');
    switch ($vandaag) {
        case 1:
            $dagen = ['ma', 'di'];
            break;

        case 2:
            $dagen = ['di', 'wo'];
            break;

        case 3:
            $dagen = ['wo', 'do'];
            break;

        case 4:
            $dagen = ['do', 'vr'];
            break;

        case 5:
            $dagen = ['vr', 'ma'];
            break;

        case 6:
            $dagen = ['ma', 'di'];
            break;

        case 7:
            $dagen = ['ma', 'di'];
            break;
    }
}

$_SESSION['reload'] = ( isset($_GET['reload']) && $_GET['reload'] == 'true');
$_SESSION['debugbar'] = ( isset($_GET['debugbar']) && $_GET['debugbar'] == 'true');

// scrape het dashboard en verzamel de roosters voor de gezochte docenten
// $ds = new DashboardScraper($dashboards, $docenten);

// haal urls op van de gevonden docentroosters
foreach($docenten as $docent=>$url) {
    if (is_array($url)) {
        foreach($url as $u) {
            $drs = new DocentroosterScraper($u, $alle_klassen, $alle_tijden);
            foreach($drs->lessenContainer->lessen as $lesObj) {
                $lessenContainer->AddLesIfUnique($lesObj);
            }
        }
    }
    else {
        $drs = new DocentroosterScraper($url, $alle_klassen, $alle_tijden);
        foreach($drs->lessenContainer->lessen as $lesObj) {
            $lessenContainer->AddLesIfUnique($lesObj);
        }
    }

    //echo $url['docent'] . " | " . $url['roosterurl'] . '<br>';
}

$datums = $lessenContainer->lesdagen;
/*
highlight_string("<?php\n\$data =\n" . var_export($lessenContainer, true) . ";\n?>");
*/

// tell blade to create HTML from the template "login.blade.php"
echo $blade->view()->make('main')
    ->with('docenten', $docenten)
    ->with('lessenContainer', $lessenContainer)
    ->with('dagen', $dagen)
    ->with('datums', $datums)
    ->with('tijden', $alle_tijden)
    ->with('lokalen', $lessenContainer->allelokalen)
    ->with('klassen', $alle_klassen)
    ->with('week', $week)
    ->with('year', $year)
    ->render();
