<?php
	require_once("controller.php");

	define('DEFAULT_VIEW', 'display.php');
	define('DEFAULT_CONTENT', 'main');

	$current = $_GET['page'] ?? DEFAULT_CONTENT;

	$message = '';
	$success = true;

	function generate_prolog() {

		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	}

	function generate_header() {
		$home = generate_link();
		$login = '<a href="login.php">Accedi</a>';
		$logout = '<a href="logout.php">Esci</a>';
		$logged_in = get_authorization() ? $logout : $login;

		$header = <<<END
			<div class="centered">
				<div id="title"><a {$home}>Linguaggi per il Web</a></div>
				<div id="login">{$logged_in}</div>
			</div>
		END;

		print($header);
	}

	function generate_menu() {

		foreach (get_categories() as $category) {
			if ($category != 'none') {
				print("<h1>{$category}</h1>\n");
			}

			print("<ul>");

			foreach (get_articles($category) as $article) {
				$href = generate_link(page:$article['name']);
				print("<li><a {$href}>{$article['title']}</a></li>\n");
			}

			print("</ul>");
		}
	}

	function generate_link($view = DEFAULT_VIEW, $page = DEFAULT_CONTENT) {
		global $current;

		$href = rewrite_URL($view, page:$page);

		$link = "href=\"{$href}\"";

		if ($page == $current) {
			$link .= " class=\"active\"";
		}

		return $link;
	}

	function generate_message() {
		global $message;
		global $success;
		$class = $success ? 'success' : 'failure';

		$msg = <<<END
		<div class="mbox {$class}">
			$message
		</div>
		END;

		print($msg);
	}

	function set_message($msg, $sx) {
		global $message;
		global $success;
		$message = $msg;
		$success = $sx;
	}
?>