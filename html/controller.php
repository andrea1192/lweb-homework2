<?php
	require_once("session.php");
	require_once("utils.php");
	require_once("model.php");
	require_once("view.php");

	function check_actions($current) { //controller

		if (!tables_exist()) header('Location:'.rewrite_URL('install.php', action:'db_issues', encode: false));

		if (!isset($_GET['action'])) return;

		switch ($_GET['action']) {

			case 'login':
				if (isset($_POST['user']) && isset($_POST['pass'])) {

					authenticate_user($_POST['user'], $_POST['pass']); //model
				} break;

			case 'login_failed':
				msg_failure("Credenziali non corrette!"); break;

			case 'access_denied':
				msg_failure("Per questa azione devi essere loggato!"); break;

			case 'logout':
				msg_success("Logout avvenuto. A presto!"); break;

			case 'edit':
				if (isset($_POST['title']) && isset($_POST['text'])) {
					$connection = connect(); //model

					$article['name'] = $current;
					$article['title'] = $_POST['title'];
					$article['text'] = $_POST['text'];

					save_article($article);
				} break;

			default: return;
		}
	}

	function msg_success($msg) { //view wrapper
		set_message($msg, true);
	}

	function msg_failure($msg) { //view wrapper
		set_message($msg, false);
	}

	function get_categories() { //model wrapper
		return select_categories();
	}

	function get_articles($category) { //model wrapper
		return select_articles($category);
	}

	function get_article($article, $encode = true) { //model wrapper
		$article = select_article($article);

		if ($encode) {
			$article['title'] = htmlspecialchars($article['title'], ENT_XHTML);
			$article['text'] = htmlspecialchars($article['text'], ENT_XHTML);
		}

		return $article;
	}

	function save_article($article) {
		$article['title'] = htmlspecialchars_decode($article['title'], ENT_XHTML);
		$article['text'] = htmlspecialchars_decode($article['text'], ENT_XHTML);

		$article['text'] = fix_sample_code($article['text']);

		update_article($article); //model
	}

?>