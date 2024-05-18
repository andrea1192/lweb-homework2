<?php
	require_once("view.php");

	check_database();
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
		<div><a id="top"></a></div>
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
						<label for="user">Nome utente:</label>
						<input id="user" name="user" autofocus="autofocus" />
						<label for="pass">Password:</label>
						<input id="pass" name="pass" type="password" />
					</div>
					<div><input type="submit" value="Accedi" /></div>
				</form>
			</div>
		</div>
		<div id="footer">
			<?php generate_footer() ?>
		</div>
	</body>
	
</html>
