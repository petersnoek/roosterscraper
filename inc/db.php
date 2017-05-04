<?php

define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1:3306');
define('DB_NAME', 'roosterscraper');
define('DB_USER', 'roosterscraper');
define('DB_PASS', 'roosterscraper');
define('DB_CHARSET', 'utf8');

$db = new mysqli(DB_HOST , DB_USER, DB_PASS, DB_NAME );
if ($db->connect_errno) {
    echo "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error;
}

function getSetting(\mysqli $mysqli, $setting) {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    $stmt = $mysqli->prepare("SELECT value FROM s_config WHERE setting=?");
    $stmt->bind_param("s", $setting);
    if (! $stmt->execute() ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);

    $stmt->bind_result($value);
    $stmt->fetch();
    $stmt->close();

    return $value;
}

function deleteAllOrganisations(\mysqli $mysqli) {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '(): $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    if (! $mysqli->query("DELETE FROM s_organisations") ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
    if (! $mysqli->query("ALTER TABLE s_organisations AUTO_INCREMENT = 1 ") ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
}

function insertOrganisation(\mysqli $mysqli, $id, $name, $url) {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    $stmt = $mysqli->prepare("INSERT INTO s_organisations (id, name, url) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id, $name, $url);

    if (! $stmt->execute() ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
    $stmt->close();
}

function getOrganisations(\mysqli $mysqli) {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    if (! ($result = $mysqli->query("SELECT id, name, url FROM s_organisations")) ) {
        throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

function deleteAllDocentRoosters(\mysqli $mysqli) {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    if (! $mysqli->query("DELETE FROM s_docentroosters") ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
    if (! $mysqli->query("ALTER TABLE s_docentroosters AUTO_INCREMENT = 1 ") ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
}

function insertDocentRooster(\mysqli $mysqli, $docent_code, $organisation_id, $url)
{
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    $stmt = $mysqli->prepare("INSERT INTO s_docentroosters (docent_code, organisation_id, url) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $docent_code, $organisation_id, $url);

    if (! $stmt->execute() ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
    $stmt->close();
}



function deleteAllGroepRoosters(\mysqli $mysqli) {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    if (! $mysqli->query("DELETE FROM s_groeproosters") ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
    if (! $mysqli->query("ALTER TABLE s_groeproosters AUTO_INCREMENT = 1 ") ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
}

function insertGroepRooster(\mysqli $mysqli, $groep_code, $organisation_id, $url) {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    $stmt = $mysqli->prepare("INSERT INTO s_groeproosters (groep_code, organisation_id, url) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $groep_code, $organisation_id, $url);
    if (! $stmt->execute() ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
    $stmt->close();
}

function deleteAllLokaalRoosters(\mysqli $mysqli) {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    if (! $mysqli->query("DELETE FROM s_lokaalroosters") ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
    if (! $mysqli->query("ALTER TABLE s_lokaalroosters AUTO_INCREMENT = 1 ") ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
}

function insertLokaalRooster(\mysqli $mysqli, $lokaal_code, $organisation_id, $url) {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    $stmt = $mysqli->prepare("INSERT INTO s_lokaalroosters (lokaal_code, organisation_id, url) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $lokaal_code, $organisation_id, $url);
    if (! $stmt->execute() ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
    $stmt->close();
}

function getGroepRoosters(\mysqli $mysqli, $where = '') {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    if (! ($result = $mysqli->query("SELECT * FROM s_groeproosters " . $where)) ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getDocentRoosters(\mysqli $mysqli, $where = '') {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    if (! ($result = $mysqli->query("SELECT * FROM s_docentroosters " . $where)) ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);

    return $result->fetch_all(MYSQLI_ASSOC);
}

function insertLes(\mysqli $mysqli, $organisation_id, $docent, $dag, $datum, $starttijd, $eindtijd, $minuten, $halfuren, $lescode, $klassen_array, $lokalen_array) {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    $datum2 = ($datum == null ? null : $datum->format('Y-m-d'));
    $dag2 = ($dag == null?"":$dag);

    $stmt = $mysqli->prepare("INSERT INTO s_lessen (docent_code, dag, datum, starttijd, eindtijd, minuten, halfuren, les_code, groepen_array, lokalen_array, organisation_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiisssi", $docent, $dag2, $datum2, $starttijd, $eindtijd, $minuten, $halfuren, $lescode, $klassen_array, $lokalen_array, $organisation_id);
    if (! $stmt->execute() ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
    $stmt->close();
}
function deleteAllLessen(\mysqli $mysqli) {
    if ($mysqli == null) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli is null');
    if ($mysqli->stat() == false) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : $mysqli not connected');

    if (! $mysqli->query("DELETE FROM s_lessen") ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
    if (! $mysqli->query("ALTER TABLE s_lessen AUTO_INCREMENT = 1 ") ) throw new Exception(__FILE__ . ' -> ' . __FUNCTION__ . '() : error during execute. ' . $mysqli->error);
}

function dump_formatted($data) {
    echo highlight_string("<?php\n\$data =\n" . var_export($data, true) . ";\n?>");
}