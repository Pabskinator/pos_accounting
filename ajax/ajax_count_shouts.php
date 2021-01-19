<?php

	$directory = '../pos/shoutbox/data/shouts/*';
	//$files = glob($directory);
	$files =false;
	if ( $files !== false )
	{
		$filecount = count( $files );
		echo $filecount;
	}
	else
	{
		echo 0;
	}