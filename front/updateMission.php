<?php
/**
 * Created by PhpStorm.
 * User: NOYAF-PC
 * Date: 03/03/2017
 * Time: 14:09
 */


//Défnition des champs autorisés
$champs = array(
    "id_prestataire",
    "id_client",
    "objet",
    "prix_unitaire_ht",
    "quantite",
    "date_debut",
    "date_fin",
    "clauses",
    "lieu_mission",
    "status",
    "validation_client",
    "validation_prestataire",
    "autres_frais",
    "facture",
    "devis"
);

if(isset($_POST["id"])){
    $critere = array("_id" => new MongoId($_POST["id"]));

    update_data($missions, $critere, $champs, $_POST);
}