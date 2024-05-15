<?php
	require_once("view.php");

	check_actions($current);
?>

<?= generate_prolog() ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>Linguaggi per il Web</title>

		<link rel="stylesheet" href="css/style.css" type="text/css" />
		<link rel="stylesheet" href="css/login.css" type="text/css" />

	</head>

	<body>
		<a name="top"></a>
		<div id="header">
			<?php generate_header() ?>
		</div>
		<div id="wrapper" class="centered">
			<div id="menu">
				<div class="sticky">
					<?php generate_menu() ?>
				</div>
			</div>

			<div id="main">

				<?php
					if (!empty($message)) {

						generate_message();
					}

				?>

				<h1>Login</h1>
				<form action="<?= rewrite_URL($referrer, action:'login') ?>" method="post">
					<div id="fields">
						<label for="name">Nome utente:</label>
						<input name="user" autofocus="autofocus" />
						<label for="pass">Password:</label>
						<input type="pass" name="pass" />
					</div>
					<input type="submit" value="Accedi" />
				</form>
			</div>
		</div>
		<div id="footer">
			<div class="centered">
				<div>Andrea Ippoliti - matricola 1496769</div>
				<div><a href="#top">Torna su</a></div>
			</div>
		</div>
	</body>
	
</html>
