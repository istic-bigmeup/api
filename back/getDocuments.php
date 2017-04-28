<?php

include("../connBD.php");

// Getting the users
$fields = array	(	"_id" 	=> 1,
					"email" => 1
				);
$usr = from_table_to_json($users->find([], $fields));

// Getting the missions
$docs = $documents->find([]);
$docs = from_table_to_json($docs->sort(array("verification" => 1)));

$resTab = array();
$resTab["documents"] 	= $docs;
$resTab["users"] 		= $usr;

echo json_encode($resTab);