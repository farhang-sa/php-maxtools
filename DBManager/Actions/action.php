<?php namespace MaxDatabaseManager ;

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

abstract class Action extends Query {

	protected $Where = "";

	protected $Limit = "";

	protected $Offset = "";
	
	protected $Order = "" ;

	public function where( ) {

		$args = func_get_args( );
		
		$argc = func_num_args( );
		
		/**
		 * where( id in ( select id from other_table where code = 1 ) ) ;
		 * where("id>=1 AND number <=2");					--
		 * where("id=1 AND number=2");						--
		 * where("id>=1 , number <=2");						--
		 * where("id=1 , number=2");						--
		 * where("id=1" , "number=2 AND imie=23423412334");	--
		 * where("id=1" , "number=2 , imie=23423412334");	--
		 * where("id","1","number","2");					--
		 *
		 * where(["id"=>"1","number"=>"2"]);				--
		 *
		 * where(["id>=1 AND number <=2"]);					--
		 * where(["id=1 AND number=2");						--
		 * where(["id>=1 , number <=2"]);					--
		 * where(["id=1 , number=2"]);						--
		 * where(["id=1","number=2 AND imie=23423412334"]);	--
		 * where(["id","1","number","2"]);					--
		 *
		 * 3 - search for ,
		 */
		
		if ( $argc === 1 ) {

			$first = $args[0] ;
			
			$first = ( is_array( $first ) && count( $first ) == 1 && isset( $first[0] ) ) ? $first[0] : $first ;
			
			if ( is_string( $first ) ){

				if ( stristr( $first , "SELECT" ) !== false && stristr( $first , "FROM" ) !== false ) {

					$this->Where .= " AND {$first}" ;

				} else $this->Where .= " AND " . str_replace( " , " , " AND " , $first ) ;

			} else if ( is_array( $first ) ) {
				
				foreach( $first as $k => $v ) if ( is_array( $v ) ) $this->{__FUNCTION__}( $v );
						
				$StringKey = hasStringKey( $first );

				$hasAssign = eachHasChar( $first , '=' ) 
								|| eachHasChar( $first , "`" );

				if ( $StringKey ) {
					
					foreach( $first as $k => $v ) {

						$v20 = str_replace( " " , "" , $v );

						if ( is_string( $k ) ) {

							$v = str_ireplace("'", "\\'", $v);

							$v = str_ireplace('"', '\\"', $v);

							$this->Where .= " AND `{$k}`='{$v}'";

						} else if ( stristr( $v , "SELECT" ) !== false && 
										stristr( $v , "FROM" ) !== false ) {

							$this->Where .= " AND {$v}" ;

						} else if ( ( stristr( $v20 , "in(" ) !== false &&
								stristr( $v20 , ")" ) !== false ) 
									|| substr_count( $v , "`" ) >= 2 ){

							$this->Where .= " AND " . $v ;

						} else { 

							$this->Where .= " AND " . str_replace( " , " , " AND " , $v ) ; 

						}

					}

				} else if ( $hasAssign ) foreach( $first as $v ) {

					$v20 = str_replace( " " , "" , $v );

					if ( stristr( $v , "SELECT" ) !== false && 
								stristr( $v , "FROM" ) !== false ) {

						$this->Where .= " AND {$v}" ;

					} else if ( stristr( $v20 , "in(" ) !== false &&
								stristr( $v20 , ")" ) !== false ){

						$this->Where .= " AND " . $v ;

					} else $this->Where .= " AND " . str_replace( " , " , " AND " , $v ) ;
					
				} else {
					
					for( $i = 0 ; $i <= ( count( $first ) - 2 ) ; $i += 2 ) {

						$f1 = str_ireplace("'", "\\'", $first[$i]);

						$f1 = str_ireplace("'", "\\'", $f1);

						$f2 = str_ireplace("'", "\\'", $first[$i + 1] );

						$f2 = str_ireplace("'", "\\'", $f2 );
						
						$this->Where .= " AND `{$f1}`='{$f2}' ";

					}

				}

			} 

		} else {
			
			$akaArray = false ;
			
			foreach( $args as $values ) {
				
				if ( is_array( $values ) ) {
					
					$this->{__FUNCTION__}( $values );
					
					$akaArray = true ;

				}

			} if ( $akaArray == true ) return $this;
				
			$hasAssgin = eachHasChar( $args , '=' )
							|| eachHasChar( $first , "`" );
			
			if ( $hasAssgin ) foreach( $args as $v ) {

				$v20 = str_replace( " " , "" , $v );
				
				if ( stristr( $v , "SELECT" ) !== false && 
							stristr( $v , "FROM" ) !== false ) {

					$this->Where .= " AND {$v}" ;

				} else if ( stristr( $v20 , "in(" ) !== false &&
								stristr( $v20 , ")" ) !== false ){

					$this->Where .= " AND " . $v ;

				} else $this->Where .= " AND " . str_replace( " , " , " AND " , $v ) ;
				
			} else {
				
				for( $i = 0 ; $i <= ( count( $args ) - 2 ) ; $i += 2 ) {

					$f1 = str_ireplace("'", "\\'", $args[$i]);

					$f1 = str_ireplace("'", "\\'", $f1);

					$f2 = str_ireplace("'", "\\'", $args[$i + 1] );
					
					$f2 = str_ireplace("'", "\\'", $f2 );
					
					$this->Where .= " AND `{$f1}`='{$f2}' ";

				}

			}

		} while( stristr( $this->Where , " AND AND " ) !== false )

			$this->Where = str_replace( " AND AND " , " AND " , $this->Where );

		$this->Where = trim( $this->Where , " /\\") ;
		
		if ( substr( $this->Where , 0 , 3 ) == "AND" ) {
			
			$this->Where = substr( $this->Where , 3 , strlen( $this->Where ) );

		} if ( substr( $this->Where , -3 ) == "AND" ) {
			
			$this->Where = substr( $this->Where , 0 , -3 );

		} $this->Where = trim( $this->Where , " /\\" );

		return $this;
	
	}

	public function limit( $limit = null ) {

		if ( $limit !== null ) $this->Limit = 'LIMIT ' . ( int ) $limit;

		$this->Limit = trim( $this->Limit , " /\\" );
		
		return $this->Limit;
	
	}

	public function offset( $offset = null ) {

		if ( $offset !== null ) $this->Offset = 'OFFSET ' . ( int ) $offset;

		$this->Offset = trim( $this->Offset , " /\\" );
		
		return $this->Offset;
	
	}

	public function order( $colName = null , $sort = "asc" ){
		
		if ( $colName ) {
			
			$colName = trim( $colName ) ;
			
			if ( stripos( $colName , 'asc') === false && 
				stripos( $colName , 'desc') === false ) 
					$colName .= " {$sort}" ;
			
			if ( stripos( $colName , 'ORDER BY') === false ) 

				$colName = " ORDER BY {$colName} " ;
				
			$this->Order = $colName ;
			
		} $this->Order = trim( $this->Order , " /\\" );

		return $this->Order ;
		
	}

	public function exec( $q = null ) {

		if ( method_exists( $this , "buildQuery" ) ) $this->buildQuery( );
		
		$this->Query = str_ireplace( "   " , " " , $this->Query );
		
		$this->Query = str_ireplace( "  " , " " , $this->Query );
		
		$this->Query = str_ireplace( "  " , " " , $this->Query );
		
		$this->Query = ( string ) $this->Query;

		$exe = @parent::exec( $this->Query );
		
		$this->AcceptQuery( $exe );
		
		return $this;
	
	}

}

?>