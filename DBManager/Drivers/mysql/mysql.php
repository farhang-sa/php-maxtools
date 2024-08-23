<?php

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

#[AllowDynamicProperties]
final class MysqlDriver implements SQLConnectorInterface {

	public $SystemName = "mysql";

	private $addr = null;

	private $user = null;

	private $pass = null;

	private $port = null;

	private $database = null;

	private $Query = null;

	private $Result = null;

	private $ERROR = null;

	private $hasError = false;

	public $QueryDriver = null;

	public $link = null;

	public final function __construct( $host = null , $user = null , $pass = null , $port = null , $db = null ) {

		$this->connection( $host , $user , $pass , $port , $db );
	
	}

	public final function connection( $host = null , $user = null , $pass = null , $port = null , $db = null ) {

		$this->addr = $host;
		
		$this->user = $user;
		
		$this->pass = $pass;
		
		$this->port = $port;
		
		$this->database = $db;
		
		$newAdd = ( $port ) ? $this->addr . ":" . $port : $this->addr;
		
		$this->link = @mysql_connect( $newAdd , $user , $pass , false , CLIENT_MULTI_STATEMENTS );
		
		if ( $this->link === false ) {
			
			$this->error( "Connection Error, Please Check Your Username And Password Access !" );
			
			$this->hasError = true;
			
			return false;
		
		} else if ( ! $this->isConnected( ) ) {
			
			return false;
		
		} else {

			$this->setCharset();
			
			$chDb = $this->database( $db );
			
			if ( $this->hasError( ) || ! $chDb ) {
				
				$this->hasError = true;
				
				$this->error( "Database Error, Cannot Change Database To '{$db}' !" );
				
				return false;
			
			} else {
				
				$QueryDriverClassName = $this->SystemName . "QueryDriver";
				
				$this->QueryDriver = new $QueryDriverClassName( );
				
				$this->hasError = false;
				
				return true;
			
			}
		
		}
	
	}

	public function setCharset( $Charset = "utf8mb4" ){ 

		$this->query("SET NAMES '{$Charset}' COLLATE 'utf8mb4'" );
		$this->set_charset( $Charset ); 
		$this->query("Charset {$Charset} ;" );
		$this->query("SET CHARACTER SET {$Charset} ;" );

	}

	public final function isConnected( ) {

		if ( @mysql_ping( $this->link ) ) {
			
			$this->error( "" );
			
			$this->hasError = false;
			
			return true;
		
		} else {
			
			if ( @mysql_errno( $this->link ) ) {
				
				$this->error( @mysql_errno( $this->link ) . " : " . @mysql_error( $this->link ) );
			
			}
			
			$this->hasError = true;
			
			return false;
		
		}
	
	}

	public final function query( $q = null ) {

		if ( $q ) $this->Query = $q;
		
		$Resualt = @mysql_query( $this->Query , $this->link );

		$this->Result = $Resualt ;
		
		if ( is_object( $Resualt ) ) $Resualt = $this->queryObject( $this->Result );
		
		else if ( $Resualt == false ) $this->hasError = true;
		
		$this->error( @mysql_errno( $this->link ) . " : " . @mysql_error( $this->link ) );
			
		return $Resualt ;
	
	}

	public final function error( $error = null ) {

		if ( $error ) $this->ERROR = $error;
		
		return $this->ERROR;
		
	}

	public final function hasError( ) {

		return $this->hasError;
	
	}

	public final function exec( $q = null ) {

		return $this->query( $q );
	
	}

	public final function database( $db = null ) {

		if ( $db ) {
			
			$chDb = @mysql_select_db( $db , $this->link );
			
			if ( ! $chDb ) {
				
				$q = "use {$db} ;";
				
				return $this->query( $q );
			
			} else {
				
				$this->hasError = false;
				
				return $chDb;
			
			}
		
		} else {
			
			if ( $this->database ) return $this->database;
			
			return false;
		
		}
	
	}

	public final function close( ) {

		if ( $this->link ) @mysql_close( $this->link );
	
	}

	private final function queryObject( $results = null ) {
		
		$this->Result = ( $results ) ? $results : $this->Result ;

		$cQuery = new MaxDatabaseManager\Query( );
		
		$cQuery->Query( $this->Query );
		
		$cQuery->effected( @mysql_affected_rows( $this->Result ) );
		
		$cQuery->Error( @mysql_errno( $this->link ) , @mysql_error( $this->link ) );
		
		$cQuery->fields( @mysql_num_fields( $this->Result ) );
		
		$cQuery->inserts( @mysql_insert_id( $this->Result ) );
		
		$cQuery->rows( @mysql_num_rows( $this->Result ) );
		
		$cQuery->params( null );
		
		$retArr = array();
		
		while( $newRow = @mysql_fetch_array( $this->Result , MYSQL_ASSOC ) ) $retArr[] = $newRow;
		
		$retArr = ( empty( $retArr ) ) ? $this->Result : $retArr ;
		
		$cQuery->result( $retArr );
		
		return $cQuery;
	
	}

}

?>