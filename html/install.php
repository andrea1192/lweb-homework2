<?php
	require_once("connection.php");
	require_once("utils.php");

	$list = [
		'none' => [
			['path' => 'static/main.html', 'title' => 'Home']
		],

		'xhtml' => [
			['path' => 'static/elementi.html', 'title' => 'Elementi'],
			['path' => 'static/correttezza.html', 'title' => 'Correttezza']
		],

		'css' => [
			['path' => 'static/selettori.html', 'title' => 'Selettori'],
			['path' => 'static/box-model.html', 'title' => 'Box model'],
			['path' => 'static/layout.html', 'title' => 'Layout'],
			['path' => 'static/posizionamento.html', 'title' => 'Posizionamento']
		]
	];


	function generate_labels() {
		global $settings;

		foreach ($settings as $name => $details) {
			$label = $details['label'];

			if (isset($_POST[$name]) && $_POST['action'] != 'Reimposta') {

				$value = $_POST[$name];
			} else {

				$value = $details['value'];
			}
			
			$html = <<<END
			<label>{$label}: 
				<input name="{$name}" value="{$value}" readonly="readonly" />
			</label>
			END;

			print($html);
		}
	}

	if (isset($_POST['action'])) {

		switch ($_POST['action']) {

			case 'Installa': install(); 
				break;
			case 'Reimposta': 
				break;
			case 'Ripristina il database': restore(); 
				break;
			default: die ("Azione non valida.");
		}
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>Install Script</title>

		<link rel="stylesheet" href="css/install.css" type="text/css" />
	</head>

	<body>
		<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
			<div id="settings">
				<h1>Credenziali per il database</h1>
				<?= generate_labels() ?>
			</div>
			<div id="controls">
				<input type="submit" name="action" value="Installa" />
				<input type="submit" name="action" value="Reimposta" />
				<input type="submit" name="action" value="Ripristina il database" />
			</div>
		</form>
	</body>
	
</html>