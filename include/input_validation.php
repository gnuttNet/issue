<?php

function isInt( $int )
{
	if( !is_numeric( $int ) )
	{
		// neither int of float
		return false;
	}
	
	// mismatch means float, or overflowing the precision of integers on 32bit systems
	if( (int)$int != $int )
	{
		return false;
	}
	
	return true;
}
?>