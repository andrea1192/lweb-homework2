<?php
	require_once("utils.php");
	require_once("view.php");

	function print_content() {
		global $current;

		$article = get_article($current);

		print("<h1>{$article['title']}</h1>");
		print($article['text']);
	}

	if (isset($_POST['title']) && isset($_POST['text'])) {
		$connection = connect();
		
		$article['name'] = $current;
		$article['title'] = $_POST['title'];
		$article['text'] = $_POST['text'];

		update_article($article);
	}

?>

<?= generate_prolog() ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>Linguaggi per il Web</title>

		<link rel="stylesheet" href="css/style.css" type="text/css" />
		<link rel="stylesheet" href="css/display.css" type="text/css" />
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
				<?php 
					if (isset($_POST['title']) && isset($_POST['text'])) {

						generate_message();
					}

					print_content() ?>
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
