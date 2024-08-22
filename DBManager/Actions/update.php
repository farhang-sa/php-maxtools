<?php namespace MaxDatabaseManager ;

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

class Update extends Action {

	protected $UpdateList = null;

	public function Update( ) {
		
		/**
		 * update("id=1 , name=farhang")	----------A 
		 * update(["id=1 , name=farhang"])	----------B 
		 * update(["id=1","name=farhang"])	----------C 
		 * update(["id","1","name","farhang"])	------D 
		 * update(["id"=>"1","name"=>"farhang"])	--E 
		 * update(["id,1,name,farhang"])	----------F 
		 * update("id","1","name","farhang")	------G 
		 * update("id=1","name=farhang")	----------H 
		 * update("id,1,name,farhang")	--------------I
		 */
		
		$args = func_get_args( );
		
		$argc = func_num_args( );
		
		if ( $argc == 1 ) {
			
			$first = $args[0];
			
			if ( is_string( $first ) ) {
				
				$exp = explode( "," , $first );
				
				$hasAssign = eachHasChar( $exp , '=' );
				
				$hasComma = eachHasChar( $exp , "," );
				
				if ( $hasComma ) { // --I
					
					for( $i = 0 ; $i <= ( count( $exp ) - 2 ) ; $i += 2 ) {
						
						$ColName = $exp[$i];
						
						$ColValu = $exp[$i + 1];
						
						$this->UpdateList[$ColName] = $ColValu;
					
					}
				
				} else if ( $hasAssign ) { // --A
					
					foreach( $exp as $k => $v ) {
						
						$ex = explode( '=' , $v );
						
						if ( count( $ex ) == 2 ) {
							
							$this->UpdateList[$ex[0]] = $ex[1];
						
						} else {
							
							$this->UpdateList[$k] = $v;
						
						}
					
					}
				
				}
			
			} else if ( is_array( $first ) ) {
				
				if ( count( $first ) == 1 && hasStringKey( $first ) ) {
					
					foreach( $first as $k => $v ) {
						
						$this->UpdateList[$k] = $v;
					
					}
				
				} else if ( count( $first ) == 1 ) { // --B
					
					if ( is_array( $first[0] ) ) {
						
						foreach( $first as $k => $v ) {
							
							$this->{__FUNCTION__}( $v );
						
						}
						
						return $this;
					
					}
					
					$arg = $first[0];
					
					$exp = explode( ',' , $arg );
					
					$hasAssign = eachHasChar( $exp , '=' );
					
					if ( $hasAssign ) {
						
						foreach( $exp as $k => $v ) {
							
							$ex = explode( '=' , $v );
							
							if ( count( $ex ) == 2 ) {
								
								$this->UpdateList[$ex[0]] = $ex[1];
							
							} else {
								
								$this->UpdateList[$k] = $v;
							
							}
						
						}
					
					} else { // --F
						
						for( $i = 0 ; $i <= ( count( $exp ) - 2 ) ; $i += 2 ) {
							
							$ColName = $exp[$i];
							
							$ColValu = $exp[$i + 1];
							
							$this->UpdateList[$ColName] = $ColValu;
						
						}
					
					}
				
				} else {
					
					$hasStringKey = hasStringKey( $first );
					
					$hasAssign = eachHasChar( $first , '=' );
					
					if ( $hasStringKey ) { // --E
						
						foreach( $first as $colName => $colValu ) {
							
							$this->UpdateList[$colName] = $colValu;
						
						}
					
					} else if ( $hasAssign ) { // --C
						
						foreach( $first as $col => $v ) {
							
							$ex = explode( '=' , $v );
							
							if ( count( $ex ) == 2 ) {
								
								$this->UpdateList[$ex[0]] = $ex[1];
							
							} else {
								
								$this->UpdateList[$k] = $v;
							
							}
						
						}
					
					} else { // --D
						
						for( $i = 0 ; $i <= ( count( $first ) - 2 ) ; $i += 2 ) {
							
							$ColName = $first[$i];
							
							$ColValu = $first[$i + 1];
							
							$this->UpdateList[$ColName] = $ColValu;
						
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
				
				return this;
			
			}
			
			$hasAssign = eachHasChar( $args , '=' );
			
			if ( $hasAssign ) { // --H
				
				foreach( $args as $key => $val ) {
					
					$val = trim( $val );
					
					$ex = explode( '=' , $val );
					
					$this->UpdateList[$ex[0]] = $ex[1];
				
				}
			
			} else { // --G
				
				for( $i = 0 ; $i <= ( count( $args ) - 2 ) ; $i += 2 ) {
					
					$ColName = $args[$i];
					
					$ColValu = $args[$i + 1];
					
					$this->UpdateList[$ColName] = $ColValu;
				
				}
			
			}
		
		}
		
		return $this;
	
	}

	public function delUpdate( ) {

		$args = func_get_args( );
		
		foreach( $args as $colmnNames ) {
			
			if ( is_array( $colmnNames ) ) {
				
				foreach( $colmnNames as $realName ) {
					
					if ( is_array( $realName ) ) {
						
						$this->{__FUNCTION__}( $realName );
					
					} else {
						
						$this->deleteUpdatesHelper( $realName );
					
					}
				
				}
			
			} else {
				
				$this->deleteUpdatesHelper( $colmnNames );
			
			}
		
		}
		
		return $this;
	
	}

	protected function buildQuery( ) {

		$cdb = $this->Database( );
		
		$ctb = $this->Table( );
		
		$tableValue = ( $cdb ) ? $cdb . '.' . $ctb : $ctb;
		
		$TheColumns = '' ;
		
		foreach( $this->UpdateList as $colName => $insValue ) {
			
			if ( stristr( $insValue , $colName ) 
				&&  ( stristr( $insValue , '+' ) || 
					stristr( $insValue , '-' ) || stristr( $insValue , '*' ) || stristr( $insValue , '/' ) ) 
				&& ( ! stristr( $insValue , '<' ) && ! stristr( $insValue , '>' ) ) ) {
				
				$insValue = str_ireplace( "'" , "\\'", $insValue ) ;
				
				$insValue = str_ireplace( '"' , '\\"', $insValue ) ;

				$TheColumns .= " {$colName}={$insValue} ,";
			
			} else {
				
				$insValue = str_ireplace( "'" , "\\'", $insValue ) ;
				
				$insValue = str_ireplace( '"' , '\\"', $insValue ) ;
				
				$TheColumns .= " {$colName}='{$insValue}' ,";
			
			}
		
		} $TheColumns = substr( $TheColumns , 0 , - 1 );
		
		$TheColumns = ( string ) "{$TheColumns}";

		$this->Limit .= ( strlen( $this->Offset ) ) ? ' ' . $this->Offset : '' ; 
		
		$this->Query = $this->Connector()->QueryDriver->update( $tableValue , $TheColumns , $this->Where , $this->Limit );

	}

	protected function deleteUpdatesHelper( ) {

		$args = func_get_args( );
		
		$newList = array ();
		
		foreach( $args as $colmnName ) {
			
			foreach( $this->UpdateList as $key => $value ) {
				
				if ( strtolower( $colmnName ) != strtolower( $key ) ) {
					
					$newList[$key] = $value;
				
				}
			
			}
		
		} $this->UpdateList = $newList;
		
		return $this;
	
	}

}

?>