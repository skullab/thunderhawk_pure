<?php

$buffer = array() ;

function setContent($content){
	global $buffer ;
	$buffer[] = $content ;
}

function getContent(){
	global $buffer ;
	return end($buffer);
}

ob_start();
	
	ob_start('setContent');
	echo 'action<br>' ;
	ob_end_clean();
	
	ob_start('setContent');
	echo 'layout<br>' ;
	echo getContent();
	ob_end_clean();
	
	ob_start('setContent');
	echo 'main<br>' ;
	echo getContent();
	ob_end_clean();
	
ob_end_clean();

echo getContent();
?>