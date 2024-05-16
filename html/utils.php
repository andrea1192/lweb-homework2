<?php
	require_once("controller.php");

	function rewrite_URL($URL, $view = null, $page = null, $action = null) { //utils
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

		return htmlspecialchars("{$URL_path}?{$URL_query}", ENT_XHTML);
	}

	function get_ext($file) { //utils
		$pattern = '/([^\/]+)\.(.*)/';

		preg_match($pattern, $file, $matches);

		return $matches[2] ?? '';
	}

	function get_UID($file) { //utils
		$pattern = '/([^\/]+)\.(.*)/';

		preg_match($pattern, $file, $matches);

		return $matches[1] ?? '';
	}

	function get_title($article) { //utils
		$pattern = '/<h1>([[:alpha:] ]*)<\/h1>/';

		preg_match($pattern, $article, $matches);

		return $matches[1] ?? '';
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
		$article['name'] = get_UID($file);
		$article['title'] = get_title($text);
		$article['text'] = strip_title($text);

		return $article;
	}

	function insert_list($list) { //model/setup
		$connection = connect();

		foreach ($list as $category => $articles) {

			foreach ($articles as $number => $article) {
				$row = scan_article($article['path']); //utils

				$row['position'] = $number+1;
				$row['category'] = $category;
				$row['title'] = $article['title'];

				insert_article($row); //model
			}
		}
	}

	function create_tables() { //model/setup
		$connection = connect();
		try {
			$sql = <<<'END'
			CREATE TABLE Categories (
			name 		VARCHAR(160)	PRIMARY KEY,
			position 	INT UNSIGNED 	UNIQUE);
			END;
			$connection->query($sql);

			$sql = <<<'END'
			CREATE TABLE Pages (
			name  		VARCHAR(160) 	PRIMARY KEY,
			position 	INT UNSIGNED 	NOT NULL,
			category 	VARCHAR(160) 	NOT NULL,
			title 		VARCHAR(160)	NOT NULL,
			text 		TEXT,
			UNIQUE(position, category),
			FOREIGN KEY(category) REFERENCES Categories(name));
			END;
			$connection->query($sql);

			$sql = <<<'END'
			CREATE TABLE Users (
			user 		VARCHAR(160)	PRIMARY KEY,
			pass 	 	VARCHAR(255)	NOT NULL);
			END;
			$connection->query($sql);

		} catch (mysqli_sql_exception $e) {
			log_error($e); //model
			throw $e;
		}
	}

	function create_user($username, $password) { //model/setup
		$connection = connect();

		$user['user'] = $username;
		$user['pass'] = password_hash($password, PASSWORD_DEFAULT);

		$sql = <<<END
		INSERT INTO Users VALUES
		('{$user['user']}', '{$user['pass']}');
		END;

		try {
			$connection->query($sql);

		} catch (mysqli_sql_exception $e) {
			log_error($e); //model
			throw $e;
		}
	}

	function install() { //setup
		global $list;
		global $settings;

		try {
			$connection = connect();		//model
			create_tables();				//model

			insert_categories($list);		//model
			insert_list($list);				//model

		} catch (mysqli_sql_exception $e) {
			$errors = true;
		} 

		if (!isset($errors)) {
			msg_success("Database \"{$settings['db_name']}\" inizializzato.");
		}
	}

	function restore() { //setup
		global $settings;

		try {
			$connection = connect(); //model

			$sql = "DROP TABLE IF EXISTS Pages, Categories, Users;";
			$connection->query($sql);

		} catch (mysqli_sql_exception $e) {
			$errors = true;
		} 

		if (!isset($errors)) {
			msg_success("Database \"{$settings['db_name']}\" ripristinato.");
		}
	}

?>