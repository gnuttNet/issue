<?php
	// @param type - can bet $_POST, $_GET, $_SESSION
	function getPresetString( $type, $name )
	{
		return isset($type[$name]) ? $type[$name] : "";
	}
?>
