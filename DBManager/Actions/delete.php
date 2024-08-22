<?php namespace MaxDatabaseManager ;

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

class Delete extends Action {

	public function delete( ) {

		$args = func_get_args( );
		
		foreach ( $args as $v ) if ( ! empty( $v ) && isset( $v ) ) $this->where( $v );
			
		return $this;
	
	}

	protected function buildQuery( ) {

		$cdb = $this->Database( );
		
		$ctb = $this->Table( );
		
		$tableValue = ( $cdb ) ? $cdb . "." . $ctb : $ctb ;

		$this->Limit .= ( strlen( $this->Offset ) ) ? " " . $this->Offset : "" ;
		
		$this->Query = $this->Connector( )->QueryDriver->delete( $tableValue , $this->Where , $this->Limit );
	
	}

}

?>