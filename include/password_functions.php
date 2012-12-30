<?php
	function generateRandomString( $length = 8 ){
		// Use of constant as we don't want to have to push this to the stack every time this function runs
		define( "AVAILABLE_CHARACTERS", 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!"\'#¤%&/()=?`;:_,.-<>|^*¨' );

		$string = "";
		$characterCount = strlen( AVAILABLE_CHARACTERS );

		for( $i = 0; $i < $length; ++$i )
		{
			$string .= substr( AVAILABLE_CHARACTERS, rand( 0, $characterCount - 1 ), 1 );
		}

		return $string;
	}
?>