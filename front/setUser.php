<?php

include("../connBD.php");

//Défnition des champs autorisés
$champs = array(
    "nom",
    "prenom",
    "pseudo",
    "email",
    "confirmation_email",
    "acceptation_email_admin",
    "langue",
    "mdp",
    "telephone",
    "photo_profil",
    "adresse",
    "status",
    "a_propos",
    "compte_paypal",
    "compte_facebook",
    "compte_linkedin",
    "nom_entreprise",
    "numero_siren",
    "numero_siret",
    "numero_tva"
);

if(isset($_POST["modifProfil"])){
	// Construction du where
    $where 		= array('_id' 	=> new MongoId($_POST["modifProfil"]));
	
	// On enlève l'id de l'utilisateur
	unset($_POST["modifProfil"]);
	
	// Construction du replace
	$replace 	= array('$set' 	=> $_POST);
	
	// On fait l'update
	$res = $users->update($where, $replace);
	
	// On envoie la réponse
	if($res != false){
		echo json_encode(["answer" => "true"]);
	} else {
		echo json_encode(["answer" => "false"]);
	}
}