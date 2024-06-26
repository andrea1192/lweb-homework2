<?php
	require_once("view.php");

	function print_content() {
		global $current;

		try {
			$article = get_article($current, encode: false);
			$edit_link = generate_link(view:'edit.php', page:$current);

		} catch (Exception $e) {
			return;
		}

		$content = <<<END
		<div id="article-header">
			<h1>{$article['title']}</h1>
			<a {$edit_link} title="Modifica questo articolo"></a>
		</div>
		{$article['text']}
		END;

		print($content);
	}

	check_database();
	check_actions($current);
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

					print_content();
				?>
			</div>
		</div>
		<div id="footer">
			<?php generate_footer() ?>
		</div>
	</body>
	
</html>
