<?php defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

#[AllowDynamicProperties]
final class MySqliConnector extends mysqli implements SQLDriverInterface {

	public $InterfaceName = "Mysqli";

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

	public function __construct( $host = null , $user = null , $pass = null , $port = null , $db = null ) {

		$this->connection( $host , $user , $pass , $port , $db );
	
	}

	public function connection( $host = null , $user = null , $pass = null , $port = null , $db = null ) {

		$this->addr = $host;
		
		$this->user = $user;
		
		$this->pass = $pass;
		
		$this->port = $port;
		
		$this->database = $db;
		
		$Connection = @$this->connect( $host , $user , $pass , $db , $port );
		
		if ( $Connection === false ) {
			
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
				
				$QueryDriverClassName = $this->InterfaceName . "QueryDriver";
				
				$this->QueryDriver = new $QueryDriverClassName( );
				
				$this->hasError = false;
				
				return true;
			
			}
		
		}
	
	}

	public function setCharset( $Charset = 'utf8' ){ 

		$this->query("SET NAMES '{$Charset}' COLLATE 'utf8_unicode_ci'" );
		$this->set_charset( $Charset ); 
		//@$this->query("Charset {$Charset} ;" );
		//@$this->query("SET CHARACTER SET {$Charset} ;" );

	}

	public function isConnected( ) {

		if ( @$this->ping( ) ) {
			
			$this->error( "" );
			
			$this->hasError = false;
			
			return true;
		
		} else {
			
			if ( $this->connect_errno ) {
				
				$this->error( $this->connect_errno . " : " . $this->connect_error );
			
			} else if ( @$this->errno ) {
				
				$this->error( @$this->errno . " : " . @$this->error );
			
			}
			
			$this->hasError = true;
			
			return false;
		
		}
	
	}

	#[\ReturnTypeWillChange]
	public function query( $q = null , $resultMode = null ) {

		if ( $q ) $this->query = $q ;
		
		$Resualt = parent::query( $this->query ) ;
		
		$this->Result = $Resualt ;

		if ( is_object( $Resualt ) ) $Resualt = $this->QueryObject( $Resualt );
		
		else if ( $Resualt == false ) $this->hasError = true;
		
		$this->error( $this->errno . " : " . $this->error );
			
		return $Resualt;
	
	}

	public function multiQuery( $q = null ){

		if ( $q ) $this->query = $q ;

		$exec = $this->multi_query( $this->query ) ;

		$Resualt = array() ;

		if ( $exec ) do {

			$result = $this->use_result() ;

	   		if ( $result ) {
				
				$Resualt[ ] = $result->fetch_all( MYSQLI_ASSOC ) ;
				
	            $result->free();

	        } else  $Resualt[ ] = ( $this->errno === 0 ) ? true : false ;

	        if ( ! $this->more_results() ) break ;

	    } while ( $this->next_result() );
		
		return $this->Result = $Resualt ;

	}

	public function error( $error = null ) {

		if ( $error ) $this->ERROR = $error;
		
		return $this->ERROR;
		
	}

	public function hasError( ) {

		return $this->hasError;
	
	}

	public function exec( $q = null ) {

		return $this->query( $q );
	
	}

	public function database( $db = null ) {

		if ( $db ) {
			
			$chDb = @$this->select_db( $db );
			
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

	public function close( ) {

		if ( $this->isConnected( ) ) parent::close( );
	
	}

	private function QueryObject( $results = null ) {
		
		$this->Result = ( $results ) ? $results : $this->Result ;

		$cQuery = new MaxDatabaseManager\Query( );
		
		$cQuery->Query( $this->Query );
		
		if ( isset( $this->Result->affected_rows ) ) $cQuery->effected( $this->Result->affected_rows );
		
		if ( isset( $this->Result->errno ) && isset( $this->Result->error ) ) 
			
			$cQuery->Error( $this->Result->errno , $this->Result->error );
		
		if ( isset( $this->Result->field_count ) ) $cQuery->fields( $this->Result->field_count );
		
		if ( isset( $this->Result->insert_id ) ) $cQuery->insert( $this->Result->insert_id );
		
		if ( isset( $this->Result->num_rows ) ) $cQuery->rows( $this->Result->num_rows );
		
		if ( isset( $this->Result->param_count ) ) $cQuery->params( $this->Result->param_count );
		
		$results = array ();
		
		$retArr = ( is_object( $this->Result ) ) ? $this->Result->fetch_all( MYSQLI_ASSOC ) : array() ;
		
		if ( is_array( $retArr ) && empty( $retArr ) ) $retArr = false ;

		if ( empty( $results ) ) $results = null ;
		
		$cQuery->result( $retArr );
		
		return $cQuery;
	
	}

}

?>