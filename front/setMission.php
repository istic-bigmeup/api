<?php
/**
 * Created by PhpStorm.
 * User: NOYAF-PC
 * Date: 03/03/2017
 * Time: 13:58
 */
include("../connBD.php");

//Défnition des champs autorisés
$champs = array(
    "id_prestataire",
    "id_client",
    "objet",
    "prix_unitaire_ht",
    "quantite",
    "date_debut",
    "date_fin",
    "clauses",
    "lieu_mission",
    "status",
    "validation_client",
    "validation_prestataire",
    "autres_frais",
    "facture",
    "devis"
);

// Variables pour l'envoi de mail
$mailClient 		= null;
$mailPrestataire 	= null;
$etatDeLaMission 	= null;
$idMission			= null;

if(isset($_POST["ajoutMission"]) || isset($_POST["modifMission"])){// Création / Modification d'une mission
	$values = array();
	
	if(isset($_POST["ajoutMission"])){// Si on est dans le cas de l'ajout d'une mission
		// On met l'ID du prestataire
		$values["id_prestataire"] = $_POST["id_prestataire"];
	}
	
	// L'id du client
	$query = array("email" => htmlspecialchars($_POST["mailClient"]));
	$idClient = from_table_to_json($users->find($query));
	$idClient = json_decode($idClient, true);
	$idClient = $idClient[0]["_id"]['$id'];
	$values["id_client"] = $idClient;
	
	// L'objet
	$values["objet"] = htmlspecialchars($_POST["objet"]);
	
	// Prix unitaire
	$values["prix_unitaire_ht"] = htmlspecialchars($_POST["prix_unitaire_ht"]);
	
	// Quantité
	$values["quantite"] = htmlspecialchars($_POST["quantite"]);
	
	// Date de début
	$values["date_debut"] = htmlspecialchars($_POST["date_debut"]);
	
	// Date de fin
	$values["date_fin"] = htmlspecialchars($_POST["date_fin"]);
	
	// Clauses
	$values["clauses"] = htmlspecialchars($_POST["clauses"]);
	
	// Lieu de mission
	$values["lieu_mission"] = htmlspecialchars($_POST["lieu_mission"]);
	
	// Statut
	$values["status"] = htmlspecialchars($_POST["status"]);
	$etatDeLaMission = $values["status"];
	
	// Validation du client
	$values["validation_client"] = htmlspecialchars($_POST["validation_client"]);
	
	// Validation du prestataire
	$values["validation_prestataire"] = htmlspecialchars($_POST["validation_prestataire"]);
	
	// Autres frais
	$values["autres_frais"] = htmlspecialchars($_POST["autres_frais"]);
	
	if(isset($_POST["ajoutMission"])){// Si on veut ajouter
		// Facture
		$facture_vals = array();
		$facture_vals["numero_facture"] = $factures->count() . "";
		$facture_vals["date_facture"] 	= "";
		$facture_vals["date_prestation"]= htmlspecialchars($_POST["date_debut"]);
		$facture_vals["tva"]			= "0";
		$facture_vals["url"]			= "";
		$facture_vals["id_client"]		= $idClient;
		$factures->insert($facture_vals);
		$values["facture"] = $facture_vals["_id"]->{'$id'};
		
		// Devis
		$devis_vals = array();
		$devis_vals["numero_devis"] 	= $devis->count(). "";
		$devis_vals["date_devis"] 		= "";
		$devis_vals["date_paiement"]	= "";
		$devis_vals["penalite_retad"]	= "";
		$devis_vals["validite_devis"]	= "2 semaines";
		$devis_vals["url"]				= "";
		$devis_vals["id_prestataire"]	= $_POST["id_prestataire"];
		$devis->insert($devis_vals);
		$values["devis"] = $devis_vals["_id"]->{'$id'};
		
		// Date de la dernière modif
		$values["date_derniere_modif"] = date("Y-m-d");
		
		$missions->insert($values);
		$idMission = $values["_id"]->{'$id'};
		try{
			getMailsFromMissionId($values["_id"]->{'$id'}, $mailClient, $mailPrestataire, $missions, $users);
			$inserted = true;
		} catch(Exception $error){
			$inserted = false;
		}
	} else if(isset($_POST["modifMission"])){// Si on veut modifier
		$idMission = $_POST["modifMission"];
		getMailsFromMissionId($_POST["modifMission"], $mailClient, $mailPrestataire, $missions, $users);
		
		// Date de la dernière modif
		$values["date_derniere_modif"] = date("Y-m-d");
		
		// Construction du where et du replace
		$where 		= array('_id' 	=> new MongoId($_POST["modifMission"]));
		$replace 	= array('$set' 	=> $values);
		
		// On fait l'update
		$inserted = $missions->update($where, $replace);
		
		$inserted = ($inserted != false);
	} else {// Sinon
		$inserted = false;
	}
	
	if($inserted){
		echo json_encode(array("answer" => "true"));
	} else {
		echo json_encode(array("answer" => "false"));
	}
} else if(isset($_POST["annulMission"])){// Annulation de la mission
	$idMission = $_POST["annulMission"];
	$etatDeLaMission = "Annulée";
	getMailsFromMissionId($_POST["annulMission"], $mailClient, $mailPrestataire, $missions, $users);
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["annulMission"]));
	$replace 	= array('$set' 	=> array(
											"status" 				=> "Annulée",
											"date_derniere_modif"	=> date("Y-m-d")
										));
	// On fait l'update
	$res = $missions->update($where, $replace);
	
	// On envoie la réponse
	if($res != false){
		echo json_encode(array("answer" => "true"));
	} else {
		echo json_encode(array("answer" => "false"));
	}
} else if(isset($_POST["refusMission"])){// Refus de la mission
	$idMission = $_POST["refusMission"];
	$etatDeLaMission = ($_POST["etatUtilisateur"] == "0" ? "Invalidée Prestataire" : "Invalidée Client");
	getMailsFromMissionId($_POST["refusMission"], $mailClient, $mailPrestataire, $missions, $users);
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["refusMission"]));
	$replace 	= array('$set' 	=> array(
											"status" 					=> ($_POST["etatUtilisateur"] == "0" ? "Invalidée Prestataire" : "Invalidée Client"),
											"validation_prestataire"	=> ($_POST["etatUtilisateur"] == "0" ? "0" : "1"),
											"validation_client"			=> ($_POST["etatUtilisateur"] == "1" ? "0" : "1"),
											"date_derniere_modif"		=> date("Y-m-d")
										));
	// On fait l'update
	$res = $missions->update($where, $replace);
	
	// On envoie la réponse
	if($res != false){
		echo json_encode(array("answer" => "true"));
	} else {
		echo json_encode(array("answer" => "false"));
	}
} else if(isset($_POST["validMission"])){// Validation de la mission
	$idMission = $_POST["validMission"];
	$etatDeLaMission = "En attente de réalisation";
	getMailsFromMissionId($_POST["validMission"], $mailClient, $mailPrestataire, $missions, $users);
	// ================== Update de la mission =========================
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["validMission"]));
	$replace 	= array('$set' 	=> array(
											"status" 					=> "En attente de réalisation",
											"validation_prestataire"	=> "1",
											"validation_client"			=> "1",
											"date_derniere_modif"		=> date("Y-m-d")
										));
	// On fait l'update
	$res = $missions->update($where, $replace);
	
	// ================== Le nom et prénom du prestataire ==================
	$usr 		= from_table_to_json($users->find(array("email" => $mailPrestataire)));
	$usr 		= json_decode($usr, true);
	$usrId		= $usr[0]["_id"]['$id'];
	$usrPrenom 	= unaccent($usr[0]["prenom"]);
	$usr		= unaccent($usr[0]["nom"]);
	
	if(strlen($usr) <= 4){
		$usr = $usr . substr($usrPrenom, 0, (5 - strlen($usr)));
	} else {
		$usr = substr($usr, 0, 5);
	}
	
	// ================== L'année et le mois ======================
	$date = date("Ym");
	
	// ================== Numéro de devis de ce mois-ci ===========
	$regex = new MongoRegex("/^" . date("Y-m-") . "/");
	$numDevis = $devis->count(array("date_devis" => $regex, "id_prestataire" => $usrId)) . "";
	
	// On le fout sous la forme 0001
	if(strlen($numDevis) < 4){
		$tmp = "";
		
		for($i = 0; $i < (4 - strlen($numDevis)) ; $i++){
			$tmp .= "0";
		}
		
		$tmp .= $numDevis;
		$numDevis = $tmp;
	}
	
	// ================== Update du devis =========================
	$nom = strtoupper($usr . $date . $numDevis);
	
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["idDevis"]));
	$replace 	= array('$set' 	=> array(
											"numero_devis"	=> $nom,
											"date_devis"	=> date("Y-m-d"),
											"url"			=> "DEV" . $nom . ".pdf"
										));
	// On fait l'update
	$res = $devis->update($where, $replace);
	
	// Génération du devis
	system("cd ../../pdf_generator;java -jar pdf_generator.jar devis " . $_POST["validMission"]);
	
	// On envoie la réponse
	if($res != false){
		echo json_encode(array("answer" => "true"));
	} else {
		echo json_encode(array("answer" => "false"));
	}
} else if(isset($_POST["realMission"])){// Réalisation de la mission
	$idMission = $_POST["realMission"];
	$etatDeLaMission = "Réalisée";
	getMailsFromMissionId($_POST["realMission"], $mailClient, $mailPrestataire, $missions, $users);
	// ================== Update de la mission =========================
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["realMission"]));
	$replace 	= array('$set' 	=> array(
											"status" 					=> "Réalisée",
											"date_derniere_modif"		=> date("Y-m-d")
										));
	// On fait l'update
	$res = $missions->update($where, $replace);
	
	// ================== Le nom et prénom du client ==================
	$usr 		= from_table_to_json($users->find(array("email" => $mailClient)));
	$usr 		= json_decode($usr, true);
	$usrId		= $usr[0]["_id"]['$id'];
	$usrPrenom 	= unaccent($usr[0]["prenom"]);
	$usr		= unaccent($usr[0]["nom"]);
	
	if(strlen($usr) <= 4){
		$usr = $usr . substr($usrPrenom, 0, (5 - strlen($usr)));
	} else {
		$usr = substr($usr, 0, 5);
	}
	
	// ================== L'année et le mois ======================
	$date = date("Ym");
	
	// ================== Numéro de la facture de ce mois-ci ===========
	$regex = new MongoRegex("/^" . date("Y-m-") . "/");
	$numFac = $factures->count(array("date_facture" => $regex, "id_client" => $usrId)) . "";
	
	// On le fout sous la forme 0001
	if(strlen($numFac) < 4){
		$tmp = "";
		
		for($i = 0; $i < (4 - strlen($numFac)) ; $i++){
			$tmp .= "0";
		}
		
		$tmp .= $numFac;
		$numFac = $tmp;
	}
	
	// ================== Update de la facture =========================
	$nom = strtoupper($usr . $date . $numFac);
	
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["idFac"]));
	$replace 	= array('$set' 	=> array(
											"numero_facture"=> $nom,
											"date_facture"	=> date("Y-m-d"),
											"url"			=> "FAC" . $nom . ".pdf"
										));
	// On fait l'update
	$res = $factures->update($where, $replace);
	
	// Génération du devis
	system("cd ../../pdf_generator;java -jar pdf_generator.jar facture " . $_POST["realMission"] . ";java -jar pdf_generator.jar bigmeup " . $_POST["realMission"]);
	
	// On envoie la réponse
	if($res != false){
		echo json_encode(array("answer" => "true"));
	} else {
		echo json_encode(array("answer" => "false"));
	}
}

