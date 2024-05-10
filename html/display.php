<?php
	define('DEFAULT_CONTENT', 'main');
	define('PAGE_PTR', 'page');
	define('PAGE_EXT', 'html');
	define('ACTIVE_PAGE_CLASS', 'active');

	$const = get_defined_constants();
	$current = $_GET[PAGE_PTR] ?? DEFAULT_CONTENT;

	function generate_prolog() {

		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	}

	function generate_link($page) {
		global $const;
		global $current;

		$link = "href=\"{$_SERVER['PHP_SELF']}?{$const['PAGE_PTR']}={$page}\"";

		if ($page == $current) {
			$link .= " class=\"{$const['ACTIVE_PAGE_CLASS']}\"";
		}

		return $link;
	}

	function print_content() {
		global $const;
		global $current;

		readfile("static/{$current}.{$const['PAGE_EXT']}");
	}

?>

<?= generate_prolog() ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>Linguaggi per il Web</title>

		<link rel="stylesheet" href="css/style.css" type="text/css" />
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
					<h1>XHTML</h1>
					<ul>
						<li><a <?= generate_link('elementi') ?>>Elementi</a></li>
						<li><a <?= generate_link('correttezza') ?>>Correttezza</a></li>
					</ul>
					<h1>CSS</h1>
					<ul>
						<li><a <?= generate_link('selettori') ?>>Selettori</a></li>
						<li><a <?= generate_link('box-model') ?>>Box model</a></li>
						<li><a <?= generate_link('layout') ?>>Layout</a></li>
						<li><a <?= generate_link('posizionamento') ?>>Posizionamento</a></li>
					</ul>
				</div>
			</div>

			<div id="main">
				<?php print_content() ?>
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
