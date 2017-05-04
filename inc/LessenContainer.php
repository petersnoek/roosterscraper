<?php


class LessenContainer
{
    public $lessen;
    public $docentlessen;
    public $klaslessen;
    public $lokaallessen;
    public $lesdagen = [];

    public $lesalias = [
    'Engels' => 'ENG',
    'Schoolactiviteit'=>'extern',
    'REKENEN' => 'REK',
    'Keuzedeel Engels' => 'ENG',
    'Engels in de beroepscontext A2' => 'ENG',
    'Computerlessen Senioren' => 'SENIOR',
    'Begeleidingsuur' => 'begeleid',
    'Begeleiding bedrijfsprojecten' => 'begeleid',
    'begeleiding bedrijfsprojecten' => 'begeleid',
    'begeleiding examenprojecten' => 'begeleid',
    'Teamoverleggen op LPP' => 'overleg',
    'Werkoverleg Beheer + Intake' => 'overleg',
    'Werkoverleg AO of Intake' => 'overleg',
    'Werkoverleggen op LPP' => 'overleg',
    'KEUZEDEEL' => 'KEUZ',
    'Keuzedeel' => 'KEUZ',
    'NEDERLANDS' => 'NED',
    'JAVASCRIPT' => 'PROG',
    'keuzedeel' => 'KEUZ',
    'DTP vakken' => 'DTP',
    'projecten' => 'PROJ',
    'Projecten' => 'PROJ',
    'Nederlands' => 'NED',
    'Vormgeving' => 'VGV',
    'SLB/BS' => 'SLBS',
    'Examentraining' => 'EX.BEG',
    'Tweede paasdag' => 'vrij',
    'Goede vrijdag' => 'vrij',
    ];

    function ShortenLes($les) {
        return (array_key_exists($les, $this->lesalias) ? $this->lesalias[$les] : $les);
    }

    public $allelokalen = [
        'MBW.0a160' => 'a160',
        'MBW.0a170' => 'a170',
        'MBW.0a170B' => 'a170 vide',
        'MBW.0a173' => 'a173',
        'MBW.0a180' => 'a180 REF',
        'MBW.0a180B' => 'a180 Vide',
        'MBW.0d180' => 'd180 lab',
        'MBW.0d190' => 'd190 DTP',
        'MBW.1d170' => '1d170',
    ];

    function __construct()
    {
        $this->lessen = array();
    }

    function GetDatumLesdag() {

    }

    function AddLesIfUnique(Les $les) {
        if ($les->lescode == "Goede vrijdag") $les->klassen_array = [];
        if ($les->lescode == "Tweede paasdag") $les->klassen_array = [];

        foreach($this->lessen as $compareles) {
            if($les->dag == $compareles->dag &&
                $les->docent == $compareles->docent &&
                $les->starttijd == $compareles->starttijd &&
                $les->eindtijd == $compareles->eindtijd &&
                $les->lescode == $compareles->lescode )
            {
                $_SESSION['errors'][] = 'Dubbele les gevonden.';
                return;
            }
        }

        if(!in_array($les->datum, $this->lesdagen)) {
            $this->lesdagen[] = $les->datum;
        }

        $this->lessen[] = $les;
        $this->docentlessen[$les->docent][] = $les;
        foreach($les->klassen_array as $klas) {
            $this->klaslessen[$klas][] = $les;
        }
        foreach($les->lokalen_array as $lokaal) {
            $this->lokaallessen[$lokaal][] = $les;
        }
    }

    function ZoekLesEnKlas($dag, $tijd, $docent, $plaintext = false)
    {
        if ($tijd == '-----') return "-";

        $les = $this->GetLesEnKlas($dag, $tijd, $docent);
        if ($les) {
            $lesCode = $this->ShortenLes($les->lescode);

            if (!$plaintext) {
                return '<span class="cel-eerste" style="color: blue;">' . $lesCode . '</span> ' .
                    "<span class='cel-tweede'>" . $les->GetKlassenShort(false) . "</span>";
            } else {
                return $lesCode . ' ' . $les->GetKlassenShort(false);
            }

        }
        return "-";
    }

