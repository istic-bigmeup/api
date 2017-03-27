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
}

?>