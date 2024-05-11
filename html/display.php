<?php
	require_once("utils.php");

	define('DEFAULT_CONTENT', 'main');
	define('PAGE_PTR', 'page');
	define('ACTIVE_PAGE_CLASS', 'active');

	$const = get_defined_constants();
	$current = $_GET[PAGE_PTR] ?? DEFAULT_CONTENT;

	function generate_prolog() {

		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	}

	function generate_menu() {

		foreach (get_categories() as $category) {
			if ($category != 'none') {
				print("<h1>{$category}</h1>\n");
			}

			print("<ul>");

			foreach (get_articles($category) as $article) {
				$href = generate_link($article['name']);
				print("<li><a {$href}>{$article['title']}</a></li>\n");
			}

			print("</ul>");
		}
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
		global $current;

		$article = get_article($current);

		print("<h1>{$article['title']}</h1>");
		print($article['text']);
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
					<?php generate_menu() ?>
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
