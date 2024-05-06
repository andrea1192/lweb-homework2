<?php
	
	$lines = file('./template.html');

	foreach ($lines as $line_num => $line) {

		if (preg_match('/{{content}}/', $line)) {

			if (isset($_GET['page'])) {

				readfile("./{$_GET['page']}.html");
			} else {

				readfile('./main.html');
			}

		} else {

			echo $line;
		}
	}

?>