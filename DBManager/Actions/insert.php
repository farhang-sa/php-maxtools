<?php namespace MaxDatabaseManager ;

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

class Insert extends Action {

	protected $depth = -1 ;

	protected $cols = array ();

	protected $InsertList = array ();

	public function TableColumns( $columnsArray = array( ) ) {

		if ( is_array( $columnsArray ) ) $this->cols = $columnsArray;
		
		return $this;
	
	}

	public function Insert( ) {
		
		/**
		 * insert(["id"=>"1" , "name"=>"farhang"])	-------A 
		 * insert(["id=1" , "name=farhang"])	-----------B 
		 * insert(["id=1 , name=farhang"])	---------------C 
		 * insert(["1" , "farhang"])	-------------------D 
		 * insert(["1 , farhang"])	-----------------------E 
		 * insert("id=1","name=farhang")	---------------F 
		 * insert("id=1 , name=farhang")	---------------G 
		 * insert("1","farhang")	-----------------------H 
		 * insert("1 , farhang")	-----------------------I
		 */
		
		$this->depth = ( $this->depth + 1 );
		
		$args = func_get_args( );
		
		$argc = func_num_args( );
		
		if ( $argc == 1 ) {
			
			$first = $args[0];
			
			if ( is_string( $first ) ) {
				
				$exp = explode( ',' , $first );
				
				$hasAssign = eachHasChar( $exp , '=' );
				
				$hasComma = eachHasChar( $exp , ',' );
				
				if ( $hasAssign && $hasComma ) { // --G
					
					foreach( $exp as $k => $v ) {
						
						$ex = explode( '=' , $v );
						
						if ( count( $ex ) == 2 ) {
							
							$this->InsertList[$this->depth][$ex[0]] = $ex[1];
						
						} else {
							
							$this->InsertList[$this->depth][$k] = $v;
						
						}
					
					}
				
				} else if ( $hasComma ) { // --I
					
					foreach( $exp as $k => $v ) {
						
						$this->InsertList[$this->depth][$k] = $v;
					
					}
				
				}
			
			} else if ( is_array( $first ) ) {
				
				if ( count( $first ) == 1 && hasStringKey( $first ) ) {
					
					foreach( $first as $k => $v ) {
						
						$this->InsertList[$this->depth][$k] = $v;
					
					}
				
				} else if ( count( $first ) == 1 ) {
					
					if ( is_array( $first[0] ) ) {
						
						foreach( $first as $k => $v ) {
							
							$this->{__FUNCTION__}( $v );
						
						}
						
						return $this;
					
					}
					
					$arg = $first[0];
					
					$exp = explode( "," , $arg );
					
					$hasAssign = eachHasChar( $exp , '=' );
					
					$hasComma = eachHasChar( $exp , "," );
					
					if ( $hasAssign && $hasComma ) { // --C
						
						foreach( $exp as $k => $v ) {
							
							$ex = explode( '=' , $v );
							
							if ( count( $ex ) == 2 ) {
								
								$this->InsertList[$this->depth][$ex[0]] = $ex[1];
							
							} else {
								
								$this->InsertList[$this->depth][$k] = $v;
							
							}
						
						}
					
					} else if ( $hasComma ) { // --E
						
						foreach( $exp as $k => $v ) {
							
							$this->InsertList[$this->depth][$k] = $v;
						
						}
					
					}
				
				} else {
					
					$hasStringKey = hasStringKey( $first );
					
					if ( $hasStringKey ) { // --A
						
						foreach( $first as $k => $v ) {
							
							$this->InsertList[$this->depth][$k] = $v;
						
						}
					
					} else {
						
						$hasAssign = eachHasChar( $first , '=' );
						
						if ( $hasAssign ) { // --B
							
							foreach( $first as $k => $v ) {
								
								$ex = explode( '=' , $v );
								
								if ( count( $ex ) == 2 ) {
									
									$this->InsertList[$this->depth][$ex[0]] = $ex[1];
								
								} else {
									
									$this->InsertList[$this->depth][$k] = $v;
								
								}
							
							}
						
						} else { // --D
							
							foreach( $first as $k => $v ) {
								
								$this->InsertList[$this->depth][$k] = $v;
							
							}
						
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
			
			}
			
			if ( $akaArray == true ) {
				
				return $this;
			
			}
			
			$hasAssign = eachHasChar( $args , '=' );
			
			if ( $hasAssign ) { // --F
				
				foreach( $args as $k => $v ) {
					
					$ex = explode( '=' , $v );
					
					if ( count( $ex ) == 2 ) {
						
						$this->InsertList[$this->depth][$ex[0]] = $ex[1];
					
					} else {
						
						$this->InsertList[$this->depth][$k] = $v;
					
					}
				
				}
			
			} else { // --H
				
				foreach( $args as $k => $v ) {
					
					$this->InsertList[$this->depth][$k] = $v;
				
				}
			
			}
		
		}
		
		return $this;
	
	}

	public function delInserts( ) {

		$args = func_get_args( );
		
		foreach( $args as $colmnNames ) {
			
			if ( is_array( $colmnNames ) ) {
				
				foreach( $colmnNames as $realName ) {
					
					if ( is_array( $realName ) ) {
						
						$this->{__FUNCTION__}( $realName );
					} else {
						
						$this->deleteInsertsHelper( $realName );
					}
				}
			} else {
				
				$this->deleteInsertsHelper( $colmnNames );
			}
		}
		
		return $this;
	
	}
	
	protected function buildQuery( ) {

		$cdb = $this->Database( );
		
		$ctb = $this->Table();
		
		$tableValue = ( $cdb ) ? $cdb . '.' . $ctb : $ctb;
		
		$TheColumns = '';

		if( count( $this->InsertList ) == 1 )

			$UpdateKey = '' ;
		
		foreach( $this->InsertList as $k => $InsertRow ) {
			
			if ( is_array( $InsertRow ) ) {
				
				$TheColumns .= '(';
				
				foreach( $this->cols as $k => $v ) {
					
					if ( isset( $InsertRow[$v] ) && is_string( $InsertRow[$v] ) ){

						$TheColumns .= " '" . str_ireplace( "'" , "\\'" , $InsertRow[$v] ) . "' ,";

						if( $UpdateKey ) 

							$UpdateKey .= "`{$v}`='{$InsertRow[$v]}' , ";
					
					} else $TheColumns .= " '' ,";
				
				} $TheColumns = substr( $TheColumns , 0 , - 1 );
				
				$TheColumns .= ' ) ,' ;
			
			}
		
		} $TheColumns = substr( $TheColumns , 0 , - 1 );
		
		$Columns = "`" . @implode( "` , `" , $this->cols ) . "`";

		if( $UpdateKey ) $UpdateKey = trim( $UpdateKey , ' ,' ) ;

		$this->Query = $this->Connector()->QueryDriver->insert( $tableValue , $Columns , $TheColumns );
		
	}

	protected function deleteInsertsHelper( ) {

		$args = func_get_args( );
		
		$newList = array ();
		
		foreach( $args as $colmnName ) {
			
			foreach( $this->InsertList as $k => $insertRow ) {
				
				foreach( $insertRow as $key => $value ) {
					
					if ( strtolower( $colmnName ) != strtolower( $key ) ) {
						
						$newList[$k][$key] = $value;
					
					}
				
				}
			
			}
		
		}
		
		$this->InsertList = $newList;
	
	}

}

?>