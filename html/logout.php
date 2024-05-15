<?php
	require_once("utils.php");
	require_once("view.php");

	function logout() {

		$_SESSION['user'] = null;
		//session_destroy();
		//setcookie(session_name(), '', time()-60*60*24);
	}

	logout();
	header('Location:'.get_referrer('logout'));

	exit();

?>