    function getRowspanDocent($dag, $tijd, $docent, $plaintext = false) {
        if ($tijd == '-----') return 1;

        $les = $this->GetLesEnKlas($dag, $tijd, $docent);
        if ($les) {
            return ($les->starttijd == $tijd ? $les->halfuren : 0 );
        }
        return 1;
    }

    function getRowspanKlas($dag, $tijd, $klas, $plaintext = false) {
        if ($tijd == '-----') return 1;
        $timeparts = explode(':', $tijd);
        $tijdTS = mktime($hour = $timeparts[0], $minute=$timeparts[1]);

        foreach ($this->lessen as $les) {
            if ($les->dag == $dag && $les->GetStartTS() <= $tijdTS && $les->GetEindTS() > $tijdTS && in_array($klas, $les->klassen_array)  ) {
                return ($les->starttijd == $tijd ? $les->halfuren : 0 );
            }
        }
        return 1;
    }

    function GetLesEnKlas($dag, $tijd, $docent, $plaintext = false)
    {
        if ($tijd == '-----') return "-";

        $timeparts = explode(':', $tijd);
        $tijdTS = mktime($hour = $timeparts[0], $minute = $timeparts[1]);
        foreach ($this->lessen as $les) {
            if ($les->dag == $dag && $les->docent == $docent && $les->GetStartTS() <= $tijdTS && $les->GetEindTS() > $tijdTS) {
                return $les;
            }
        }
        return null;
    }

    function ZoekKlas($dag, $tijd, $docent)
    {
        if ($tijd == '-----') return "-";

        $timeparts = explode(':', $tijd);
        $tijdTS = mktime($hour = $timeparts[0], $minute=$timeparts[1]);

        foreach ($this->lessen as $les) {
            if ($les->dag == $dag && $les->docent == $docent && $les->GetStartTS() <= $tijdTS && $les->GetEindTS() > $tijdTS)
                return $les->GetKlassen();
        }
        return "-";
    }

    function ZoekLokaalLes($dag, $tijd, $lokaal, $plaintext = false)
    {
        if ($tijd == '-----') return "-";

        $timeparts = explode(':', $tijd);
        $tijdTS = mktime($hour = $timeparts[0], $minute=$timeparts[1]);

        foreach ($this->lessen as $les) {
            if (isset($les->lokalen_array)==false) $les->lokalen_array = Array();
            if ($les->dag == $dag && in_array($lokaal, $les->lokalen_array) && $les->GetStartTS() <= $tijdTS && $les->GetEindTS() > $tijdTS)

                if (!$plaintext) {
                    return "<span class='cel-eerste' style='color: blue;'>" . $les->docent
                        . "</span> "  . "<span class='cel-tweede'>" .  $les->GetKlassenShort(false) ."</span>";
                } else {
                    return $les->docent . ' ' . $les->GetKlassenShort(false);
                }


        }
        return "-";
    }

    function ZoekDocentEnLes($dag, $tijd, $klas, $plaintext = false) {
        if ($tijd == '-----') return "-";
        $timeparts = explode(':', $tijd);
        $tijdTS = mktime($hour = $timeparts[0], $minute=$timeparts[1]);

        foreach ($this->lessen as $les) {
            if ($les->dag == $dag && $les->GetStartTS() <= $tijdTS && $les->GetEindTS() > $tijdTS && in_array($klas, $les->klassen_array)  ) {
                $lesCode = (array_key_exists($les->lescode, $this->lesalias) ? $this->lesalias[$les->lescode]
                    : $les->lescode);
                if (!$plaintext) {
                    return "<span class='cel-eerste' style='color: blue;'>" . $lesCode . "</span> " . "<span class='cel-tweede'>" . $les->docent . "</span>";
                } else {
                    return $lesCode . ' ' . $les->docent;
                }
            }
        }

        return "'";
    }
}