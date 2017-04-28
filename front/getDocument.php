<?php

include("../connBD.php");

if(isset($_GET["id_user"])) {
    // Kbis
	$query 	= array('id_user' => $_GET["id_user"], 'libelle' => 'kbis');
	$kbis 	= from_table_to_json($documents->find($query));
	
	// Côtisations Sociales
    $query 	= array('id_user' => $_GET["id_user"], 'libelle' => 'cotSoc');
	$cotSoc = from_table_to_json($documents->find($query));
	
	// Côtisations Fiscales
    $query 		= array('id_user' => $_GET["id_user"], 'libelle' => 'cotFisc');
	$cotFisc 	= from_table_to_json($documents->find($query));
	
	// Déclaration du tableau de retour
	$res = array();
	$res["kbis"] 	= $kbis;
	$res["cotSoc"]	= $cotSoc;
	$res["cotFisc"] = $cotFisc;
	
	echo json_encode($res);
} else if(isset($_GET["controleValide"])){
	// Kbis
	$query 	= array('id_user' => $_GET["controleValide"], 'libelle' => 'kbis');
	$kbis 	= from_table_to_json(	$documents->
									find($query)->
									sort(array("_id" => -1))->
									limit(1));
	
	// Côtisations Sociales
    $query 	= array('id_user' => $_GET["controleValide"], 'libelle' => 'cotSoc');
	$cotSoc = from_table_to_json(	$documents->
									find($query)->
									sort(array("_id" => -1))->
									limit(1));
	
	// Côtisations Fiscales
    $query 		= array('id_user' => $_GET["controleValide"], 'libelle' => 'cotFisc');
	$cotFisc 	= from_table_to_json(	$documents->
										find($query)->
										sort(array("_id" => -1))->
										limit(1));
	
	$kbis = json_decode($kbis);
	$cotSoc = json_decode($cotSoc);
	$cotFisc = json_decode($cotFisc);
	
	if(sizeof($kbis) > 0){
		$kbis = $kbis[0]->verification == "1" ? "true" : "false";
	}else{
		$kbis = "false";
	}
	
	
	if(sizeof($cotSoc) > 0){
		$cotSoc = $cotSoc[0]->verification == "1" ? "true" : "false";
	}else{
		$cotSoc = "false";
	}
	
	
	if(sizeof(cotFisc) > 0){
		$cotFisc = $cotFisc[0]->verification == "1" ? "true" : "false";
	}else{
		$cotFisc = "false";
	}
	
	// Déclaration du tableau de retour
	$res = array();
	$res["kbis"] 	= $kbis;
	$res["cotSoc"]	= $cotSoc;
	$res["cotFisc"] = $cotFisc;
	
	echo json_encode($res);
}

?>