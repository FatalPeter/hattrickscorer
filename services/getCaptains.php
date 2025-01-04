<?php

	/* file di servizio richiamato dal javascript che ritorna i giocatori di una determinata categoria */
	require_once '../engine/database.php';
	require_once '../engine/constant.php';
	require_once '../engine/PHT/autoload.php';
	include 'utilsCaptainsSingle.php'; //funzioni per compattare ed ordinare i risultati;
	include 'utilsCaptainsMultiple.php';
	
	//debug
	/*
	error_reporting(E_ALL | E_WARNING | E_NOTICE);
	ini_set('display_errors', TRUE);
	*/
	
	$userCode = $_GET["id"]; //sha1 dell'utente
	$request = $_GET["request"]; //campionato, coppa, amichevole, nazionale, masters
	$userId = -1;
	
	if($request == null || $request == "" || $userCode == null || $userCode == "")
		die("1.0");
	
	$result = $db->query("SELECT id FROM `user` WHERE `user_code` = '".$userCode."'");
	if($result->num_rows == 1) //codice fornito in get -> valido!
	{
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$userId = $row["id"];		
	}
	else
	{
		die("2.0");
	}
	
	$authParams = array();
	$result = $db->query("SELECT * FROM `auth` WHERE id = '".$userId."'");
	if($result)
	{
		$authParams = $result->fetch_array(MYSQLI_ASSOC);
    }
	
	//provo a fare una richiesta
	require '../engine/private.php';
	require '../engine/global.php';
	$config = array(
		'CONSUMER_KEY' => $CHPP_CONSUMER_KEY,
		'CONSUMER_SECRET' => $CHPP_CONSUMER_SECRET,
		'MEMCACHED_SERVER_IP' => $GLOBAL_MEMCACHED_SERVER_IP,
		'MEMCACHED_SERVER_PORT' => $GLOBAL_MEMCACHED_SERVER_PORT,
		'OAUTH_TOKEN' => $authParams["oauth_token"],
		'OAUTH_TOKEN_SECRET' => $authParams["oauth_token_secret"]
	);
	
	//classe principale
	$HT = new \PHT\PHT($config);
	$team = $HT->getSeniorTeam();
	
	//estraggo i giocatori attuali della squadra, per verificare se sono ancora attivi
	$teamPlayers = $team->getPlayers();
	$playersList = array();
	foreach($teamPlayers->getPlayers() as $key => $value)
	{
		$playersList[$value->getId()] = $value->getName();
	}
	
	//controllo la richiesta e l'ordinamento della tabella per poter procedere alla richiesta giusta
	
	//richiesta singola
	if($request == "campionato" || $request == "coppa" || $request == "amichevole" || $request == "masters")
	{
		//ricavo i dati salvati dal calculator solo per la singola categoria
		$result = $db->query("SELECT `c_".$request."`
							  FROM `captains`
							  WHERE `id` = '".$userId."'");
							  
		$array = $result->fetch_array(MYSQLI_ASSOC);
		$capCategory = json_decode($array["c_".$request],true);

		$capCategoryCompressed = compressCapArraySingle($capCategory,$playersList);
		echo json_encode($capCategoryCompressed);
	}	
	if($request == "ufficiali")
	{
		
		//ricavo i dati salvati dal calculator
		$result = $db->query("SELECT *
							  FROM `captains`
							  WHERE `id` = '".$userId."'");
							  
		$array = $result->fetch_array(MYSQLI_ASSOC);
		
		//ricavo i dati dell'ultima richiesta
		$capCampionato = json_decode($array["c_campionato"],true);
		$capCoppa = json_decode($array["c_coppa"],true);
		$capMasters = json_decode($array["c_masters"],true);
		
		$capGlobal = array();
							
		//compatto le strutture per mostrare la classifica (l'array globale è passato per indirizzo)
		compressCapArrayMultiple($capCampionato,$capGlobal);
		compressCapArrayMultiple($capCoppa,$capGlobal);
		compressCapArrayMultiple($capMasters,$capGlobal);
		
		$capGlobal = detectInTeam($playersList,$capGlobal);
		
		echo json_encode($capGlobal);		
	}
	if($request == "tutti")
	{
		//ricavo i dati salvati dal calculator
		$result = $db->query("SELECT *
							  FROM `captains`
							  WHERE `id` = '".$userId."'");
							  
		$array = $result->fetch_array(MYSQLI_ASSOC);
		
		//ricavo i dati dell'ultima richiesta
		$capCampionato = json_decode($array["c_campionato"],true);
		$capCoppa = json_decode($array["c_coppa"],true);
		$capMasters = json_decode($array["c_masters"],true);
		$capAmichevole = json_decode($array["c_amichevole"],true);
		
		$capGlobal = array();
							
		//compatto le strutture per mostrare la classifica (l'array globale è passato per indirizzo)
		compressCapArrayMultiple($capCampionato,$capGlobal);
		compressCapArrayMultiple($capCoppa,$capGlobal);
		compressCapArrayMultiple($capMasters,$capGlobal);
		compressCapArrayMultiple($capAmichevole,$capGlobal);
		
		$capGlobal = detectInTeam($playersList,$capGlobal);
		
		echo json_encode($capGlobal);		
	}
	
?>
