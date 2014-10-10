<!DOCTYPE html>
<?php // Laten we eerst de request eens analyseren

	//Controleer cookie en haal eventueel groepsinfo op
	$hasCookie = isset($_COOKIE["group"]);
	if($hasCookie) {
		$cookiename = $_COOKIE["group"];
		$filename = "groups/$cookiename.xml";
		$cookieCorrect = file_exists ( $filename );
		if ($cookieCorrect) {
			$groupdata=simplexml_load_file($filename);
		}
	}

	// Controleer de parameters uit de URL
	$hasParams = !empty($_GET);
	$correctParams = 0;
	$incorrectKeys = 0;
	$incorrectValues = 0;
	$indexes = array("trend" => 0, "pagina" => 1, "SLEUTEL" => 2, "apps" => 3, "php" => 4, "HBO-ICT_HOME-CODE" => 5);
	$values = array("trend" => "bigData", "pagina" => "expertise", "SLEUTEL" => "GEVONDEN", "apps" => "future", "php" => "zonnenet", "HBO-ICT_HOME-CODE" => "12ME9PAL");
	foreach ($_GET as $key => $value) { 
		if (array_key_exists($key, $values)) {
			// Er is een correcte key
			if ($values[$key] == $value) {
				// jeej, correct
				$correctParams++;
				// zet juiste stap op completed
				foreach ($groupdata->steps->step as $step) {
					if ($step->number == $indexes[$key])
						$step->completed = 1;
				}
			} else {
				$incorrectValues++;
			}
		} else {
			// Er is geen correcte key
			$incorrectKeys++;
		}
	}
	$hasCompleted = $correctParams==6 && $incorrectValues==0;
	if ($hasCompleted) {
		//mail
		$to      = 'daan.de.waard@hz.nl';
		$subject = 'Oplossing gevonden!';
		if ($hasCookie) {
			$message = 'Groep: ' . $cookiename;
		} else {
			$message = 'Anoniem';
		}
		$headers = 'From: inethunt_noreply@hz.nl' . "\r\n" .
			'Reply-To: inethunt_noreply@hz.nl' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();

		mail($to, $subject, $message, $headers);
	}
?>
<html>
	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <meta name="description" content="Internet hunt voor 1e jaars @HZICT studenten">
	    <meta name="author" content="waar0003">
	    <link rel="shortcut icon" href="http://hz.nl/Style%20Library/HogeschoolZeeland/favicon.ico">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>HZICT Internet Hunt</title>
	    <!-- Bootstrap -->
	    <link href="dist/css/bootstrap.min.css" rel="stylesheet" media="screen">
	
	    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
	      <script src="bootstrap-master/assets/js/html5shiv.js"></script>
	      <script src="bootstrap-master/assets/js/respond.min.js"></script>
	    <![endif]-->
	</head>
	<body>
		<div class="container" style="padding-top: 55px">
			<div class="jumbotron"><?php
				if($hasCompleted) {
					include("congratz.html");
				} else {
					if(!$hasCookie) {
						include("welcome.html");						
					} else {
						// Er is een cookie, dus user is bezig met uitwerken
						echo "<h1>Cookie: group=$cookiename</h1>";
						if(!$cookieCorrect) {
							echo '<div class="alert alert-danger" role="alert"><strong>FOUT!</strong> Dit is geen geldige Cookie </div>';
						} else {
							// Goede cookie.
							if ($incorrectKeys>0) {
								echo '<div class="alert alert-warning" role="alert"><strong>WAARSCHUWING!</strong> Er zitten foute keys in de parameters</div>';
							}
							if ($incorrectValues>0) {
								echo '<div class="alert alert-warning" role="alert"><strong>WAARSCHUWING!</strong> Er zitten foute values in de parameters</div>';
							}
							?>
							<p></p>
							<p>Aantal correcte parameters:</p>
							<div class="progress">
							  <div class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="<?php echo $correctParams ?>" aria-valuemin="0" aria-valuemax="6" style="width: <?php echo $correctParams*100/6 ?>%">
								<?php echo $correctParams ?>/6
							  </div>
							</div>
							<?php
						}
					} 
					if (!$hasCookie || !$cookieCorrect) {
						include("cookiebtn.html");
					}
				}
			?></div>
			<?php
			//De body van de pagina (onder de jumbotron)
			if($hasCompleted) {
				include("partylocation.html");
			} else {
				if ($hasCookie && $cookieCorrect)
					include("clues/".getCurrentClue($groupdata).".html");
			}

			function getCurrentClue($groupdata) {
				foreach ($groupdata->steps->step as $step) {
					if ($step->completed == 0) {
						return "clue$step->number";
					}
				}
				return "noclue";
			}
			
			?>
		</div>
	    <!-- Bootstrap core JavaScript ================================================== -->
	    <!-- Placed at the end of the document so the pages load faster -->
	    <script src="jquery-2.0.3.min.js"></script>
	    <script src="bootstrap-master/dist/js/bootstrap.min.js"></script>
	</body>
</html>

