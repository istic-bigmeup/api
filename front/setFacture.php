<?php
/**
 * Created by PhpStorm.
 * User: NOYAF-PC
 * Date: 03/03/2017
 * Time: 14:21
 */

//Défnition des champs autorisés
$champs = array(
    "numero_facture",
    "date_facture",
    "date_prestation",
    "tva",
    "url"
);

if(isset($_POST)){
    insert_data($factures, $champs, $_POST);
}