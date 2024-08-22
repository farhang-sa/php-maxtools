<?php namespace MaxDatabaseManager ;

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

abstract class MaxDBObject {

	protected $Name = null ;

	protected $Server = null ;

	protected $Database = null;

	protected $Table = null;

	protected $isLoaded = false;

	public $isExists = false;


	public function Name( ) { return $this->Name ; }

	public function Load( $table = null ) {

		if ( ! $table ) return $this->isLoaded ;

		if ( $this instanceof Table ) {

			if ( $table instanceof Database ) {
				
				$this->AcceptTable( $table->Table( ) );

				return true ;
				
			} else if ( $table instanceof Table ) {
				
				$this->AcceptTable( $table );

				return true ;
				
			} else if ( is_string( $table ) ) {
				
				$this->Name( $table );
				
				$this->PrepareTable( );
				
				return true;
			
			} else if ( $this->Name && ! $this->isLoaded ) {
					
				$this->PrepareTable( );
					
				return true;
				
			} else return false;
		
		} else if ( $this instanceof Database ) {

			if ( $table instanceof Database ) {
				
				$this->AcceptDatabase( $table );

				return true ;
			
			} else if ( is_string( $table ) ) {
				
				$this->Name( $table );
				
				$this->PrepareDatabase( );
				
				return true;
			
			} else if ( $this->Name ) {

				$this->PrepareDatabase( );

				return true ;

			} else return false;

		}
	
	}

	public function Exists( ) { return ( $this->isExists == true ) ? true : false ; }

	public function Server( $Server = null ) {

		if ( $Server ) {
			
			if ( $Server instanceof Server ) $this->Server = $Server->Name( );
			
			else if ( $Server instanceof Database ) $this->Database( $Server );
			
			else if ( is_string( $Server ) ) $this->Server = $Server ;
			
		} return ( $this->Server ) ? Server::getInstance( $this->Server ) : false ;
		
	}

	public function Error( $e = null ){ return $this->Server()->Error( $e ) ; }

	public function Connector(){ return $this->Server()->Connector() ; }

	public function Query( $q = null , $QueryClassType = 'Query' ) {

		$QueryClassType = "MaxDatabaseManager\\" . trim( $QueryClassType , " \\/." ) ;

		$QueryClassType = ( class_exists( $QueryClassType ) ) ? 
			$QueryClassType : "MaxDatabaseManager\\Query" ;

		$Query = new $QueryClassType ;

		$Query->Query( $q );

		if ( $this->Server ) $Query->Server( $this->Server );

		if ( $this instanceof Database ) $Query->Database( $this->Name() ) ;
		
		else if ( $this instanceof Table ) {

			if ( $this->Database() )

				$Query->Database( $this->Database()->Name() ); 
		
			$Query->Table( $this->Name() );

		} return $Query;
	
	}

	public function exec( $q ){ return $this->Query( $q )->exec( ); }

	public function Execute() {

		$args = func_get_args( );
		
		if ( count( $args ) <= 1 ) return false;
		
		$table = $this ;

		if ( ! $table instanceof Table ) {

			$table = ( string ) $args[0];

			$table = $this->Table( $table ) ;

			array_shift( $args ) ;

		} $fName = ( string ) $args[0] ;
		
		array_shift( $args ) ;
		
		if ( $table ) return call_user_func_array( [ $table , $fName ] , $args );

		$this->Error( "{$fName} faild , table '{$table}' not found" ) ;
		
		return false;
	
	}

	/// General Sample Working Area
	public function InsertIntoTable( $table = null , $insert = array() ){

		if ( $this instanceof Table ) {

			if ( is_array( $table ) && empty( $insert ) ) {

				$insert = $table ;

			} $table = $this->Name() ;

		} else if ( $this instanceof Database ) {

			if ( $table instanceof Table ) $table = $table->Name() ;

		} $QueryOK = $this->CheckQuery( $table , $insert ) ;
		
		if ( ! is_string( $QueryOK ) ) {

			if ( $this->Execute( $table , 'insertRecord' , $insert ) ) return true ;

		} else $this->Error( $QueryOK ) ;

		return false ;

	}

	public function UpdateIntoTable( $table = null , $update = array() , $where = array() ,

	$limit = null , $offset = null ){

		if ( $this instanceof Table ) {

			if ( is_array( $table ) && empty( $where ) ) {

				$where = $update ;

				$update = $table ;

			} $table = $this->Name() ;

		} else if ( $this instanceof Database ) {

			if ( $table instanceof Table ) $table = $table->Name() ;

		} $QueryOK = $this->CheckQuery( $table , $update , $where ) ;
		
		if ( ! is_string( $QueryOK ) ) {
			
			$upd = $this->Execute( $table , 'updateRecord' , $update , $where ) ;

			if ( $upd ) return true ;

		} else $this->Error( $QueryOK ) ;

		return false ;

	}

