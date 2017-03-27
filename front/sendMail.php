<?php
include("../connBD.php");

$send = false;

if(isset($_POST["email"])){
    $to = htmlspecialchars($_POST["email"]);
    $from = 'no_reply@bigmeup.istic.univ-rennes1.fr';

    $errorC = (!checkmymail($to)) ? true : false; //vérifie la conformité de l'email'

    if(!$errorC){
        //Recupértion de l'id de l'utilisateur
        $query = array("email" => $to);
        $json = from_table_to_json($users->find($query));
		$json = json_decode($json, true);

        //vérification de l'existence de l'utilsateur
		if(sizeof($json) > 0){
            $id = $json[0]["_id"]['$id']; //Récupération d'id de l'utilisateur
            $token = getToken(15); //génération du token

            //Enregistrement du token dans la bd
            $replace = array("mdpToken" => $token);
            $where = array('_id' => new MongoId($id));
            $res = $users->update($where, array('$set' => $replace));

            // On renvoie la réponse
            if($res){
                //Définition des paramètres du mail
                $subject = "(Re)initialisation du mot de passe";
                $message = "Cliquez sur le lien suivant pour (re)définir votre mot de passe: \n";
                $message .= "http://bigmeup.istic.univ-rennes1.fr/frontend/mdp_oublie.html?t=" . $token ;
                $headers = 'From: '. $from . "\r\n";
                $headers = 'Reply-to: '. $from . "\r\n";
                $headers.= 'MIME-Version: 1.0' . "\r\n";
                $headers .= "Content-Type: text/plain" . "\r\n";
                $headers = 'X-Mailer: PHP/' . phpversion();

                //Envoie du mail
                if(mail($to,$subject,$message,$headers)){
                    $send = true;
                }
            }
		}
    }
}

echo json_encode(["response" => $send]);


//Définition des fonctions
function getToken($size) {
    $string = "";
    $chaine = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    srand((double) microtime() * 1000000);
    
    for($i = 0; $i < $size; $i++) {
        $string .= $chaine[rand()%strlen($chaine)];
    }
    
    return $string;
}

function checkmymail($mailadresse){
	$email_flag = preg_match("!^\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$!", $mailadresse);
	return $email_flag;
}