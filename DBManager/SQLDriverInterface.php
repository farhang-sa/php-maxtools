<?php defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

interface SQLDriverInterface {

	function connection( $host = null , $user = null , 
		$pass = null , $port = null , $db = null );

	function isConnected();

	function query( $q = null );

	function multiQuery( $q = null  );

	function error();

	function exec( $q = null );

	function database( $db = null );

	function close();

	function setCharset( $Charset = 'utf8mb4' );
	
}

?>