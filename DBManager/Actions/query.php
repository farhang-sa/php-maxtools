<?php namespace MaxDatabaseManager ;

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

class Query {

	protected $Query 	= null;

	protected $Result 	= null;

	protected $Params 	= null;

	protected $Effected = null;

	protected $Error 	= null;

	protected $Fields 	= null;

	protected $Inserts 	= null;

	protected $Rows 	= null;

	protected $Server 	= null;

	protected $Database = null;

	protected $Table 	= null;

	protected $Columns 	= null;


	public function exec( $q = null ) {

		if ( ! $this->Connector()->isConnected( ) ) {
			
			$this->Error( $this->Connector()->error( ) );
			
			return false ;
			
		} if ( $q ) $this->Query = $q;
		
		$QueryExec = $this->Connector()->query( $this->Query );
		
		if ( ! is_object( $QueryExec ) ) $this->Result( $QueryExec ) ;
		
		else $this->AcceptQuery( $QueryExec );
		
		$this->Error( $this->Connector()->Error( ) );
		
		return $this;
	
	}

	public function Connector(){ return Server::getInstance( $this->Server )->Connector() ; }

	protected function AcceptQuery( Query $q ) {

		if ( $q->Server( ) ) 	$this->Server( 		$q->Server( ) );
		
		if ( $q->Database( ) ) 	$this->Database(	$q->Database( ) );
			
		if ( $q->Table( ) ) 	$this->Table( 		$q->Table( ) );
			
		if ( $q->Columns( ) ) 	$this->Columns( 	$q->Columns( ) );
			
		if ( $q->Query( ) ) 	$this->Query( 	$q->Query( ) );
			
		if ( $q->Effected( ) ) 	$this->Effected( 	$q->Effected( ) );
		
		if ( $q->Error( ) ) 	$this->Error( 		$q->Error( ) );
		
		if ( $q->Fields( ) ) 	$this->Fields( 		$q->Fields( ) );
		
		if ( $q->Inserts( ) ) 	$this->Inserts( 	$q->Inserts( ) );
		
		if ( $q->Rows( ) ) 		$this->Rows( 		$q->Rows( ) );
		
		if ( $q->Params( ) ) 	$this->Params( 		$q->Params( ) );
		
		$this->Result( $q->Result( ) );
		
	}

	public function Query( $q = null ) {

		if ( is_string( $q ) ) $this->Query = $q;
		
		else if ( $q instanceof Query ) $this->AcceptQuery( $q );
		
		else if ( $q instanceof Database ) $this->Database( $q );
		
		else if ( $q instanceof Table ) $this->Table( $q );
		
		else if ( $q instanceof Columns ) $this->Columns( $q );
		
		return ( $this->Query ) ? $this->Query : false;
			
	}

	public function Server( $Server = null ) {

		if ( $Server ) {

			if ( $Server instanceof Server ) $this->Server = $Server->Name( );
			
			else if ( $Server instanceof Database ) $this->Server = $Server->Server( )->Name();
				
			else if ( $Server instanceof Table ) $this->Server = $Server->Server( )->Name();
				
			else if ( $Server instanceof Columns ) $this->Server = $Server->Server( )->Name();
				
			else if ( is_string( $Server ) )$this->Server = $Server;
			
		} return ( $this->Server )? $this->Server : false;
			
	}

	public function Database( $database = null ) {

		if ( $database ) {
			
			if ( is_string( $database ) ) $this->Database = $database;
			
			else if ( $database instanceof Database ) {
				
				if ( $database->Server( ) ) 

					$this->Server( $database->Server( )->Name() );
				
				$this->Database = $database->Name();
			
			} 
		
		} return ( $this->Database ) ? $this->Database : false;
		
	}

	public function Table( $table = null ) {

		if ( $table ) {
			
			if ( is_string( $table ) ) $this->Table = $table;
			
			else if ( $table instanceof Table ) {
				
				if ( $table->Server( ) ) $this->Server( $table->Server( )->Name() );
				
				if ( $table->Database( ) ) $this->Database( $table->Database( )->Name() );
				
				$this->Table = $table->Name();
			
			} return true;
		
		} return ( $this->Table ) ? $this->Table : false;
		
	}

	public function Columns( $column = null ) {

		if ( $column ) {
			
			if ( is_string( $column ) ) {
				
				$column = strtolower( $column );
				
				$column = new Columns( $column );
				
				if ( $this->Database ) $column->Database( $this->Database );
				
				if ( $this->Table ) $column->Table( $this->Table );
				
				if ( $this->Server ) $column->Server( $this->Server );
				
				$this->Columns = $column;
			
			} else if ( $column instanceof Columns ) {
				
				if ( $column->Server( ) ) $this->Server( $column->Server( )->Name() );
				
				if ( $column->Database( ) ) $this->Database( $column->Database( )->Name() );
				
				if ( $column->Table( ) ) $this->Table( $column->Table( )->Name() );
				
				$this->Columns = $column;
			
			} return true;
		
		} return ( $this->Columns ) ? $this->Columns : false;
		
	}

	public function Effected( $EffectedCount = null ) {

		if ( $EffectedCount ) {
			
			$this->Effected = $EffectedCount;
			
			return true;
		
		} return ( $this->Effected ) ? $this->Effected : false;
		
	}

	public function Error( $ErrorNO = null , $Error = null ) {

		if ( $ErrorNO && $Error ) {
			
			$this->Error = ( $ErrorNO . " : " . $Error );
			
			return true;
		
		} else if ( is_array( $ErrorNO ) ) {
			
			$this->Error = ( $ErrorNO[0] . " : " . $ErrorNO[1] );
			
			return true;
		
		} else if ( is_string( $ErrorNO ) ) $this->Error = $ErrorNO;
		
		return ( $this->Error ) ? $this->Error : false;
			
	
	}

	public function Fields( $Fields = null ) {

		if ( $Fields ) {
			
			$this->Fields = $Fields;
			
			return true;
		
		} return ( $this->Fields ) ? $this->Fields : false;
			
	}

	public function Inserts( $insertID = null ) {

		if ( $insertID ) {
			
			$this->Inserts = $insertID;
			
			return true;
		
		} return ( $this->Inserts ) ? $this->Inserts : false;

	}

	public function Rows( $Rows = null ) {

		if ( $Rows ) {
			
			$this->Rows = $Rows;
			
			return true;
		
		} return ( $this->Rows ) ? $this->Rows : false;
	
	}

	public function Params( $params = null ) {

		if ( $params ) {
			
			$this->Params = $params;
			
			return true;
		
		} return ( $this->Params ) ? $this->Params : false;
	
	}

	public function Result( $result = null ) {

		if ( $result !== null ) {
				
			if ( is_array( $result ) ) {

				$this->Result = new Result() ;

				$this->Result->Result( $result ) ;
			
			} else $this->Result = $result ;
		
		} return $this->Result ;
		
	}

}

?>