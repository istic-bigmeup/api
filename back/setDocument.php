<?php

include("../connBD.php");

if(isset($_POST["valider"])){// Si validation
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["valider"]));
	$replace 	= array('$set' 	=> array(
											"date_enregistrement"	=> date("Y-m-d"),
											"date_echeance"			=> $_POST["date_echeance"],
											"verification"			=> "1"
										));
	// On fait l'update
	$res = $documents->update($where, $replace);
	
	// On envoie la réponse
	if($res != false){
		echo json_encode(["answer" => "true"]);
	} else {
		echo json_encode(["answer" => "false"]);
	}
} else if(isset($_POST["refus"])){// Sinon si refus
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["refus"]));
	$replace 	= array('$set' 	=> array(
											"date_enregistrement"	=> date("Y-m-d"),
											"date_echeance"			=> $_POST["date_echeance"],
											"verification"			=> "-1"
										));
	// On fait l'update
	$res = $documents->update($where, $replace);
	
	// On envoie la réponse
	if($res != false){
		echo json_encode(["answer" => "true"]);
	} else {
		echo json_encode(["answer" => "false"]);
	}
}