<html>
	<head>
		<title>OurChoice</title>
	</head>
	<body>
		<h1 align="center">Our Choice</h1>
		<h3>Error</h3>
		<p>Sorry for the Inconvenience</p>
		<?php 
			if(isset($_GET['msg'])){											//If any error occurs in remaining pages,display it here and start again
				$error = intval($_GET['msg']);
				echo $error;
			}
		?>
		<br/><br/>
		<a href="index.php">Login</a>											<!--  Link to login page   -->
	</body>
</html>