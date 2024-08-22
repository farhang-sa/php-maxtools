<?php namespace MaxDatabaseManager ;

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

class Database extends MaxDBObject {

	protected $Tables = array();

	protected $Counter = 0;

	public function __construct( $database = null ) {

		$database && $this->Database( $database ); 
		
	}


	public function Database( $database = null ) {

		if ( $database ) {
			
			if ( $database instanceof Database ) $this->AcceptDatabase( $database );
			
			else if ( $database instanceof Table ) $this->Table( $database );
			
			else if ( is_string( $database ) ) $this->Name = $database;
			
		} return ( $this->Name ) ? Database::getInstance( $this->Name ) : false ;
		
	}

	public function Table( $table = null ) {

		if ( $table ) {

			$table = ( $table instanceof Table ) ? $table->Name() : $table ;

			if ( is_string( $table ) ) {
					
				$tbl = self::getInstance( $this->Server . '@' . $this->Name . ':' . $table );

				if ( $tbl ) return $tbl ;

				if ( $this->hasTable( $table ) ) {

					$this->Table = $table;
			
					$cTable = new Table ;

					$cTable->Table( $table );
					
					$cTable->Server( $this->Server );
					
					$cTable->Database( $this->Name );
					
					$cTable->isExists = true;

					$cTable->Load( 1 );
					
					return $cTable;
				
				} else return false ;
			
			} else ( $this->Tables ) ? $this->Tables : false ;
			
		} else {
			
			if ( $this->Table ) {
				
				$TableNumber = $this->Counter;
				
				if ( $TableNumber > count( $this->Tables ) - 1 ) {
					
					$this->Counter = 0 ;
					
					return $this->Table();
				
				} else {
					
					$this->Counter = ( int ) $this->Counter + 1;
					
					return $this->Table( $this->Tables[$TableNumber] );
				
				}
			
			} else {
				
				if ( ! $this->Load() && $this->Name ) {
					
					$this->Load( 1 );
					
					return $this->Table( );
				
				} else return false;
			
			}
		
		}
	
	}

	public function getTables(){ return $this->Tables ; }

	protected function hasTable( $table = null ) {

		$this->Load() OR $this->PrepareDatabase( );
		
		if ( $table ) {
			
			$table = ( $table instanceof Table ) ? $table->Name( ) : $table ;

			$table = strtolower( $table ) ;
			
			foreach( $this->Tables as $tbl => $name )
				
				if ( strtolower( $name ) === $table ) return true;
		
		} return false;
		
	}

	/***************************************/// The Instance Holder Part
	private static $Instances = array ();

	protected static function addInstance( $instance = null , $obj = null ){

		$LowIns = strtolower( $instance ) ;

		self::$Instances[ $LowIns ] = $obj ;

	}

	public static function getInstance( $instance = null ){

		$LowIns = strtolower( $instance ) ;

		if ( isset( self::$Instances[ $LowIns ] ) ) return self::$Instances[ $LowIns ] ;

		return null ;

	}

	public static function getAllInstances(){

		return array_keys( self::$Instances ) ;

	}

}

?>