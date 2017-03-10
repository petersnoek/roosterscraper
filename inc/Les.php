<?php

class Les {
    public $docent;
    public $dag;            // ma di wo do vr
    public $datum;
    public $starttijd;
    public $eindtijd;
    public $minuten;
    public $halfuren;   // aantal lesblokken van 30 minuten
    public $lescode;
    public $lesblokken;
    public $klassen_array;
    public $lokalen_array;

    function __construct() {
        $this->klassen_array = array();
        $this->lokalen_array = array();
        $this->lesblokken = array();
    }

    public $replaceklassen = [
        'MBICO15M1' => '',

        'MBWZO15OA' => '',
        'MBWZO15P4A' => '',
        'MBVVO15ZM' => '',
        'MBWZO15M4B' => '',
        'MBWZO15M4C' => '',
        'MBZDO16AMA' => '',
        'MBWZO16QC' => '',
        'MBN2O16HEB' => '',
        'MBWZO16QA' => '',
        'MBWZO16QB' => '',
        'MBN2O16HEA' => '',
        'MBICO' => '',
        'MBADO' => '',
        'MBMAB' => '',
        'MBGOO' => '',
        'LPICO' => '',
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
    function GetKlassenShort($single = true) {
        if ( isset($this->klassen_array) && sizeof($this->klassen_array)>0) {
            $out = "";
            $counter = 1;
            foreach ($this->klassen_array as $k) {
                $klas = str_replace(array_keys($this->replaceklassen), array_values($this->replaceklassen), $k);
                //$klas = rtrim($klas, "0");
                //$klas = rtrim($klas, "1");
                if ($counter==1) {
                    $out .= $klas . ' ';
                }
                elseif ($single == true && $counter>=2)
                {
                    $out .= "..";
                }
                elseif ($single == false) {
                    $out .= $klas . ' ';
                }
                $counter++;
            }
            return rtrim($out, ' ');
        }
        else
            return '';
    }

    function __toString()
    {
        return sprintf('? ? ? ?-?', $this->docent, $this->dag, $this->lescode, $this->starttijd, $this->eindtijd);
    }
}