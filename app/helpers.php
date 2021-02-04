<?php

function getShortNameCat($string){
    switch ($string) {
        case "monofocales":
            return [
                "code" => "MF",
                "rangoInf" => -10,
                "rangoSup" => 6,
                "cil" => -4
          ];
        case "monofocal digital DriveSafe":
            return [
                "code" => "MDSF",
                "rangoInf" => -10,
                "rangoSup" => 6,
                "cil" => -4
            ];
        case "monofocal digital superb":
            return [
                "code" => "MDSB",
                "rangoInf" => -10,
                "rangoSup" => 6,
                "cil" => -4
            ];
        case "monofocal digital individual":
            return [
                "code" => "MDI",
                "rangoInf" => -10,
                "rangoSup" => 6,
                "cil" => -4
            ];
        case "bifocales":
            return [
                "code" => "BF",
                "rangoInf" => -6,
                "rangoSup" => 6,
                "cil" => -4
            ];
        case "bifocal invisible":
            return [
                "code" => "BI",
                "rangoInf" => -6,
                "rangoSup" => 6,
                "cil" => -4
            ];
        case "progresivo basico":
        case "progresivo-basico":
            return [
                "code" => "PB",
                "rangoInf" => -6,
                "rangoSup" => 6,
                "cil" => -4
            ];
        case "progresivo digital individual":
        case "progresivo-digital-individual":
            return [
                "code" => "PDI",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        case "progresivo digital":
        case "progresivo-digital":
            return [
                "code" => "PD",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        case "progresivo digital plus":
        case "progresivo-digital-plus":
            return [
                "code" => "PDP",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        case "progresivo digital drive safe":
        case "progresivo-digital-drive-safe":
            return [
                "code" => "PDS",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        case "plastico":
            return [
                "code" => "CR",
                "rangoInf" => -3,
                "rangoSup" => 3,
                "cil" => -2.5
            ];
        case "policarbonato":
            return [
                "code" => "PL",
                "rangoInf" => -6,
                "rangoSup" => 6,
                "cil" => -4
            ];
        case "hi-index":
        case "hi index":
            return [
                "code" => "HI",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        case "antirreflejantes":
            return [
                "code" => "AR",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        case "photo":
            return [
                "code" => "PH",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        case "ar & photo":
        case "antirreflejante-photo":
            return [
                "code" => "ARPH",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        case "blanco":
            return [
                "code" => "BL",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        case "polarizado":
            return [
                "code" => "PO",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        case "bifocal invisible":
        case "bifocal-invisible":
            return [
                "code" => "BI",
                "rangoInf" => -3,
                "rangoSup" => 3,
                "cil" => -2
            ];
        case "monofocal drivesafe":
        case "monofocal-drivesafe":
        case "monofocal digital drivesafe":
        case "monofocal digital DriveSafe":
            return [
                "code" => "MDSF",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        case "monofocal superb":
        case "monofocal-superb":
        case "monofocal digital superb":
            return [
                "code" => "MDSU",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        case "monofocal individual":
        case "monofocal-individual":
        case "monofocal digital individual":
            return [
                "code" => "MDIN",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
        default:
            return [
                "code" => "XX",
                "rangoInf" => -15,
                "rangoSup" => 8,
                "cil" => -6
            ];
      }
}