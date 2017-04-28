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
	// Envoi du mail avec la raison
	$email = json_decode(from_table_to_json($documents->find(array('_id' => new MongoId($_POST["refus"])))));
	$email = json_decode(from_table_to_json($users->find(array("_id" => new MongoId($email[0]->id_user)))));
	$email = $email[0]->email;
	
	$raison = htmlspecialchars($_POST["raison_refus"]);
	
	// Header
	$headers = 'From: '. $MAIL_FROM . "\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= "Content-Type: text/plain" . "\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion();
	
	mail($email, "Refus de votre document", "Votre document a été refusé pour ce motif : " . $raison . "\nRendez vous sur http://bigmeup.istic.univ-rennes1.fr/frontend/document.html afin d'en importer autre.", $headers);
	
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["refus"]));
	$replace 	= array('$set' 	=> array(
											"date_enregistrement"	=> date("Y-m-d"),
											"date_echeance"			=> $_POST["date_echeance"],
											"verification"			=> "2",
											"raison_refus"			=> $raison
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