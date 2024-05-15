<?php
	$referrer = '';

	session_start();
	update_referrer();

	function update_referrer() {
		global $referrer;

		$referrer = $_SESSION['referrer'] ?? '';

		$current = basename($_SERVER['PHP_SELF']);

		if (!in_array($current, ['login.php', 'logout.php'])) {

			$_SESSION['referrer'] = $_SERVER['REQUEST_URI'];
		}
	}

	function append_action($URL, $action) {
		$URL_path = parse_url($URL, PHP_URL_PATH);
		$URL_query = parse_url($URL, PHP_URL_QUERY);

		if (isset($URL_query))
			parse_str($URL_query, $args);
		else
			$args = [];

		$args['action'] = $action;
		$query = http_build_query($args);

		return "{$URL_path}?{$query}";
	}

	function get_referrer($action = null) {
		global $referrer;

		if (isset($action))
			return append_action($referrer, $action);

		return $referrer;
	}

	function get_authorization() {

		return (isset($_SESSION['user'])) ? true : false;
	}

?>
