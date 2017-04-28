<?php

include("../connBD.php");

if(isset($_GET["id"]) && isset($_GET["mdp"])){ //Si on vérifie le mot de passe d'un utilisateur'
    $id  = htmlspecialchars($_GET["id"]);
    $mdp    = htmlspecialchars($_GET["mdp"]);

    if((strlen($mdp) > 0) && (strlen($id) > 0)) {
        $mdp = sha1($id.$mdp); //Hachage du mot de passe avec avec ajount d'un grain de sel'

        $query = array(
                    '_id' => new MongoId($id),
                    'mdp' => $mdp
                );
        $json = from_table_to_json($users->find($query));
        $json = json_decode($json, true);

        if (sizeof($json) > 0) {
            if($mdp == $json[0]["mdp"]){
                echo json_encode(["answer" => "true"]);
                exit();
            }
        }
    }
    echo json_encode(["answer" => "false"]);   
}
else if(isset($_GET["id"])) {// Si on cherche un user via son id
    $id = $_GET["id"];
    $query = array('_id' => new MongoId($id));

    echo from_table_to_json($users->find($query));
} 
else if(isset($_GET["login"])){// Si on cherche un user via son login
    $login  = htmlspecialchars($_GET["login"]);
    $mdp    = htmlspecialchars($_GET["mdp"]);

    if(strlen($mdp) > 0) {
        //Récupération de l'id de l'utilisateur
        $query = array('email' => $login);
        $json = from_table_to_json($users->find($query));
        $user = json_decode($json, true);
        $id = $user[0]["_id"]['$id'];

        $mdp = sha1($id.$mdp); //Hachage du mot de passe avec ajout d'un grain de sel

        $query = array("email" => $login, "ban" => "0");
        $json = from_table_to_json($users->find($query));
        $json = json_decode($json, true);

        if (sizeof($json) > 0) {
            if($mdp == $json[0]["mdp"]){
                echo json_encode([	
                    "answer" 	=> "true",
                    "id" 		=> $json[0]["_id"]['$id'],
                    "rang"		=> $json[0]["rang"]
                ]);
                exit();
            }
            var_dump($json[0]["mdp"], $mdp, $mdp == $json[0]["mdp"]);
            exit();
        }
    }
    echo json_encode(["answer" => "false"]);
}
else if(isset($_GET["mail"])){// Si on cherche un user via son mail
	$mail = htmlspecialchars($_GET['mail']);
	
	if(strlen($mail) > 0){
		$query = array("email" => $mail);
		$json = from_table_to_json($users->find($query));
		$json = json_decode($json, true);

		if(sizeof($json) > 0){
			echo json_encode(["answer" => "true", "id" => $json[0]["_id"]['$id']]);
			exit();
		}
	}
	echo json_encode(["answer" => "false"]);
}