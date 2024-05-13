<?php
	$referrer = '';

	session_start();
	set_referrer();

	function set_referrer() {
		global $referrer;

		$referrer = $_SESSION['referrer'] ?? $_SERVER['PHP_SELF'];

		$_SESSION['referrer'] = $_SERVER['REQUEST_URI'];
	}

	function get_referrer() {
		global $referrer;
		return $referrer;
	}

	function get_status() {

		return (isset($_SESSION['user'])) ? true : false;
	}

?>
