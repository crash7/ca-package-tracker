<?php
	/*
		Thanks StackOverflow for this snippet
	*/
	function generateRandomCaptcha($len) {
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $len; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}

	// Validate the input (min validation)
	$hascode = isset($_GET['code']);
	if($hascode) {
		if (preg_match('/^[a-zA-Z0-9]+$/', $_GET['code'])) {
			$trackingcode = $_GET['code'];
				
			// Fake user agents
			$user_agents = array(
				'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36',
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1'
			);
			
			/* cURL request */
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // output to var
			
			curl_setopt($curl, CURLOPT_URL, 'http://www.correoargentino.com.ar/sites/all/modules/custom/ca_forms/api/ajax.php'); // target url
			curl_setopt($curl, CURLOPT_REFERER, 'http://www.correoargentino.com.ar/formularios/oidn'); // let's fake the referer ;)
			curl_setopt($curl, CURLOPT_USERAGENT, $user_agents[array_rand($user_agents, 1)]); // fake some agent
			
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'X-Requested-With: XMLHttpRequest' // fake to be an ajax request ;)
			));
			
			curl_setopt($curl, CURLOPT_POST, true); // yep, post request
			
			// setup the info
			curl_setopt($curl, CURLOPT_POSTFIELDS, array(
				'id' => $trackingcode,
				'action' => 'oidn',
				'ct_captcha' => generateRandomCaptcha(6) // fake some captcha (just for fun)
			)); // the info!

			// actual request
			$response = curl_exec($curl);
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			curl_close($curl);
			/* End of cURL request */

			// Now do whatever you want - MAMBO! :D
			$response = preg_replace('/<p>Resultados.+?<\/p>/', '', $response); // we already know/show this one
			$response = preg_replace('/(?<=class=")table/', 'table table-hover table-condensed', $response); // fancier table
			$response = preg_replace('/<script>.+?<\/script>/', '', $response); // chau js call
			$response = preg_replace('/<button.+?<\/button>/', '', $response); // chau botón de imprimir, we love trees
			$response = preg_replace('/collapse/', '', $response); // chau nacional oculto
			$response = preg_replace('/accordion-heading/', 'hidden', $response); // chau accordion nacional
		} else {
			$hascode = false;
			$badcode = true;
		}
	}

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Paqueteame</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<link rel="icon" type="image/png" href="/favicon.png">
</head>
<body>

	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-header-navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">
					<span class="glyphicon glyphicon-ice-lolly-tasted"></span>&nbsp;Paquetea<strong>me</strong>
				</a>
			</div>

			<div class="collapse navbar-collapse hidden-print" id="bs-header-navbar">
			<?php if($hascode): ?>
				<form action="/index.php" method="GET" class="navbar-form navbar-right" role="search">
					<div class="form-group">
						<input type="text" name="code" class="form-control" placeholder="Tracking code..." autocomplete="on" value="<?php echo $trackingcode; ?>" title="XX123456789YY" pattern="[a-zA-Z]{2}[0-9]{9}[a-zA-Z]{2}">
					</div>
					<button type="submit" class="btn btn-default">Go!</button>
				</form>
			<?php endif; ?>
			</div>
		</div>
	</nav>

	<div class="container">
	<?php if(isset($badcode)): ?>
		<div class="alert alert-danger" role="alert"><strong>Invalid code</strong>. The tracking code is missing or is not valid.</div>
	<?php endif; ?>

	<?php if($hascode): ?>
	<div class="page-header">
		<h2>
		Package <strong class="text-info"><?php echo $trackingcode; ?></strong>
		<a class="small hidden-print" href="/index.php?code=<?php echo $trackingcode; ?>" title="Permalink">
			<span class="glyphicon glyphicon-link"></span>
		</a>
		</h2>
	</div>
	<?php echo $response; ?>
	<hr>
	<small class="pull-right text-muted"><strong>Status</strong> <?php echo $status; ?></small>
	<?php else: ?>
		<div class="jumbotron text-center">
			<h1>Welcome!</h1>
			<p>Enter your tracking code below and start tracking like the pros.</p>
			<form action="/index.php" method="GET" class="form-inline" role="search">
				<div class="form-group">
					<input type="text" name="code" class="form-control text-center" placeholder="Your tracking code..." title="XX123456789YY" autocomplete="on" pattern="[a-zA-Z]{2}[0-9]{9}[a-zA-Z]{2}" style="font-weight: bold;">
				</div>
				<button type="submit" class="btn btn-primary">Track it</button>
			</form>
		</div>
	<?php endif; ?>
	</div>
</body>
</html>