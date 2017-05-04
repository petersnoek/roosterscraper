<?php

session_start();
$_SESSION['docenten'] = 0;
$_SESSION['groepen'] = 0;
$_SESSION['lokalen'] = 0;
$_SESSION['lessen'] = 0;

date_default_timezone_set('Europe/Amsterdam');
set_time_limit(60);    // 1 minute execution time
$errors = array();

header( 'Content-type: text/html; charset=utf-8' );

require_once 'inc/db.php';      // gives us a $db mysqli connection;
require_once 'inc/scraper.php';      // FillOrganisationsTableFromDashboard
require_once 'inc/html.header.php';


$dashurl = getSetting($db,'dashurl');

echo "start: " . date("H:i:s");

deleteAllOrganisations($db);
$result = FillOrganisationsTable($db, $dashurl);
echo '<h1>Results:</h1>';
echo 'Organisaties opgehaald: ' . $result . PHP_EOL . "<br>";

flush(); ob_flush();

deleteAllDocentRoosters($db);
deleteAllGroepRoosters($db);
deleteAllLokaalRoosters($db);
deleteAllLessen($db);

foreach ( getOrganisations($db) as $org) {
    FillDocentGroepLokaalTables($db, $org['id'], $org['name'], $org['url']);
}
$result = DownloadDocentRoosters($db);


$db->close();

echo '<div>';
echo '<h3>Docenten: ' . $_SESSION['docenten'] . '</h3>';
echo '<h3>Groepen: ' . $_SESSION['groepen'] . '</h3>';
echo '<h3>Lokalen: ' . $_SESSION['lokalen'] . '</h3>';
echo '<h3>Lessen: ' . $_SESSION['lessen'] . '</h3>';

echo "done: " . date("H:i:s");
echo '</div>';