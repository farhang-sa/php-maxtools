<?php namespace MaxDatabaseManager ;

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

class Table extends MaxDBObject {

	protected static $alias = array (
	
		'insert' => 'insert' ,
		'embed' => 'insert' ,
		'add' => 'insert' ,
		'new' => 'insert' ,
		'dig' => 'insert' ,
		'poach' => 'insert' ,
		'inculcate' => 'insert' ,
		'implant' => 'insert' ,
		'infix' => 'insert' ,
		'thrust' => 'insert' ,
		'jam' => 'insert' ,
		'enhance' => 'insert' ,
		'eke' => 'insert' ,
		'redouble' => 'insert' ,
		'augment' => 'insert' ,
		'append' => 'insert' ,
		'amplify' => 'insert' ,
		'aggrandize' => 'insert' ,
		'adjoin' => 'insert' ,
		'aggravate' => 'insert' ,
		'inset' => 'insert' ,
		'increase' => 'insert' ,
		'push' => 'insert' ,
		'surcharge' => 'insert' ,
		'save' => 'insert' ,
		'put' => 'insert' ,
		
		'update' => 'update' ,
		'change' => 'update' ,
		'alter' => 'update' ,
		'refill' => 'update' ,
		'change' => 'update' ,
		'modify' => 'update' ,
		'reorgnize' => 'update' ,
		'orgnize' => 'update' ,
		'conversion' => 'update' ,
		'commutation' => 'update' ,
		'vicissitude' => 'update' ,
		'vexation' => 'update' ,
		'variation' => 'update' ,
		'mutation' => 'update' ,
		
		'select' => 'select' ,
		'view' => 'select' ,
		'see' => 'select' ,
		'observe' => 'select' ,
		'open' => 'select' ,
		'scout' => 'select' ,
		'return' => 'select' ,
		'chosen' => 'select' ,
		'pick' => 'select' ,
		'elect' => 'select' ,
		'choice' => 'select' ,
		'draft' => 'select' ,
		'look' => 'select' ,
		'regard' => 'select' ,
		'selectData' => 'select' ,
		'viewData' => 'select' ,
		'seeData' => 'select' ,
		'observeData' => 'select' ,
		'openData' => 'select' ,
		'scoutData' => 'select' ,
		'returnData' => 'select' ,
		'chosenData' => 'select' ,
		'pickData' => 'select' ,
		'electData' => 'select' ,
		'choiceData' => 'select' ,
		'draftData' => 'select' ,
		'lookData' => 'select' ,
		'regardData' => 'select' ,
		
		'delete' => 'delete' ,
		'drop' => 'delete' ,
		'del' => 'delete' ,
		'remove' => 'delete' ,
		'rm' => 'delete' ,
		'dele' => 'delete' ,
		'clear' => 'delete' ,
		'pull' => 'delete' ,
		'retrench' => 'delete' ,
		'omit' => 'delete' ,
		'emit' => 'delete' ,
		'eliminate' => 'delete' ,
		'expurgate' => 'delete' ,
		'take' => 'delete' ,
		'minus' => 'delete' 
	) ;

	protected $Columns 	= array ();


	public final function __get( $methodOrColumn ) {

		$Method = $this->RecodnizeMethod( $methodOrColumn );
		
		if ( $Method !== false ) 

		return $this->$Method( );
		
		else if ( $this->hasColumn( $methodOrColumn ) )
			
			return $this->Columns[$methodOrColumn];
		
		else return false;
		
	}

	public final function __call( $func , $params ) {
		
		$Method = $this->RecodnizeMethod( $func );
		
		if ( $Method === false ) return false;
		
		return call_user_func_array( [ $this , $Method ] , $params );
	
	}

	public function Alias( $aliasOf , $alias ) {

		$methods = [ 'select' , 'update' , 'delete' , 'insert'  ];
		
		foreach( $methods as $v ) {
			
			if ( stristr( $v , $alias ) || strtolower( $alias ) == strtolower( $v ) ) {
				
				self::$alias[$aliasOf] = $alias;
				
				return true;
			
			}
		
		} return false;
	
	}

	public function Database( $database = null ) {

		if ( $database ) {

			if ( $database instanceof Database ) {
				
				if ( $database->Server( ) ) 

					$this->Server = $database->Server()->Name();
				
				$this->Database = $database->Name();
			
			} else if ( is_string( $database ) ) $this->Database = $database;

			self::addInstance( $this->Server . "@" . $this->Database . ":" . $this->Name , $this )  ;

		} return ( $this->Database ) ? Database::getInstance( $this->Database ) : false ;
	
	}

	public function Table( $table = null ) {

		if ( $table ) {

			if ( $table instanceof Database ) $this->Database( $table );
			
			else if ( $table instanceof Table ) $this->AcceptTable( $table );
			
			else if ( $table instanceof Columns ) $this->Columns( $table );
			
			else if ( is_string( $table ) ) $this->Name = $table;

			self::addInstance( $this->Name , $this );

		} return ( $this->Name ) ? $this->Name : false ;

	}

	public function Columns( ) {

		return ( $this->Columns ) ? $this->Columns : array() ;
		
	}

