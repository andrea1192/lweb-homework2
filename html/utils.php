<?php
	require_once("connection.php");

	$connection = null;

	function get_ext($file) {
		$pattern = '/([^\/]+)\.(.*)/';

		preg_match($pattern, $file, $matches);

		return $matches[2] ?? '';
	}

	function get_UID($file) {
		$pattern = '/([^\/]+)\.(.*)/';

		preg_match($pattern, $file, $matches);

		return $matches[1] ?? '';
	}

	function get_title($article) {
		$pattern = '/<h1>([[:alpha:] ]*)<\/h1>/';

		preg_match($pattern, $article, $matches);

		return $matches[1] ?? '';
	}

	function strip_title($article) {
		$title_pattern = '/^[[:space:]]*<h1>[[:alpha:] ]*<\/h1>/';
		$space_pattern = '/^[[:space:]]*/';

		$parts = preg_split($title_pattern, $article, 2);
		$parts = preg_split($space_pattern, $parts[1] ?? $parts[0], 2);

		return $parts[1] ?? $parts[0];
	}

	function scan_article($file) {
		$connection = connect();
		$text = file_get_contents($file); // Possible SQL injection

		$article = [];
		$article['name'] = get_UID($file);
		$article['title'] = get_title($text);
		$article['text'] = strip_title($text);

		return $article;
	}

	function insert_article($article) {
		$connection = connect();

		$article['title'] = $connection->real_escape_string($article['title']);
		$article['text'] = $connection->real_escape_string($article['text']);

		$sql = <<<END
		INSERT INTO Pages VALUES
		('{$article['name']}', 
			{$article['position']}, 
			'{$article['category']}', 
			'{$article['title']}', 
			'{$article['text']}');
		END;

		try {
			$connection->query($sql);

		} catch (mysqli_sql_exception $e) {
			log_error($e);
			throw $e;
		}
	}

	function load_categories($list) {
		$connection = connect();
		$i = 1;

		foreach ($list as $category => $articles) {

			$sql = "INSERT INTO Categories VALUES ('{$category}', {$i});";
			$connection->query($sql);

			$i++;
		}
	}

	function load_list($list) {
		$connection = connect();

		foreach ($list as $category => $articles) {

			foreach ($articles as $number => $article) {
				$row = scan_article($article['path']);

				$row['position'] = $number+1;
				$row['category'] = $category;
				$row['title'] = $article['title'];

				insert_article($row);
			}
		}
	}

	function connect() {
		global $connection;
		global $settings;

		$db_host = $settings['db_host'];
		$db_user = $settings['db_user'];
		$db_pass = $settings['db_pass'];
		$db_name = $settings['db_name'];

		try {
			return $connection ?? new mysqli($db_host, $db_user, $db_pass, $db_name);

		} catch (mysqli_sql_exception $e) {
			log_error($e);
			throw $e;
		}
	}

	function create_tables() {
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

		} catch (mysqli_sql_exception $e) {
			log_error($e);
			throw $e;
		}
	}

	function log_error($e) {
		msg_failure(
			"Errore del database: {$e->getMessage()} ({$e->getFile()}:{$e->getLine()})");
	}

	function install() {
		global $list;
		global $settings;

		try {
			$connection = connect();
			create_tables();

			load_categories($list);
			load_list($list);

		} catch (mysqli_sql_exception $e) {
			$errors = true;
		} 

		if (!isset($errors)) {
			msg_success("Database \"{$settings['db_name']}\" inizializzato.");
		}
	}

	function restore() {
		global $settings;

		try {
			$connection = connect();

			$sql = "DROP TABLE IF EXISTS Pages, Categories;";
			$connection->query($sql);

		} catch (mysqli_sql_exception $e) {
			$errors = true;
		} 

		if (!isset($errors)) {
			msg_success("Database \"{$settings['db_name']}\" ripristinato.");
		}
	}

	function get_categories() {
		$connection = connect();
		$sql = "SELECT name FROM Categories ORDER BY position;";
		$result = $connection->query($sql);

		$categories = [];

		while ($category = $result->fetch_column()) {
			$categories[] = $category;
		}

		return $categories;
	}

	function get_articles($category) {
		$connection = connect();
		$sql = "SELECT name,title FROM Pages WHERE category = '{$category}' ORDER BY position;";
		$result = $connection->query($sql);

		$articles = [];

		while ($article = $result->fetch_assoc()) {
			$articles[] = $article;
		}

		return $articles;
	}

	function get_article($article) {
		$connection = connect();
		$sql = "SELECT title,text FROM Pages WHERE name = '{$article}';";
		$result = $connection->query($sql);

		return $result->fetch_assoc();
	}

?>