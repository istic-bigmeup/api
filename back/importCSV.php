<?php
include("../connBD.php");

//Récupération du contenu du fichier
$file = $_FILES["file"]["tmp_name"];
$csv = file_get_contents($file);
$array_from_file = array_map("str_getcsv", explode("\n", $csv));

//Formation de l'entête
$header = $array_from_file[0][0];
$header = str_replace('"', '', $header);
$header_array = explode(",", $header);
$header_formated = array(); //Tableau qui contiendra les noms des champs dans la BD

//Correspondance de l'entête du fichier aux champs de la bd
foreach($header_array as $key => $val){
    switch($val){
        case 'first_name' : $header_formated[$key] = "nom";
        break;
        case 'last_name' : $header_formated[$key] = "prenom";
        break;
        case 'username' : $header_formated[$key] = "pseudo";
        break;
        case 'phone_number' : $header_formated[$key] = "telephone";
        break;
        case 'address' : $header_formated[$key] = "adresse";
        break;
        case 'email_address' : $header_formated[$key] = "email";
        break;
        case 'email_address_confirmed' : $header_formated[$key] = "confirmation_email";
        break;
        case 'joined_at' : $header_formated[$key] = "joined_at";
        break;
        case 'status' : $header_formated[$key] = "status";
        break;
        case 'is_admin' : $header_formated[$key] = "rang";
        break;
        case 'accept_emails_from_admin' : $header_formated[$key] = "acceptation_email_admin";
        break;
        case 'language' : $header_formated[$key] = "langue";
        break;
        default:
            $header_formated[$key] = $val;
    }
}
$header_formated[] = "ban"; //Rajouter le champ "ban" à la fin du tableau

//Formation du tableau final de données
$content = array();
$res = true;
foreach($array_from_file as $key => $row){
    if($key > 0){        
        foreach($row as $cle => $val){            
            $val = str_replace('"', '', $val); //suppression des côtes            
            if(strlen($val) > 0){
                $content_array = explode(",", $val);

                //formation du tableau à inserrer dans la BD
                foreach($content_array as $k => $v){                    
                    if($header_formated[$k] == "rang"){
                        $v = ($v == "true") ? "1" : "0";
                    }
                    $content[$header_formated[$k]] = $v;
                }
                $content["ban"] = "0"; //Rajouter le champ "ban" avec la valeur à 0

                //Importation dans la BD
                $where 	= array('pseudo' 	=> $content["pseudo"]);
                $test_presence = $users->findOne($where);//Vérif de la présence éventuelle

                if(is_null($test_presence)){
                    $content["_id"] = new MongoId();
                    $test = $users->insert($content);
                }
                else{
                    $replace = array('$set' => $content);
                    $test = $users->update($where, $replace);
                }

                //Vérification du resultat de la requête
                if($test["err"] == NULL){
                    $res = $res && true;
                }
                else{
                    $res = $res && false;
                }
            } 
        }
    }
}

// On envoie la réponse
if($res){
    echo json_encode(["answer" => "true"]);
} else {
    echo json_encode(["answer" => "false"]);
}