	public function Fill( $params ) {

		$columns = $this->Columns( );
		
		$fill = array ();
		
		foreach( $columns as $v ) {
			
			if ( isset( $params[$v] ) ) 

				$fill[$v] = $params[$v];
			
			else $fill[$v] = '0' ;
		
		} return $fill;
	
	}

	/// Unique Methods For This Table
	
	public function Insert() {

		$arg = func_get_args( );
		
		$Insert = ( isset( $arg[0] ) && $arg[0] instanceof Insert ) ? $arg[0] : null;
		
		if ( $Insert ) {
			
			$Insert->Server( $this->Server );
			
			$Insert->Database( $this->Database );
			
			$Insert->Table( $this->Name );
			
			$Insert->TableColumns( $this->Columns( ) );
			
			$Insert->exec();
			
			return $Insert->Result( );
		
		} $Insert = $this->Query( null , 'Insert' ) ;
		
		$Insert->TableColumns( $this->Columns( ) );
		
		$Insert->Insert( $arg );
		
		return $Insert;
	
	}

	public function insertRecord( $recordInfos = null ) {
		
		if ( $recordInfos === null ) return $this->Insert( );
		
		return $this->Insert( $this->Fill( $recordInfos ) )->exec( )->Result( );
	
	}

	public function Update( ) {

		$arg = func_get_args( );
		
		$Update = ( isset( $arg[0] ) && $arg[0] instanceof Update ) ? $arg[0] : null;
		
		if ( $Update ) {
			
			$Update->Server( $this->Server );
			
			$Update->Database( $this->Database );
			
			$Update->Table( $this->Name );
			
			$Update->exec( );
			
			return $Update->Result( );
		
		}
		
		$Update = $this->Query( null , 'Update' ) ;
		
		$Update->Update( $arg );
		
		return $Update;
	
	}

	public function updateRecord( $what = null , $where = null ) {	

		if ( $what === null ) return $this->Update( );
		
		$update = $this->Update( $what );
		
		if ( $where !== null ) $update->where( $where );

		return $update->exec()->Result( );
	
	}

	public function Select( ) {

		$arg = func_get_args( );
		
		$select = ( isset( $arg[0] ) && $arg[0] instanceof Select ) ? $arg[0] : null;
		
		if ( $select ) {
			
			$select->Server( $this->Server );
			
			$select->Database( $this->Database );
			
			$select->Table( $this->Name );
			
			$select->exec( );
			
			return $select->Result( );
		
		} $select = $this->Query( null , 'Select' ) ;
		
		if ( func_num_args( ) == 0 ) $arg = '*' ;
		
		$select->select( $arg );

		return $select;
	
	}

	public function selectRecord( $what = null , $where = null , $limit = null , $offset = null , $order = null ) {

		if ( $what === null ) $what = '*' ;
		
		$view = $this->Select( $what );
		
		$where && $view->where( $where );

		$limit && $view->limit( $limit );

		$offset && $view->offset( $offset );

		$order && $view->order( $order );
		
		$view = $view->exec( )->Result( );
		
		return $view;
	
	}

	public function Delete( ) {

		$arg = func_get_args( );
		
		$delete = ( isset( $arg[0] ) && $arg[0] instanceof Delete ) ? $arg[0] : null;
		
		if ( $delete ) {
			
			$delete->Server( $this->Server );
			
			$delete->Database( $this->Database );
			
			$delete->Table( $this->Name );
			
			$delete->exec( );
			
			return $delete->Result( );
		
		} $delete = $this->Query( null , 'Delete' ) ;
		
		$delete->where( $arg );
		
		return $delete;
	
	}

	public function deleteRecord( $where = null ) {

		if ( $where === null ) return $this->Delete( );
		
		return $this->Delete( )->where( $where )->exec( )->Result( );
	
	}

	protected function RecodnizeMethod( $method ) {

		if ( stristr( $method , 'Records' ) ) {
			
			$method = str_ireplace( 'Records' , '' , $method );
			
			$method = strtolower( $method );
			
			$alias = ( isset( self::$alias[$method] ) ) ? self::$alias[$method] : false;
			
			if ( $alias === false ) 

			return false;
			
			return "{$alias}Record";
		
		} elseif ( stristr( $method , 'Record' ) ) {
			
			$method = str_ireplace( 'Record' , '' , $method );
			
			$method = strtolower( $method );
			
			$alias = ( isset( self::$alias[$method] ) ) ? self::$alias[$method] : false;
			
			if ( $alias === false ) 

			return false;
			
			return "{$alias}Record";
		
		} else {
			
			$method = strtolower( $method );
			
			$alias = ( isset( self::$alias[$method] ) ) ? self::$alias[$method] : false;
			
			if ( $alias === false ) 

			return false;
			
			return $alias;
		
		}
	
	}

	protected function hasColumn( $column = null ) {

		if ( $column ) {
			
			$column = ( $column instanceof Columns ) ? $column->Name( ) : $column;
			
			foreach( $this->Columns as $k => $col ) {
				
				if ( $k == $column || $col == $column ) return true;
				
			} return false;
		
		} else return false;
		
	}

	/***************************************/// The Instance Holder Part
	protected static $Instances = array ();

	protected static function addInstance( $name = null , $obj = null ){

		self::$Instances[ $name ] = $obj ;

		return true ;

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