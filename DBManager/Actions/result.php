<?php namespace MaxDatabaseManager ;

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

class Result {

	protected $results = null;

	protected $Query = null;

	protected $QueryCount = 0;

	protected $Rows = 0;

	protected $cRow = 0;

	protected $depth = 0;

	public function Result( $ASSOCC_RESULTS = null ) {

		if ( $ASSOCC_RESULTS !== null ) {
			
			$this->results = $ASSOCC_RESULTS;
			
			$this->findDepth( );
			
			$this->QuerySelector( );
		
		} return $this->getData( 0 );
		
	}

	protected function findDepth( $TheDepthValue = null ) {

		$TheDepthValue = ( $TheDepthValue ) ? $TheDepthValue : $this->results ;
		
		if ( is_array( $TheDepthValue ) ) {
			
			$this->depth = $this->depth + 1 ;
			
			if ( isset( $TheDepthValue[0] ) ) {
				
				$this->findDepth( $TheDepthValue[0] );
			
			} else { return null ; }
		
		} else { return null ; }
	
	}

	protected function QuerySelector( ) {

		$TheQueryValue = $this->results;
		
		if ( is_array( $TheQueryValue ) ) {
			
			if ( $this->depth >= 3 ) {
				
				$this->QueryCount = count( $TheQueryValue );
				
				$this->Rows = count( $TheQueryValue[0] ) - 1;
				
				$this->Query = $TheQueryValue[0];
			
			} else {
				
				$this->QueryCount = 1;
				
				$this->Rows = count( $TheQueryValue ) - 1;
				
				$this->Query = $TheQueryValue;
			
			}
		
		} else {
			
			$this->QueryCount = 1;
			
			$this->Rows = 1;
			
			$this->Query = $TheQueryValue;
		
		}
	
	}

	public function GetQuery( $Qnumber = null ) { return $this->QueryResults( $Qnumber ); }

	public function QueryResults( $Qnumber = null ) {

		if ( $Qnumber ) {
			
			if ( $Qnumber <= $this->QueryCount && $this->depth >= 3 ) {
				
				$this->Query = $this->results[$Qnumber];
			
			}
		
		} else {
			
			if ( $this->Query ) { return $this->Query; }

			else { return false; }
		
		}
	
	}

	public final function Last() { return $this->Rows; }

	public final function getData( $rowNumber = null ) {
	
		if ( $rowNumber !== null ) {
			
			if ( $this->Query && $this->Rows >= $rowNumber ) {
				
				if ( is_array( $this->Query ) ) 

				return $this->Query[$rowNumber];
			
			} else { return false; }
		
		} else {
			
			$row = $this->cRow;

			if ( $row > $this->Rows ) {
				
				$this->cRow = 0;
				
				return false;
			
			} else {
				
				$this->cRow = $this->cRow + 1;

				return $this->getData( $row );
			
			}
		
		}
	
	}

}