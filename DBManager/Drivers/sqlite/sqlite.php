<?php

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

#[AllowDynamicProperties]
final class SqliteDriver extends SQLite3 implements SQLDriverInterface {

	public $SystemName = "sqlite";

	private $file = null;

	private $database = null;

	private $Query = null;

	private $Result = null;

	private $ERROR = null;

	private $hasError = false;

	public $QueryDriver = null;

	public final function __construct( $file = null ) {

		$this->connection( $file );
	
	}

	public final function connection( $file = null , $user = null , $pass = null , $port = null , $db = null ) {

		$this->file = $file;
		
		$this->database = pathinfo( $file )['filename'];
		
		$Connection = $this->open( $this->file );
		
		if ( $Connection === false ) {
			
			$this->error( "Connection Error : Please Check Your Username And Password Access !" );
			
			$this->hasError = true;
			
			return false;
		
		} else if ( ! $this->isConnected( ) ) {
			
			return false;
		
		} else {
			
			$chDb = $this->database( $db );
			
			if ( $this->hasError( ) || ! $chDb ) {
				
				$this->hasError = true;
				
				$this->error( "Database Error : Cannot Change Database To '{$db}' !" );
				
				return false;
				
			} else {
				
				$QueryDriverClassName = $this->SystemName . "QueryDriver";
				
				$this->QueryDriver = new $QueryDriverClassName( );
				
				$this->hasError = false;
				
				return true;

			}
			
		}
	
	}

	public final function isConnected( ) {

		if ( file_exists( $this->file ) ) {
			
			$this->error( "" );
			
			$this->hasError = false;
			
			return true;
		
		} else {
			
			$this->error( "Database Error : Sqlite Database File Not Exists ." );
			
			$this->hasError = true;
			
			return false;
		}
	
	}

	public final function query( $q = null ) {

		if ( $q ) $this->Query = $q ;
		
		if ( stristr( $this->Query , 'insert' ) || stristr( $this->Query , 'update' ) ||  stristr( $this->Query , 'delete' ) )
				
			$Resualt = @parent::exec( $this->Query ) ;
			
		else $Resualt = @parent::query( $this->Query ) ;
		
		$this->Result = $Resualt ;
			
		if ( is_object( $Resualt ) ) $Resualt = $this->queryObject( $this->Result );
		
		else if ( $Resualt == false ) $this->hasError = true;
		
		$this->error( $this->lastErrorCode( ) . " : " . $this->lastErrorMsg( ) );
			
		return $Resualt;
	
	}

	public final function error( $error = null ) {

		if ( $error ) $this->ERROR = $error;
		
		return $this->ERROR;
		
	}

	public final function hasError( ) {

		return $this->hasError;
	
	}

	public final function exec( $q = null ) {

		return $this->query( $q ) ;
	
	}

	public final function database( $db = null ) {

		if ( $db ) {
			
			if ( $this->database == $db ) {
				
				$this->hasError = false;
				
				return true;
			
			} else {
				
				$this->hasError = true;
				
				return false;
			
			}
		
		} else {
			
			if ( $this->database ) return $this->database;
			
			return false;
		
		}
	
	}

	public final function close( ) {

		if ( $this->isConnected( ) ) parent::close( );
	
	}

	private final function queryObject( $results = null ) {
		
		$this->Result = ( $results ) ? $results : $this->Result ;

		$cQuery = new MaxDatabaseManager\Query( );

		$cQuery->Query( $this->Query );
		
		$cQuery->system( $this );
		
		$cQuery->effected( $this->changes( ) );
		
		$cQuery->Error( $this->lastErrorCode( ) , $this->lastErrorMsg( ) );
		
		$cQuery->fields( $this->Result->numColumns( ) );
		
		if ( isset( $this->Result->insert_id ) ) $cQuery->insert( $this->Result->insert_id );
		
		if ( isset( $this->Result->num_rows ) ) $cQuery->rows( $this->Result->num_rows );
		
		if ( isset( $this->Result->param_count ) ) $cQuery->params( $this->Result->param_count );
		
		$results = array ();
		
		while( $cArray = $this->Result->fetchArray( SQLITE3_ASSOC ) ) $results[] = $cArray;
			
		if ( stristr( $this->Query , "PRAGMA TABLE_INFO" ) ) {
					
			$newArray = array ();
					
			foreach( $results as $k => $v ) {
			
				$v = array_values( $v );
			
				unset( $v[0] );
			
				$v = array_values( $v );
			
				$newArray[] = array_values( $v );
						
			}
					
			$results = $newArray;
			
		}
		
		if ( empty( $results ) ) $results = null ;
		
		$cQuery->result( $results );
		
		return $cQuery;
	
	}

}
?>