<?php

$connection = new MongoClient();
$db = $connection->bigmeup;

$missions   = $db->missions;
$users      = $db->users;
$annonces   = $db->annonces;

$documents  = $db->documents;
$bdc        = $db->bons_de_commandes;
$factures   = $db->factures;
$devis      = $db->devis;

function from_table_to_json($table){
    $array = array();

    foreach($table as $key => $val){
        array_push($array, $val);
    }

    return json_encode($array);
}

?>