// Envoi des mails si toutes les variables sont OK
if(	$mailClient != null && $mailPrestataire != null && $etatDeLaMission != null && $idMission != null){
	// Construction du texte
	$texte = "La mission ayant pour client " . $mailClient . " et comme prestataire " . $mailPrestataire . " est passée à l'état : " . $etatDeLaMission . "\nRendez vous sur cette page afin de consulter la missions : http://administration.bigmeup.fr/creationMission.html#msn" . $idMission;
	
	// Header
	$headers = 'From: '. $MAIL_FROM . "\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= "Content-Type: text/plain" . "\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion();
	
	// Envoi du mail au client
	mail($mailClient		, "Changement de l'état d'une mission [" . $mailPrestataire . "]"	, $texte, $headers);
	
	// Envoi du mail au prestataire
	mail($mailPrestataire	, "Changement de l'état d'une mission [" . $mailClient . "]"		, $texte, $headers);
}

/**
 * Permet de prendre les mails du client et du prestataire d'une mission
 * @param	missionId		L'id de la mission
 * @param	mailClient		Le mail du client à retourner
 * @param	mailPresta		Le mail du prestataire à retourner
 * @param	tableMission	La table des missions
 * @param	tableUsers		La table des utilisateurs
 */
function getMailsFromMissionId($missionId, &$mailClient, &$mailPresta, $tableMissions, $tableUsers){
	// On prend la mission
	$query 		= array('_id' => new MongoId($missionId));
    $msn 		= from_table_to_json($tableMissions->find($query));
	$msn 		= json_decode($msn, true);
	$idClient 	= $msn[0]["id_client"];
	$idPresta 	= $msn[0]["id_prestataire"];
	
	// On regarde le mail du client
	$query 		= array('_id' => new MongoId($idClient));
    $clt 		= from_table_to_json($tableUsers->find($query));
	$clt 		= json_decode($clt, true);
	$mailClient = $clt[0]["email"];
	
	// On regarde le mail du prestataire
	$query 		= array('_id' => new MongoId($idPresta));
    $clt 		= from_table_to_json($tableUsers->find($query));
	$clt 		= json_decode($clt, true);
	$mailPresta = $clt[0]["email"];
}

/**
 * Permet d'enlever l'accent
 */
function unaccent($string)
{
	return html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8')), ENT_QUOTES, 'UTF-8');
}

?>