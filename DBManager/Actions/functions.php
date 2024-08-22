<?php namespace MaxDatabaseManager ;

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

function getColumnName( $colNameWithPeoperty = "" ) {

	if ( $colNameWithPeoperty ) {
		
		return ( strstr( $colNameWithPeoperty , " " ) ) ? 
			explode( " " , $colNameWithPeoperty , 2 )[0] : false;
	
	} else return false;

}

function hasStringKey( array $theArray ) {

	$keyTypeString = false;
	
	foreach( array_keys( $theArray ) as $v ) {
		
		$keyTypeString = ( is_string( $v ) && ! is_int( $v ) ) ? true : false;
		
		if ( $keyTypeString ) break;
	
	} return $keyTypeString;

}

function hasProperty( array $theArray ) {

	$hasProperty = true;
	
	foreach( $theArray as $v ) {
		
		$hasProperty = ( strstr( $v , "(" ) && strstr( $v , "(" ) ) ? true : false;
		
		if ( $hasProperty == false ) {
			
			break;
		
		}
	
	} return $hasProperty;

}

function eachHasChar( array $theArray , $char = " " ) {

	$eachHasChar = true;
	
	foreach( $theArray as $v ) {
		
		$eachHasChar = ( stristr( $v , $char ) !== false ) ? true : false;
		
		if ( ! $eachHasChar ) break;
		
	} return $eachHasChar;

}

function thisHasChar( $theString , $char = " " ) {

	if ( stristr( $theString , $char ) !== false ) return true;
	
	else return false;

} ?>