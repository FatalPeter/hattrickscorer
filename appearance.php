<?php

	//lato model dello script...ho lo sha1 trovo l'id...
	require_once 'engine/database.php';
	require_once 'engine/constant.php';
	require_once 'engine/PHT/autoload.php';
		
	$userCode = $_GET["id"];
	$userId = -1;
	
	/*
		preferences
	*/
		
	/******/
	//Controllo se l'utente è valido e abilitato
	
	$result = $db->query("SELECT id FROM `user` WHERE `user_code` = '".$userCode."'");
	if($result->num_rows == 1) //codice fornito in get -> valido!
	{
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$userId = $row["id"];		
	}
	else
	{
		echo "Invalid userCode";
		exit();
	}
		
	/*****/
	//Utilità per la grafica
		
	$authParams = array();
	$result = $db->query("SELECT * FROM `auth` WHERE id = '".$userId."'");
	if($result)
	{
		$authParams = $result->fetch_array(MYSQLI_ASSOC);
    }
	
	//provo a fare una richiesta
	require 'engine/private.php';
	require 'engine/global.php';
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
	
	//informazioni generali
	$user = $HT->getUser();
	$team = $HT->getSeniorTeam();
	$teamId = $team->getId();
	$teamName = $team->getName();
	$logoUrl = $team->getLogoUrl();
			
	/********/

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Hattrick Scorer</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Scorer tool for Hattrick online game" />
	<meta name="keywords" content="hattrick, scorer, stats, strikers, goleador" />
	<meta name="author" content="SoichiroArima" />

  	<!-- Facebook and Twitter integration -->
	<meta property="og:title" content=""/>
	<meta property="og:image" content=""/>
	<meta property="og:url" content=""/>
	<meta property="og:site_name" content=""/>
	<meta property="og:description" content=""/>
	<meta name="twitter:title" content="" />
	<meta name="twitter:image" content="" />
	<meta name="twitter:url" content="" />
	<meta name="twitter:card" content="" />

	<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
	<link rel="shortcut icon" href="favicon.ico">

	<!-- <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'> -->
	
	<!-- Animate.css -->
	<link rel="stylesheet" href="css/animate.css">
	<!-- Icomoon Icon Fonts-->
	<link rel="stylesheet" href="css/icomoon.css">
	<!-- Bootstrap  -->
	<link rel="stylesheet" href="css/bootstrap.css">
	<!-- Superfish -->
	<link rel="stylesheet" href="css/superfish.css">

	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/responsive.css">


	<!-- Modernizr JS -->
	<script src="js/modernizr-2.6.2.min.js"></script>
	<!-- FOR IE9 below -->
	<!--[if lt IE 9]>
	<script src="js/respond.min.js"></script>
	<![endif]-->
	
	<!-- font awesome -->
	<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
	
	<!-- data tables -->
	<link rel="stylesheet" href="css/datatables.css" />	
	<link rel="stylesheet" href="css/datatables.bootstrap.css" />	
	
	</head>
	<body>
				
		<header id="fh5co-header-section" class="sticky-banner">
			<div class="container">
				<div class="nav-header">
					<a href="#fh5co-primary-menu" onclick="showMenu()" class="js-fh5co-nav-toggle fh5co-nav-toggle dark"><i></i></a>
					<h1 id="fh5co-logo"><a href="index.php">Hattrick Scorer</a></h1>
					<!-- START #fh5co-menu-wrap -->
					<nav id="fh5co-menu-wrap" role="navigation">
						<ul class="sf-menu" id="fh5co-primary-menu">
							<li><a href="bomberlist.php"><i class="fa fa-trophy"></i> BomberList</a></li>
							<li><a href="dashboard.php?id=<?php echo $userCode; ?>"><i class="fas fa-futbol"></i> Goals</a></li>
							<li class="active"><a href="appearance.php?id=<?php echo $userCode; ?>"><i class="far fa-sticky-note"></i> Appearance</a></li>
							<li><a href="global.php?id=<?php echo $userCode; ?>"><i class="far fa-clipboard"></i> General</a></li>
							<li><a href="#" onclick="unsetAndLogout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
						</ul>
					</nav>
				</div>
			</div>
		</header>
			
		<div id="top"></div>	
		<div id="fh5co-feature-product" class="fh5co-section-gray mg_dashboard">
			<div class="container">
				<div class="intest">
					<div class="team_logo" alt="TEAM LOGO"><img src="<?php echo $logoUrl; ?>" /></div>
					<div class="team_name"><?php echo $teamName; ?></div>
					<div class="team_id">(<?php echo $teamId; ?>)</div>
				</div>
				<div class="controls">
					<div class="scorer_category">Global</div>
					<div class="scorer_control scorer_active" id="official_appearance" onclick="showAppearance(this,'ufficiali','presenze','desc')">Official appearance</div>
					<div class="scorer_control" id="all_appearance" onclick="showAppearance(this,'tutti','presenze','desc')">All appearance</div>
				</div>		
				<div class="controls">
					<div class="scorer_category">Competition</div>
					<div class="scorer_control" id="league_appearance" onclick="showAppearance(this,'campionato','presenze','desc')">League appearance</div>
					<div class="scorer_control" id="cup_appearance" onclick="showAppearance(this,'coppa','presenze','desc')">Cup appearance</div>
					<div class="scorer_control" id="friendly_appearance" onclick="showAppearance(this,'amichevole','presenze','desc')">Friendly appearance</div>
					<div class="scorer_control" id="masters_appearance" onclick="showAppearance(this,'masters','presenze','desc')">Masters appearance</div>		
					<?php
						//temporaneamente disabilitato
						//<div class="scorer_control" id="national_appearance" onclick="showAppearance(this,'nazionale','presenze','desc')">National appearance</div>
					?>
				</div>
				<div class="loading_gears" id="loading_gears">
					<img src="images/gears_inverted.svg" />
				</div>								
				<div class="scorer_tables" id="scorer_append">
					
					
				</div>

				<div id="goToTop"><a href="#top">^<br/>TOP</div></div>
				
			</div>
		</div>
				
		<?php 
			//footer
			include("footer.php"); 
		?>
	

	</div>
	<!-- END fh5co-page -->

	</div>
	<!-- END fh5co-wrapper -->

	<!-- jQuery -->


	<script src="js/jquery.min.js"></script>
	<!-- jQuery Easing -->
	<script src="js/jquery.easing.1.3.js"></script>
	<!-- Bootstrap -->
	<script src="js/bootstrap.min.js"></script>
	<!-- Waypoints -->
	<script src="js/jquery.waypoints.min.js"></script>
	<script src="js/sticky.js"></script>

	<!-- Stellar -->
	<script src="js/jquery.stellar.min.js"></script>
	<!-- Superfish -->
	<script src="js/hoverIntent.js"></script>
	<script src="js/superfish.js"></script>
	
	<!-- Main JS -->
	<script src="js/main.js"></script>
	<script src="js/menu.js"></script>
	
	<!-- funzioni di utilità -->
	<input type="hidden" id="current_active" value="official_appearance" />
	<input type="hidden" id="current_request" value="ufficiali" />
	<input type="hidden" id="userCode" value="<?php echo $userCode; ?>" /> <?php //mi serve per il javascript ?>
	<script src='js/appearance.js'></script>
	
	<!-- data tables -->
	<script src="js/datatables.js"></script>

	</body>
</html>

