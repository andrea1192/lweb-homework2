<?php
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
?>