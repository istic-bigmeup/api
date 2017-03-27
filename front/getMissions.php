<?php

include("../connBD.php");

if(isset($_GET["id_user"])) {
    $id = $_GET["id_user"];
	
	// Getting the users
	$fields = array	(	"_id" 	=> 1,
						"email" => 1
					);
	$usr = from_table_to_json($users->find([], $fields));
	
	// Getting the client
	$query 	= array('id_client' => htmlspecialchars($_GET["id_user"]));
    $client = from_table_to_json($missions->find($query));
	
	// Getting the prestataire
	$query 	= array('id_prestataire' => htmlspecialchars($_GET["id_user"]));
    $presta = from_table_to_json($missions->find($query));
	
	$resTab = array();
	$resTab["client"] 	= $client;
	$resTab["presta"] 	= $presta;
	$resTab["usr"]		= $usr;
	
	echo json_encode($resTab);
}