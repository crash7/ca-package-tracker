<!DOCTYPE HTML>
<html>
<head>
	<title>Paqueteame</title>
</head>
<body>
	<h1>Paquetea<strong>me</strong></h1>
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
		if(isset($_GET['code']) && preg_match('/^[a-zA-Z0-9]+$/', $_GET['code'])) {
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
			$result = curl_exec($curl);
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			curl_close($curl);
			/* End of cURL request */


			// The final info!
			// Now do whatever you want - MAMBO! :D
			echo "Tracking code <strong>$trackingcode</strong> requested.<br>";
			echo "Response status: <strong>$status</strong><br>";
			echo "<hr>";
			echo $result;

		} else {
			// Sorry cracker, sorry criollita.
			echo 'Sorry. The tracking code is not valid.';

		}

	?>
</body>
</html>