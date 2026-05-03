<?php namespace MaxTools ;

// DS!
function DS(){
  return DIRECTORY_SEPARATOR ; }

// Require functions
$dirPath = realpath( __DIR__ ) . DS() . 'Functions' . DS() ;
foreach( scandir( $dirPath ) as $file )
  if( $file === '.' || $file === '..' )
    continue ;
  else if( strtolower( substr( $file , -4) ) === '.php' )
    include_once $dirPath . $file ;
  else die( $dirPath . $file );