	public function SelectFromTable( $table = null , $select = array() , 

	$where = array() , $limit = null , $offset = null , $order = null ){

		if ( $this instanceof Table ) {

			if ( is_array( $table ) && is_null( $order ) ) {

				$order = $offset ;

				$offset = $limit ;

				$limit = $where ;

				$where = $select ;

				$select = $table ;

			} $table = $this->Name() ;

		} else if ( $this instanceof Database ) {

			if ( $table instanceof Table ) $table = $table->Name() ;

		} $QueryOK = $this->CheckQuery( $table , $select , $where ) ;

		if ( ! is_string( $QueryOK ) ) {
			
			$Result = $this->Execute( $table , 'selectRecord' , $select , $where , $limit , $offset, $order ) ;

			if ( $Result ) return $Result->getQuery();

		} else $this->Error( $QueryOK ) ;

		return false ;

	}

	public function DeleteFromTable( $table = null , $where = array() , 

	$limit = null , $offset = null ){

		if ( $this instanceof Table ) {

			if ( is_array( $table ) && is_null( $offset ) ) {

				$offset = $limit ;

				$limit = $where ;

				$where = $table ;

			} $table = $this->Name() ;

		} else if ( $this instanceof Database ) {

			if ( $table instanceof Table ) $table = $table->Name() ;

		} $QueryOK = $this->CheckQuery( $table , $where ) ;

		if ( ! is_string( $QueryOK ) ) {

			$del = $this->Table( $table )->Execute( 'deleteRecord' , $where , $limit , $offset ) ;
			
			if ( $del ) return true ;

		} else $this->Error( $QueryOK ) ;

		return false ;

	}

	protected function CheckQuery( $table = null , $data = array() , $where = null ){

		$QueryOK = true ;

		if ( ! $table || ( $this instanceof Database && ! $this->hasTable( $table ) ) ) {

			$QueryOK = 'No table found' ;

		} else if ( empty( $data ) || ! is_array( $data ) ){

			$QueryOK = 'No data array found' ;

		} else if ( $where !== null && ( empty( $data ) || ! is_array( $data ) ) ) {

			$QueryOK = 'No search array found' ;

		} return $QueryOK ;

	}

	/// Protected Functions
	protected function AcceptTable( $table ) {

		if ( $table->Server( ) ) 	$this->Server = $table->Server( )->Name() ;
		
		if ( $table->Database( ) ) 	$this->Database = $table->Database( )->Name() ;
		
		if ( $table->Name( ) ) 		$this->Name = $table->Name( );

		if ( $table instanceof Database ) $table = $table->Table( );

		if ( ! $table instanceof Table ) return false ;
		
		if ( $table->Load( ) ) {

			$this->Columns = $table->Columns() ;
			
			$this->isExists = true;
			
			$this->isLoaded = true;
		
		} else if ( $table->Exists( ) ) {
			
			$this->isExists = true;
			
			$this->Load( 1 );
		
		} else {
			
			$this->isLoaded = false;
			
			$this->isExists = false;
		
		} return true;
	
	}

	protected function PrepareTable( ) {

		$this->Connector()->database( $this->Database );
		
		if ( ! $this->Database ) { return false; }
		
		$q = $this->Connector()->QueryDriver->describeTable( $this->Database , $this->Name );
		
		$q = $this->Query( $q );
		
		$res = $q->exec( )->Result( );
		
		if ( is_bool( $res ) || ! $res instanceof Result ) {
			
			$this->isLoaded = true;
			
			return true ;
		
		} $res = $res->QueryResults( );
		
		foreach( $res as $k => $column ) {
			
			$cColumnName = $column[array_keys( $column )[0]];
			
			$this->Columns[$cColumnName] = $cColumnName;
		
		} $this->isLoaded = true;
		
		$this->isExists = true;
	
	}

	protected function AcceptDatabase( $db ) {

		if ( $db->Server( ) ) $this->Server = $db->Server( )->Name();
		
		if ( $db->Name( ) ) $this->Name = $db->Name( );

		if ( ! $db instanceof Database ) return false ;
		
		if ( $db->Load( ) ) {

			$this->Table = $db->Table ;

			$this->Tables = $db->Tables ;
			
			$this->isExists = true;
			
			$this->isLoaded = true;
		
		} else if ( $db->Exists( ) ) {
			
			$this->isExists = true;
			
			$this->Load( 1 );
		
		} else {
			
			$this->isLoaded = false;
			
			$this->isExists = false;
		
		} return true;
	
	}

	protected function PrepareDatabase( ) {

		$this->Connector()->database( $this->Name );
		
		$q = $this->Connector()->QueryDriver->showTables( $this->Name );
		
		$q = $this->Query( $q );
		
		$res = $q->exec( )->Result( );
		
		if ( is_bool( $res ) || ! $res instanceof Result ) {
			
			$this->isLoaded = true;
			
			return true ;

		} $res = $res->QueryResults( );
		
		foreach( $res as $k => $tbls ) {
			
			$cTableName = $tbls[array_keys( $tbls )[0]];
			
			$this->Table = $cTableName;
			
			$this->Tables[] = $cTableName;
		
		} $this->isExists = true;
		
		$this->isLoaded = true;
		
		ksort( $this->Tables );
		
		return true;
	
	}

}

?>