<?php
	define('DEFAULT_ACTION', 'display.php');
	define('DEFAULT_CONTENT', 'main');
	define('PAGE_PTR', 'page');
	define('ACTIVE_PAGE_CLASS', 'active');

	$const = get_defined_constants();
	$current = $_GET[PAGE_PTR] ?? DEFAULT_CONTENT;

	$message = '';
	$success = true;

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

		$link = "href=\"{$const['DEFAULT_ACTION']}?{$const['PAGE_PTR']}={$page}\"";

		if ($page == $current) {
			$link .= " class=\"{$const['ACTIVE_PAGE_CLASS']}\"";
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

	function msg_success($msg) {
		set_message($msg, true);
	}

	function msg_failure($msg) {
		set_message($msg, false);
	}

	function set_message($msg, $sx) {
		global $message;
		global $success;
		$message = $msg;
		$success = $sx;
	}
?>