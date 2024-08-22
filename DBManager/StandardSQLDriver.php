<?php defined( 'MaxDatabaseManagerExec' ) or die( 'Access Denied' );

abstract class StandardSQLDriver {

	public function createDatabase( $db ) {

		return "CREATE DATABASE IF NOT EXISTS '{$db}' ;";
	
	}

	public function renameDatabase( $db , $newName ) {

		return "ALTER DATABASE '{$db}' UPGRADE DATA DIRECTORY {$newName}";
	
	}

	public function dropDatabase( $db ) {

		return "DROP DATABASE '{$db}' ;";
	
	}

	public function showDatabases( ) {

		return 'SHOW DATABASES ;' ;
	
	}

	public function databaseExists( $db ) {

		return "SHOW DATABASES LIKE '{$db}' ;";
	
	}

	public function createTable( $db , $fields ) {

		if( stristr( $db , '.' ) === false && stristr( $db , '`' ) === false )
			$db = "`{$db}`" ;

		return "CREATE TABLE IF NOT EXISTS {$db}( {$fields} ) ;";
	
	}

	public function showTables( $db ) {

		if( stristr( $db , '.' ) === false && stristr( $db , '`' ) === false )
			$db = "`{$db}`" ;

		return "SHOW FULL TABLES IN {$db} ;";
	
	}

	public function tableExists( $db , $table ) {

		return "SHOW FULL TABLES IN {$db} LIKE '{$table}' ;";
	
	}

	public function dropTable( $db , $table ) {

		return "DROP TABLE IF EXISTS {$db}.{$table} ;";
	
	}

	public function columnExists( $db , $table , $column ) {

		return "SHOW FULL COLUMNS FROM {$db}.{$table} WHERE Field = '{$column}';";
	
	}

	public function createColumn( $db , $table , $column ) {

		return "ALTER TABLE {$db}.{$table} ADD COLUMN {$column}";
	
	}

	public function alterColumn( $db , $table , $column ) {

		return "ALTER TABLE {$db}.{$table} MODIFY COLUMN {$column}";
	
	}

	public function dropColumn( $db , $table , $column ) {

		return "ALTER TABLE {$db}.{$table} DROP COLUMN {$column}";
	
	}

	public function describeTable( $db , $table ) {

		return "SHOW FULL COLUMNS FROM {$db}.{$table} ;";
	
	}

	public function insert( $table , $columns , $values ) {

		if( stristr( $table , '.' ) === false && stristr( $table , '`' ) === false )
			$table = "`{$table}`" ;

		return "INSERT INTO {$table} ( {$columns} ) VALUES {$values} ;";
	
	}

	public function update( $table , $columns , $Where , $limitOffset = null ) {

		if( stristr( $table , '.' ) === false && stristr( $table , '`' ) === false )
			$table = "`{$table}`" ;

		$update = "UPDATE {$table} SET {$columns} ";
		
		if ( strlen( $Where ) ) $update .= "WHERE {$Where} ";
		
		if ( strlen( $limitOffset ) ) $update .= "{$limitOffset} ";

		$update .= ";" ;
		
		return $update;
	
	}

	public function select( $table , $columns , $Where , $limitOffset = '' ) {

		if( stristr( $table , '.' ) === false && stristr( $table , '`' ) === false )
			$table = "`{$table}`" ;

		$select = "SELECT {$columns} FROM {$table} ";
		
		if ( strlen( $Where ) ) $select .= "WHERE {$Where} ";
		
		if ( strlen( $limitOffset ) ) $select .= "{$limitOffset} ";

		$select .= ";" ;
		
		return $select;
	
	}

	public function delete( $table , $Where , $limitOffset = '' ) {

		if( stristr( $table , '.' ) === false && stristr( $table , '`' ) === false )
			$table = "`{$table}`" ;

		$delete = "DELETE FROM {$table} ";
		
		if ( strlen( $Where ) ) $delete .= "WHERE {$Where} ";
		
		if ( strlen( $limitOffset ) ) $delete .= "{$limitOffset} ";

		$delete .= ";" ;
		
		return $delete;
	
	}

}