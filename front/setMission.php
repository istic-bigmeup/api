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
	
	// Validation du client
	$values["validation_client"] = htmlspecialchars($_POST["validation_client"]);
	
	// Validation du prestataire
	$values["validation_prestataire"] = htmlspecialchars($_POST["validation_prestataire"]);
	
	// Autres frais
	$values["autres_frais"] = htmlspecialchars($_POST["autres_frais"]);
	
	if(isset($_POST["ajoutMission"])){// Si on veut ajouter
		
		// Facture
		$facture_vals = array();
		//TODO Changer numéro de facture
		$facture_vals["numero_facture"] = $factures->count() . "";
		$facture_vals["date_facture"] 	= "";
		$facture_vals["date_prestation"]= htmlspecialchars($_POST["date_debut"]);
		$facture_vals["tva"]			= "0";
		$facture_vals["url"]			= "";
		$factures->insert($facture_vals);
		$values["facture"] = $facture_vals["_id"]->{'$id'};
		
		// Devis
		//TODO Changer numéro de devis
		$devis_vals = array();
		$devis_vals["numero_devis"] 	= $devis->count(). "";
		$devis_vals["date_devis"] 		= "";
		$devis_vals["date_paiement"]	= "";
		$devis_vals["penalite_retad"]	= "";
		$devis_vals["validite_devis"]	= "2 semaines";
		$devis_vals["url"]				= "";
		$devis->insert($devis_vals);
		$values["devis"] = $devis_vals["_id"]->{'$id'};
		
		$inserted = insert_data($missions, $champs, $values);
	} else if(isset($_POST["modifMission"])){// Si on veut modifier
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
		echo json_encode(["answer" => "true"]);
	} else {
		echo json_encode(["answer" => "false"]);
	}
} else if(isset($_POST["annulMission"])){// Annulation de la mission
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["annulMission"]));
	$replace 	= array('$set' 	=> array(
											"status" => "Annulée"
										));
	// On fait l'update
	$res = $missions->update($where, $replace);
	
	// On envoie la réponse
	if($res != false){
		echo json_encode(["answer" => "true"]);
	} else {
		echo json_encode(["answer" => "false"]);
	}
} else if(isset($_POST["refusMission"])){// Refus de la mission
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["refusMission"]));
	$replace 	= array('$set' 	=> array(
											"status" 					=> ($_POST["etatUtilisateur"] == "0" ? "Invalidée Prestataire" : "Invalidée Client"),
											"validation_prestataire"	=> ($_POST["etatUtilisateur"] == "0" ? "0" : "1"),
											"validation_client"			=> ($_POST["etatUtilisateur"] == "1" ? "0" : "1")
										));
	// On fait l'update
	$res = $missions->update($where, $replace);
	
	// On envoie la réponse
	if($res != false){
		echo json_encode(["answer" => "true"]);
	} else {
		echo json_encode(["answer" => "false"]);
	}
} else if(isset($_POST["validMission"])){// Validation de la mission
	// ================== Update de la mission =========================
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["validMission"]));
	$replace 	= array('$set' 	=> array(
											"status" 					=> "En attente de réalisation",
											"validation_prestataire"	=> "1",
											"validation_client"			=> "1"
										));
	// On fait l'update
	$res = $missions->update($where, $replace);
	
	// ================== Update du devis =========================
    $query = array('_id' => new MongoId($_POST["idDevis"]));
    $nom = from_table_to_json($devis->find($query));
	$nom = json_decode($nom, true);
	$nom = "DEV" . $nom[0]["numero_devis"];
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["idDevis"]));
	$replace 	= array('$set' 	=> array(
											"date_devis"=> date("Y-m-d"),
											"url"		=> "Devis_" . $nom . ".pdf"
										));
	// On fait l'update
	$res = $devis->update($where, $replace);
	
	// Génération du devis
	system("cd ../../pdf_generator;java -jar pdf_generator.jar devis " . $_POST["validMission"]);
	
	// On envoie la réponse
	if($res != false){
		echo json_encode(["answer" => "true"]);
	} else {
		echo json_encode(["answer" => "false"]);
	}
} else if(isset($_POST["realMission"])){// Réalisation de la mission
	// ================== Update de la mission =========================
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["realMission"]));
	$replace 	= array('$set' 	=> array(
											"status" 					=> "Réalisée"
										));
	// On fait l'update
	$res = $missions->update($where, $replace);
	
	// ================== Update de la facture =========================
    $query = array('_id' => new MongoId($_POST["idFac"]));
    $nom = from_table_to_json($factures->find($query));
	$nom = json_decode($nom, true);
	$nom = "FAC" . $nom[0]["numero_facture"];
	// Construction du where et du replace
    $where 		= array('_id' 	=> new MongoId($_POST["idFac"]));
	$replace 	= array('$set' 	=> array(
											"date_facture"	=> date("Y-m-d"),
											"url"			=> "Facture_" . $nom . ".pdf"
										));
	// On fait l'update
	$res = $factures->update($where, $replace);
	
	// Génération du devis
	system("cd ../../pdf_generator;java -jar pdf_generator.jar facture " . $_POST["realMission"]);
	
	// On envoie la réponse
	if($res != false){
		echo json_encode(["answer" => "true"]);
	} else {
		echo json_encode(["answer" => "false"]);
	}
}

?>