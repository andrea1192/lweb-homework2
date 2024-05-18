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

	function get_authorization() {
		return isset($_SESSION['user']);
	}

?>
