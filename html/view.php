<?php
	require_once("controller.php");

	define('DEFAULT_ACTION', 'display.php');
	define('DEFAULT_CONTENT', 'main');

	$current = $_GET['page'] ?? DEFAULT_CONTENT;

	$message = '';
	$success = true;

	function generate_prolog() {

		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	}

	function generate_header() {
		$login = '<a href="login.php">Accedi</a>';
		$logout = '<a href="logout.php">Esci</a>';
		$logged_in = get_authorization() ? $logout : $login;

		$header = <<<END
			<div class="centered">
				<div id="title"><a href="display.php">Linguaggi per il Web</a></div>
				<div id="part">{$logged_in}</div>
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
				$href = generate_link($article['name']);
				print("<li><a {$href}>{$article['title']}</a></li>\n");
			}

			print("</ul>");
		}
	}

	function generate_link($page, $action = null) {
		global $current;

		$action = $action ?? DEFAULT_ACTION;
		$link = "href=\"{$action}?page={$page}\"";

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