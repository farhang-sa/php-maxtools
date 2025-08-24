<?php defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

$dirPath = realpath( __DIR__ ) . DIRECTORY_SEPARATOR;
include_once ( $dirPath . 'functions.php' );
include_once ( $dirPath . 'query.php' );
include_once ( $dirPath . 'action.php' );
include_once ( $dirPath . 'insert.php' );
include_once ( $dirPath . 'select.php' );
include_once ( $dirPath . 'update.php' );
include_once ( $dirPath . 'delete.php' );
include_once ( $dirPath . 'result.php' );

?>