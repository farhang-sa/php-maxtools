<?php namespace MaxDatabaseManager ;

define( 'MaxDatabaseManagerExec' , true ) ;

include_once 'SQLDriverInterface.php';
include_once 'StandardSQLDriver.php';

Server::Init();

/**
 * @author Farhang Saeedi
 * @tutorial Main Max Database Managment
 */

class Server {

	protected static $DS ;

	protected static $ROOT ;

	protected static $Error ;

	protected static $Instances = array ();

	protected static function addInstance( $instance = null , $obj = null ){

		$LowIns = strtolower( $instance ) ;

		self::$Instances[ $LowIns ] = $obj ;

	}

	public static function getInstance( $instance = null ){

		$LowIns = strtolower( $instance ) ;

		if ( isset( self::$Instances[ $LowIns ] ) ) 

			return self::$Instances[ $LowIns ] ;

		return null ;

	}

	public static function getAllInstances(){

		return array_keys( self::$Instances ) ;

	}

	public static function Init(){

		self::$DS = DIRECTORY_SEPARATOR ;

		self::$ROOT  = realpath( __DIR__ ) ;

		$Helper = self::$ROOT . self::$DS . 'Objects' . self::$DS . 'helper.php' ;

		if ( file_exists( $Helper ) ) include_once $Helper ;

		$Helper = self::$ROOT . self::$DS . 'Actions'	. self::$DS . 'helper.php';

		if ( file_exists( $Helper ) ) include_once $Helper ;

	}

	public function Error( $Error = null ){

		$Error = self::$Error = ( $Error ) ? $Error : self::$Error ;

		if ( empty( self::$Error ) && $this->Connector() ) 

			{ $Error = self::$Error = $this->Connector()->error(); }

		return "Max Database Manager : {$Error}" ;

	}

	private $Name 	= null ;

	private $Driver = null;

	private $Connector 	= null;

	private $Database 	= null;

	public function __construct( $Driver = null ) {

		$this->LoadConnector( $Driver );

	}

	public function LoadConnector( $Driver = null ){

		if ( $this->Driver ) return true ; 

		if ( $Driver ) {
			
			$ConnectorFile = self::$ROOT . self::$DS . 'Drivers' . 

			self::$DS . strtolower( $Driver ) . self::$DS . strtolower( $Driver ) . '.php' ;
			
			$ConnectorFile = realpath( $ConnectorFile );

			if ( file_exists( $ConnectorFile ) ) {
			
				include_once $ConnectorFile ;

				$this->Driver = "{$Driver}Driver";
			
				$DriverFile = self::$ROOT . self::$DS . 'Drivers' . self::$DS . 

				strtolower( $Driver ) . self::$DS . strtolower( $Driver ) . '_driver.php' ;
				
				$DriverFile = realpath( $DriverFile );

				if ( file_exists( $DriverFile ) ) include_once $DriverFile ;
			
			} else self::$Error = 'Connector not found' ;
		
		} return true ;

	}

	public function Name(){ return $this->Name ; }

	public function Config( $addr = 'localhost' , 
		$user = 'root' , $pass = 'rootpass' , $port = '3306' , $database = null ) {
		
		$sys = $this->Driver ;

		$sys = new $sys( $addr , $user , $pass , $port , $database );

		$sys = $this->Connector( $sys );
		
		if ( $sys !== false ){

			$this->Name = $this->Name = $user . ( $pass ? ':' . $pass : '' ) . '@' . $addr . ( $port ? ':' . $port : '' );

			self::addInstance( $this->Name , $this );

			return true ;

		} return false ;
	
	}

	public function Connector( $system = null ) {

		if ( $this->Connector ) 
			return $this->Connector ;

		if ( $system instanceof Server ) {
			
			$system = $system->Connector( );

			$this->Driver = $system->Driver;

		} if ( $system instanceof $this->Driver ) {
			
			$this->Connector = $system;
			
			if ( $this->Connector->hasError( ) ) {
				
				self::$Error = $this->Connector->error( );
				
				return false;
			
			} 
		
		} return ( $this->Connector ) ? $this->Connector : false ;
			
	}

	public function Database( $database = null ) {

		if ( ! $this->Connector ) {

			self::$Error = 'Connector misconfigured' ;

			return false;

		} 

		$database = ( $database !== null ) ? $database : $this->Connector->database() ;

		if ( $database ) {
			
			$newDb = ( $database instanceof Database ) ? $database->Name( ) : $database ;
			
			$ins = Database::getInstance( $this->Name . '@' . $newDb );

			if ( $ins ) return $ins ;
			
			else if ( $newDb ) {
				
				$changeDB = $this->Connector->database( $newDb );
				
				if ( ! $changeDB ) {

					self::$Error = "can't change current database to '{$newDb}' !";

					return false;

				} $this->Database = $this->Connector->database();

				$cDatabase = new Database ;

				$cDatabase->Database( $this->Database );

				$cDatabase->Server( $this->Name() );
				
				$cDatabase->Load( 1 );

				return $cDatabase ;
			
			} return ( $this->Database ) ? $this->Database : false;
			
		} return ( $this->Database ) ? $this->Database : false ;
		
	}

	public function Query( $q = null ) {

		$Query = new Query ;

		$Query->Query( $q );
		
		$Query->Server( $this->Name() );
		
		$Query->Database( $this->Database->Name() );
		
		return $Query;
	
	}

	public function Kill( ) {

		if ( $this->Connector ) $this->Connector->close( );

		unset( $this->Database , $this->Connector ) ;

		return true ;
		
	}

	public function Execute( ) {

		$args = func_get_args( );
		
		if ( count( $args ) <= 2 ) return false;
		
		$tName = ( string ) $args[0];
		
		$fName = ( string ) $args[1];
		
		unset( $args[0] , $args[1] );
		
		$args = array_values( $args );
		
		$tbl = self::Database( )->Table( $tName );
		
		if ( $tbl ){

			self::$Error = 'Table not found' ;

			return call_user_func_array( [  $tbl , $fName  ] , $args ) ;

		} return false ;
		
	}

}

@ob_end_clean(); // End Cleaning Output Buffer

?>