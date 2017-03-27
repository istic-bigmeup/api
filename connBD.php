<?php

$connection = new MongoClient();
$db = $connection->bigmeup;

$missions   = $db->missions;
$users      = $db->users;
$annonces   = $db->annonces;
$devis		= $db->devis;
$factures	= $db->factures;
$documents	= $db->documents;

function from_table_to_json($table){
    $array = array();

    foreach($table as $key => $val){
        array_push($array, $val);
    }

    return json_encode($array);
}

/**
 * Trie l'ensemble des données du document afin de ne garder que celle faissant parti du schema de la collection et les insert par la suite
 * @param $collection               Collection dans laquelle sera enregistré le document
 * @param array $champs_autorises   Ensemble des champs de la collection
 * @param array $document           Document candidat à l'enregistrement
 */
function insert_data($collection, array $champs_autorises, array $document){
    $tab_val = $document;
	
    /*
	ça fait bugger sinon
	foreach ($tab_val as $key=>$value){
        if(in_array($champs_autorises, $key)){ //Vérifie l'existence de la clé parmi les valeurs de champs autorisé
            unset($tab_val[$key]);
        }
    }
	*/

    if(!empty($tab_val)){
        $collection->insert($tab_val);
		
		return true;
    }
	
	return false;
}

/**
 * Trie l'ensemble des données du document afin de ne garder que celle faisant parti du schema de la collection et les met à jour par la suite suivant les critères
 * @param $collection               Collection dans laquelle sera enregistré le document
 * @param array $criteres            Critères de modification
 * @param array $champs_autorises   Ensemble des champs de la collection
 * @param array $document Document  contenant les données à mettre à jour
 */
function update_data($collection, array $criteres, array $champs_autorises, array $document){
    $tab_val = $document;
    /*foreach ($tab_val as $key=>$value){
        if(!in_array($champs_autorises, $key)){ //Vérifie l'existence de la clé parmi les valeurs de champs autorisé
            unset($tab_val[$key]);
        }
    }*/

    if(!empty($tab_val)){
        $collection->update($criteres, array('$set'=>$tab_val));
		
		return true;
    }
	
	return false;
}