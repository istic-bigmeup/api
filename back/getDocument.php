<?php

include("../connBD.php");

if(isset($_GET["id"])){
	$query = array("_id" => new MongoId($_GET["id"]));
	echo from_table_to_json($documents->find($query));
}