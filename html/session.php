<?php
	session_start();

	function get_status() {

		return (isset($_SESSION['user'])) ? true : false;
	}

?>
