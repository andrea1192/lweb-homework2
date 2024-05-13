<?php
	require_once("utils.php");
	require_once("view.php");

	function authenticate_user($username, $password) {
		$connection = connect();
		$sql = "SELECT user,pass FROM Users WHERE user = '{$username}';";
		$stored = $connection->query($sql)->fetch_assoc();

		return password_verify($password, $stored['pass']);
	}

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
			<div class="centered">
				<div id="title"><a href="display.php">Linguaggi per il Web</a></div>
				<div id="part">P01: XHTML+CSS</div>
			</div>
		</div>
		<div id="wrapper" class="centered">
			<div id="menu">
				<div class="sticky">
					<?php generate_menu() ?>
				</div>
			</div>

			<div id="main">
				<h1>Login</h1>
				<?php
					if (isset($_GET['submit'])) {

						if (authenticate_user($_GET['user'], $_GET['pass']))
							print("<p>Credenziali corrette</p>");

						else
							print("<p>Credenziali sbagliate</p>");
					}

				?>
				<form action="" method="get">
					<div id="fields">
						<label for="name">Nome utente:</label>
						<input name="user" />
						<label for="pass">Password:</label>
						<input type="password" name="pass" />
					</div>
					<input type="submit" name="submit" value="Submit" /> 
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
