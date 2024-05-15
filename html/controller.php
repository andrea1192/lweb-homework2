<?php
	require_once("session.php");
	require_once("utils.php");
	require_once("model.php");
	require_once("view.php");

	function check_actions($current) { //controller

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
				msg_success("Logout avvenuto."); break;

			case 'edit':
				if (isset($_POST['title']) && isset($_POST['text'])) {
					$connection = connect(); //model

					$article['name'] = $current;
					$article['title'] = $_POST['title'];
					$article['text'] = fix_sample_code($_POST['text']); //utils

					update_article($article); //model
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

	function get_article($article) { //model wrapper
		return select_article($article);
	}

?>