<?php
/**
 * Created by PhpStorm.
 * User: NOYAF-PC
 * Date: 03/03/2017
 * Time: 14:21
 */

//Défnition des champs autorisés
$champs = array(
    "numero_devis",
    "date_devis",
    "date_paiement",
    "penalite_retad",
    "validite_devis",
    "url"
);

if(isset($_POST)){
    insert_data($devis, $champs, $_POST);
}