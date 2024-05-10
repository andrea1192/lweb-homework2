<?php

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

	function scan_article($file, $target_connection) {
		$text = file_get_contents($file); // Possible SQL injection

		$name = get_UID($file);
		$title = get_title($text);
		$text = strip_title($text);

		$article = [];

		$article['name'] = $name;
		$article['title'] = $target_connection->real_escape_string($title);
		$article['text'] = $target_connection->real_escape_string($text);

		return $article;
	}

	function insert_article($article, $target_connection) {
		$sql = <<<END
		INSERT INTO Pages VALUES
		('{$article['name']}', 
			{$article['position']}, 
			'{$article['category']}', 
			'{$article['title']}', 
			'{$article['text']}');
		END;

		try {
			$target_connection->query($sql);

		} catch (mysqli_sql_exception $e) {
			print_error($e);
		}
	}

	function load_categories($list, $target_connection) {
		$i = 1;

		foreach ($list as $category => $articles) {

			$sql = "INSERT INTO Categories VALUES ('{$category}', {$i});";

			$target_connection->query($sql);

			$i++;
		}
	}

	function load_list($list, $target_connection) {

		foreach ($list as $category => $articles) {

			foreach ($articles as $number => $article) {

				$row = scan_article($article['path'], $target_connection);

				$row['position'] = $number+1;
				$row['category'] = $category;
				$row['title'] = $article['title'];

				insert_article($row, $target_connection);
			}
		}
	}

	function connect() {
		$db_host = $_POST['db_host'];
		$db_user = $_POST['db_user'];
		$db_pass = $_POST['db_pass'];
		$db_name = $_POST['db_name'];

		try {
			return new mysqli($db_host, $db_user, $db_pass, $db_name);

		} catch (mysqli_sql_exception $e) {
			print_error($e);
		}
	}

	function create_tables($connection) {
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
			print_error($e);
		}
	}

	function print_error($e) {
		die ("Database error: {$e->getMessage()} ({$e->getFile()}:{$e->getLine()})");
	}

	function install() {
		global $list;

		$connection = connect();
		create_tables($connection);

		load_categories($list, $connection);
		load_list($list, $connection);

		print("Database \"{$_POST['db_name']}\" initialized.");
	}

	function restore() {
		$connection = connect();

		$sql = "DROP TABLE IF EXISTS Pages, Categories;";
		$connection->query($sql);

		print("Database \"{$_POST['db_name']}\" restored.");
	}

?>