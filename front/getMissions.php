<?php

include("../connBD.php");

if(isset($_GET["id_user"])) {
    $id = $_GET["id_user"];
	
	// Getting the users
	$fields = array	(	"_id" 	=> 1,
						"email" => 1
					);
	$usr = from_table_to_json($users->find(array(), $fields));
	
	// Getting the client
	$query 	= array('id_client' => htmlspecialchars($_GET["id_user"]));
    $client = from_table_to_json($missions->find($query)->sort(array("_id" => -1, "date_derniere_modif" => -1)));
	
	// Getting the prestataire
	$query 	= array('id_prestataire' => htmlspecialchars($_GET["id_user"]));
    $presta = from_table_to_json($missions->find($query)->sort(array("_id" => -1, "date_derniere_modif" => -1)));
	
	$resTab = array();
	$resTab["client"] 	= $client;
	$resTab["presta"] 	= $presta;
	$resTab["usr"]		= $usr;
	
	echo json_encode($resTab);
} else if(isset($_GET["anpasse"])){
    $id = $_GET["id_user"];
	
	// Getting the users
	$fields = array	(	"_id" 	=> 1,
						"email" => 1
					);
	$usr = from_table_to_json($users->find(array(), $fields));
	
	// Getting the client
	$regex = new MongoRegex("/^" . (date("Y") - 1) . "/");
	$query 	= array(	'id_client' 			=> htmlspecialchars($_GET["id_user"]), 
						"date_derniere_modif"	=> $regex);
    $client = from_table_to_json($missions->find($query)->sort(array("_id" => -1, "date_derniere_modif" => -1)));
	
	// Getting the prestataire
	$query 	= array('id_prestataire' => htmlspecialchars($_GET["id_user"]));
    $presta = from_table_to_json($missions->find($query)->sort(array("_id" => -1, "date_derniere_modif" => -1)));
	
	$resTab = array();
	$resTab["client"] 	= $client;
	$resTab["presta"] 	= $presta;
	$resTab["usr"]		= $usr;
	
	echo json_encode($resTab);
}