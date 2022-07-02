<?php
function normaliza($cadena)
{
    if (!$cadena) return;

    $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
    $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    $cadena = utf8_decode($cadena);
    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
    return utf8_encode($cadena);
}
function getItemsRequest($items, $branch_id = null)
{
    if ($items) {
        $itemsArray = is_string($items) ? json_decode($items, true) : $items;

        if (is_array($itemsArray)) {
            if ($branch_id) {
                foreach ($itemsArray as $key => $item) {
                    $itemsArray[$key]['branch_id'] = $branch_id;
                    $itemsArray[$key]['total'] = $item['cant'] * $item['price'];
                }
            }

            return $itemsArray;
        }
    }

    return [];
}
function getPaymentsRequest($payments, $branch_id = null)
{
    if ($payments) {
        $paymentsArray = is_string($payments) ? json_decode($payments, true) : $payments;

        if (is_array($paymentsArray)) {
            if ($branch_id) {
                foreach ($paymentsArray as $key => $item) {
                    $paymentsArray[$key]['branch_id'] = $branch_id;
                }
            }

            return $paymentsArray;
        }
    }

    return [];
}
function getParentCategories($item)
{
    $codeCategory = "0|0|0|0";
    $codeNameCategory = "|||";

    if ($item->parent) {
        if ($item->parent->parent) {
            if ($item->parent->parent->parent) {
                $codeCategory = $item->parent->parent->parent->id . "|" . $item->parent->parent->id . "|" . $item->parent->id . "|" . $item->id;
                $codeNameCategory = $item->parent->parent->parent->name . "|" . $item->parent->parent->name . "|" . $item->parent->name . "|" . $item->name;
            } else {
                $codeCategory = $item->parent->parent->id . "|" . $item->parent->id . "|" . $item->id . "|0";
                $codeNameCategory = $item->parent->parent->name . "|" . $item->parent->name . "|" . $item->name . "|";
            }
        } else {
            $codeCategory = $item->parent->id . "|" . $item->id . "|0|0";
            $codeNameCategory = $item->parent->name . "|" . $item->name . "||";
        }
    } else {
        $codeCategory =  "$item->id|0|0|0";
        $codeNameCategory = "$item->name|||";
    }

    return [
        "codeCategory" => explode("|", $codeCategory),
        "codeNameCategory" => explode("|", $codeNameCategory),
    ];
}
function getPaymentName($id)
{
    switch ($id) {
        case 1:
            return "efectivo";
        case 2:
            return "tarjeta debito";
        case 3:
            return "tarjeta de credito";
        case 4:
            return "la marina";
        case 5:
            return "cheque";
        case 6:
            return "transferencia";
        default:
            return "otro";
    }
}
function getShortNameCat($string)
{
    switch ($string) {
        case "monofocales":
            return [
                "code" => "MF",
                "rangoInf" => -10,
                "rangoSup" => 6,
                "cil" => -6
            ];
        case "monofocal digital DriveSafe":
            return [
                "code" => "MDSF",
                "rangoInf" => -10,
                "rangoSup" => 6,
                "cil" => -6
            ];
        case "monofocal digital superb":
            return [
                "code" => "MDSB",
                "rangoInf" => -10,
                "rangoSup" => 6,
                "cil" => -6
            ];
        case "monofocal digital individual":
            return [
                "code" => "MDI",
                "rangoInf" => -10,
                "rangoSup" => 6,
                "cil" => -6
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
