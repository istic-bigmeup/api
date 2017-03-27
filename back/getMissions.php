<?php

include("../connBD.php");

// Getting the users
$fields = array	(	"_id" 	=> 1,
					"email" => 1
				);
$usr = from_table_to_json($users->find([], $fields));

// Getting the missions
$missions = from_table_to_json($missions->find([]));

$resTab = array();
$resTab["missions"] 	= $missions;
$resTab["users"] 		= $usr;

echo json_encode($resTab);