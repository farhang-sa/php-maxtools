<?php namespace MaxDatabaseManager ;

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

class Select extends Action {

	protected $SelectList = null;

	public function select( ) {

		/**
		 * select("*"); select(["*"]);
		 * select(["id","name","last","nick"])	------A
		 * select(["id"])	--------------------------B
		 * select(["id , name , last , nick"])	------C
		 * select(["id , name" , "last , nick"])	--D
		 * select("id","name","last","nick")	------E
		 * select("id")	------------------------------F
		 * select("id , name , last , nick ")	------G
		 * select("id , name" , "last , nick")	------H
		 */
		$args = func_get_args( );
		
		$argc = func_num_args( );

		if ( $argc == 1 ) {
			
			$first = $args[0];
			
			if ( is_string( $first ) ) {

				$exp = array() ;

				if ( stristr( $first , ")" ) !== false ) {

					$exp[ ] = $first ;

				} else $exp = explode( "," , $first );
				
				if ( count( $exp ) == 1 ) { // --F
					
					$this->SelectList[ $exp[ 0 ] ] = $exp[0] ;
				
				} else { // --G
					
					foreach( $exp as $k => $v ) {
						
						if ( is_string( $k ) ) $this->SelectList[$k] = $v;

						else $this->SelectList[$v] = $v;
					
					}
				
				}
			
			} else if ( is_array( $first ) ) {
				
				if ( count( $first ) == 1 ) {
					
					if ( is_array( $first[0] ) ) {
						
						foreach( $first as $k => $v ) {
							
							$this->{__FUNCTION__}( $v );
						
						} return $this;
					
					} $exp = array() ;

					if ( stristr( $first[0] , ")" ) !== false ) {

						$exp = $first ;

					} else $exp = explode( "," , $first[0] );

					if ( count( $exp ) == 1 ) { // --B
					
						$this->SelectList[ $exp[ 0 ] ] = $exp[0] ;
					
					} else { // --C
						
						foreach( $exp as $k => $v ) {
							
							if ( is_string( $k ) ) $this->SelectList[$k] = $v;

							else $this->SelectList[$v] = $v;
						
						}
					
					}
				
				} else {
					
					$hasComma = eachHasChar( $first , "," );
					
					$hasPO = eachHasChar( $first , "(" );
					
					$hasPE = eachHasChar( $first , ")" );

					if ( $hasComma && ! ( $hasPO && $hasPE ) ) { // --D
						
						foreach( $first as $k => $v ) {
							
							$exp = explode( "," , $v );
							
							foreach( $exp as $k2 => $v2 ) {
								
								if ( is_string( $k2 ) ) $this->SelectList[$k2] = $v2;

								else $this->SelectList[$v2] = $v2;
							
							}
						
						}
					
					} else { // --A
						
						foreach( $first as $k => $v ) {

							if ( is_string( $k ) ) $this->SelectList[$k] = $v;

							else $this->SelectList[$v] = $v;
							
						}
					
					}
				
				}
			
			}
		
		} else {
			
			$akaArray = false;
			
			foreach( $args as $values ) {
				
				if ( is_array( $values ) ) {
					
					$this->{__FUNCTION__}( $values );
					
					$akaArray = true;
				}
			} if ( $akaArray == true ) { return $this; }
			
			$hasComma = eachHasChar( $args , "," );
			
			if ( $hasComma ) { // --D----
				
				foreach( $args as $k => $v ) {
					
					$exp = explode( "," , $v );
					
					foreach( $exp as $k2 => $v2 ) {
						
						if ( is_string( $k2 ) ) $this->SelectList[$k2] = $v2;

						else $this->SelectList[$v2] = $v2;

					}
				
				}
			
			} else { // --A
				
				foreach( $args as $k => $v ) {
					
					if ( is_string( $k ) ) $this->SelectList[$k] = $v;

					else $this->SelectList[$v] = $v;
				
				}
			
			}
		
		}
		
		return $this;
	
	}

	public function delSelect( ) {

		$args = func_get_args( );
		
		foreach( $args as $colmnNames ) {
			
			if ( is_array( $colmnNames ) ) {
				
				foreach( $colmnNames as $realName ) {
					
					if ( is_array( $realName ) ) {
						
						$this->{__FUNCTION__}( $realName );
					
					} else { $this->deleteUpdatesHelper( $realName ); }
				
				}
			
			} else { $this->deleteUpdatesHelper( $colmnNames ); }
		
		} return $this;
	
	}

	public function getQuery( ){

		$this->buildQuery();
		
		return $this->Query ;

	}
	
	protected function buildQuery( ) {

		$cdb = $this->Database( );
		
		$ctb = $this->Table( );

		if ( empty( $this->SelectList ) ) return $this ;

		$tableValue = ( $cdb ) ? $cdb . "." . $ctb : $ctb ;
		
		$TheColumns = "";

		foreach( $this->SelectList as $colName => $AsName ) {
			
			if ( ! stristr( $colName , "(" ) && ! stristr( $colName , ")" ) ) {
				
				$TheColumns .=  "{$tableValue}.{$colName}";
			
			} else {

				$TheColumns .= " {$colName}";
			
			} if ( ! stristr( $TheColumns , "as" ) ) {

				$TheColumns .= " as `$AsName` , ";

			} $TheColumns = trim( $TheColumns , "/\\ , " ) . " , " ;

			if ( strstr( $colName , "*" ) ) { $TheColumns = "*"; break; }
		
		} if ( strstr( $TheColumns , "*" ) === false ) 

			$TheColumns = substr( $TheColumns , 0 , -3 );

		$this->Where .= ( strlen( $this->Order ) ) ? " " . $this->Order : "" ;
		
		$this->Limit .= ( strlen( $this->Offset ) ) ? " " . $this->Offset : "" ; 
		
		$this->Query = $this->Connector()->QueryDriver->select( 
			$tableValue , $TheColumns , $this->Where , $this->Limit );
		
		//if( stristr( $this->Query , "lms" ) ) print $this->Query . "<br>" ;

	}

	protected function deleteSelectsHelper() {

		$args = func_get_args( );
		
		$newList = array ();
		
		foreach( $args as $colmnName ) {
			
			foreach( $this->SelectList as $key => $value ) {
				
				if ( strtolower( $colmnName ) != strtolower( $key ) ) {
					
					$newList[$key] = $value;
				
				}
			
			}
		
		}
		
		$this->SelectList = $newList;
	
	}

}

?>