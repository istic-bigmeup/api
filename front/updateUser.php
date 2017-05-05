<?php
include("../connBD.php");

//Définition des champs à tester par défaut
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
    $res = false;

    //Teste la conformité du mdp
    $testSize = (strlen($mdp) >= 8) ? true : false;
    $matchingMin = preg_match("/[a-z]/", $mdp);
    $matchingMaj = preg_match("/[A-Z]/", $mdp);
    $matchingSpecialChar = preg_match("/[&@€£ùµ¢%#:;,=_'~\!\^\$\(\)\{\}\?\.\/\\\|]/", $mdp);

    if($testSize && $matchingMin && $matchingMaj && $matchingSpecialChar){
        $mdp = sha1($id.$mdp); //Hachage du mot de passe avec avec ajout d'un grain de sel

        // On défini les paramètres de la requête
        $where = array('_id' 	=> new MongoId($id));
        $replace = array("mdp" => $mdp);

        // On fait l'update
        $res = $users->update($where, array('$set' => $replace));
    }

	// On renvoie la réponse
	if($res){
		echo json_encode(array("response" => "true"));
	} else {
		echo json_encode(array("response" => "false"));
	}
}
else if(isset($_POST["token"]) && isset($_POST["mdp"])){
    $token = htmlspecialchars($_POST["token"]);
    $mdp = htmlspecialchars($_POST["mdp"]);

    //Récupération de l'id de l'utilisateur
    $query = array('mdpToken' => $token);
    $json = from_table_to_json($users->find($query));
    $user = json_decode($json, true);
    $id = $user[0]["_id"]['$id'];

    $mdp = sha1($id.$mdp); //Hachage du mot de passe avec avec ajout d'un grain de sel
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
