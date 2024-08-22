<?php

defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

final class SqliteQueryDriver extends StandardSQLDriver {

	public function showTables( $db ) {

		return "SELECT NAME,TBL_NAME FROM SQLITE_MASTER WHERE TYPE='table' ;";
	
	}

	public function tableExists( $db , $table ) {

		return "SELECT NAME,TBL_NAME FROM SQLITE_MASTER WHERE tbl_name='{$table}' AND TYPE='table' ;";
	
	}

	public function dropTable( $db , $table ) {

		return "DROP TABLE IF EXISTS {$db}.{$table} ;";
	
	}

	public function describeTable( $db , $table ) {

		return "PRAGMA TABLE_INFO('{$table}') ;";
	
	}

	public function insert( $table , $columns , $values ) {
		
		$table = ( stristr( $table , "." ) ) ? ( string ) explode( "." , $table , 2 )[ 1 ] : $table ;

		return "INSERT INTO $table ({$columns}) VALUES {$values} ;";
	
	}

	public function update( $table , $columns , $Where , $limitOffset = '' ) {

		$update = "UPDATE {$table} SET {$columns} ";
		
		if ( $Where ) {
			
			$update .= "WHERE {$Where} ";
		
		}
		
		$update .= "{$limitOffset} ; ";
		
		return $update;
	
	}

	public function select( $table , $columns , $Where , $limitOffset = '' ) {

		$tableSet = null;
		
		if ( stristr( $table , '.' ) ) {
			
			$explo = explode( '.' , $table , 2 );
			
			$tableSet = $explo[1];
		
		} else $tableSet = $table;
		
		$select = "SELECT {$columns} FROM {$tableSet} ";
		
		if ( $Where ) {
			
			$select .= "WHERE {$Where} ";
		
		}
		
		$select .= "{$limitOffset} ; ";
		
		return $select;
	
	}

	public function delete( $table , $where , $limitOffset = '' ) {

		$tableSet = null;
		
		if ( stristr( $table , '.' ) ) {
			
			$explo = explode( '.' , $table , 2 );
			
			$tableSet = $explo[1];
		
		} else $tableSet = $table;

		$delete = "DELETE FROM {$tableSet} ";
		
		if ( $where ) $delete .= "WHERE {$where} ";
		
		$delete .= "{$limitOffset} ; ";
		
		return $delete;
	
	}

}
?>