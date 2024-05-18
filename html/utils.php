<?php
	require_once("controller.php");

	function rewrite_URL($URL, $view = null, $page = null, $action = null, $encode = true) { //utils
		$URL_path = parse_url($URL, PHP_URL_PATH);
		$URL_query = parse_url($URL, PHP_URL_QUERY);

		if (isset($view)) {
			$old_basename = basename($URL_path);
			$new_basename = $view;
			$URL_path = str_replace($old_basename, $new_basename, $URL_path);
		}

		if (isset($page) || isset($action)) {

			if (isset($URL_query)) {
				parse_str($URL_query, $args);
			} else {
				$args = [];
			}

			if (isset($page)) $args['page'] = $page;
			if (isset($action)) $args['action'] = $action;

			$URL_query = http_build_query($args);
		}

		if (!$encode)
			return "{$URL_path}?{$URL_query}";

		return htmlspecialchars("{$URL_path}?{$URL_query}", ENT_XHTML);
	}

	function get_title($article) { //utils
		$pattern = '/<h1>([[:alpha:] ]*)<\/h1>/';

		preg_match($pattern, $article, $matches);

		return $matches[1] ?? '';
	}

	function generate_UID($title) { //utils
		$pattern = '/[[:^alnum:]]+/';

		$UID = preg_replace($pattern, '-', $title);
		$UID = strtolower($UID);

		return $UID;
	}

	function fix_sample_code($text) { //utils
		$pattern = '/<code([^>]*)>(.*?)<\/code>/s';

		function replacement_code($matches) {
			$content = htmlspecialchars($matches[2], ENT_XHTML);
			$element = "<code{$matches[1]}>{$content}</code>";

			return $element;
		}

		return preg_replace_callback($pattern, 'replacement_code', $text);
	}

	function strip_title($article) { //utils
		$title_pattern = '/^[[:space:]]*<h1>[[:alpha:] ]*<\/h1>/';
		$space_pattern = '/^[[:space:]]*/';

		$parts = preg_split($title_pattern, $article, 2);
		$parts = preg_split($space_pattern, $parts[1] ?? $parts[0], 2);

		return $parts[1] ?? $parts[0];
	}

	function scan_article($file) { //utils
		$text = file_get_contents($file); // Possible SQL injection

		$article = [];
		$article['title'] = get_title($text);
		$article['text'] = strip_title($text);
		$article['name'] = generate_UID($article['title']);

		return $article;
	}

?>