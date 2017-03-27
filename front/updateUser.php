<?php
include("../connBD.php");

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

if(isset($_POST["id"]) && isset($_POST["mdp"])){
    $id = htmlspecialchars($_POST["id"]);
    $mdp = htmlspecialchars($_POST["mdp"]);

    // On défini les paramètres de la requête
    $where = array('_id' 	=> new MongoId($id));
	$replace = array("mdp" => $mdp);

	// On fait l'update
	$res = $users->update($where, array('$set' => $replace));

	// On renvoie la réponse
	if($res){
		echo json_encode(["response" => "true"]);
	} else {
		echo json_encode(["response" => "false"]);
	}
}
else if(isset($_POST["token"]) && isset($_POST["mdp"])){
    $token = htmlspecialchars($_POST["token"]);
    $mdp = htmlspecialchars($_POST["mdp"]);
    $response = array("response" => "false");

	// On fait l'update
    $where = array("mdpToken" => $token);
	$replace = array("mdp" => $mdp);
	$res = $users->update($where, array('$set' => $replace));

	if($res){
        // On supprime le token de la bd
        $replace = array("mdpToken" => "");
        $res = $users->update($where, array('$unset' => $replace));
        
        $response["response"] = ($res) ? "true" : "false";
	} else {
		$response["response"] = "false";
	}

    echo json_encode($response);
}
else if( isset($_POST["id"]) ){
    $critere = array("_id" => new MongoId($_POST["id"]));

    update_data($users, $critere, $champs, $_POST);
}
