<?php
/**
 * Created by PhpStorm.
 * User: NOYAF-PC
 * Date: 03/03/2017
 * Time: 14:47
 */

include("../connBD.php");

//Défnition des champs autorisés
$champs = array(
    "libelle",
    "date_enregistrement",
    "date_echeance",
    "verification",
    "id_user",
	"url"
);

//Chemin du repertoire de sauvegarde du fichier
$uploadDirectory = "../../documents/";

if(isset($_FILES['file'])){// Si on upload un fichier
	$file 	= $_FILES['file'];
	$type 	= $_POST["type"];
	$user_id= $_POST["user_id"];
	
	$nomFic = $type . $user_id . "-" . date("YmdHis") . "." . end((explode(".", $file["name"])));
	
	$tab 						= array();
	$tab["libelle"] 			= $type;
	$tab["date_enregistrement"]	= "";
	$tab["date_echeance"]		= "";
	$tab["verification"]		= "0";
	$tab["id_user"]				= $user_id;
	$tab["url"]					= $nomFic;
	
	$inserted = false;
	
	// On met le fichier dans le document d'upload
	if(move_uploaded_file($file["tmp_name"], $uploadDirectory . $tab["url"])){
		$inserted = insert_data($documents, $champs, $tab);
	}
	
	if($inserted){
		echo json_encode(["answer" => "true"]);
	} else {
		echo json_encode(["answer" => "false"]);
	}
}