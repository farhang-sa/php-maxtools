<?php namespace MaxTools ;

// Copy All Files from $src folder to $dst folder
function Copy( $src , $dst , $overwrite = true ) { 

  if( empty( $src ) || empty( $dst ) )
    return;

  // copy file to new-file/folder
  if( is_file( $src ) ){

    if( is_file( $dst ) && ! $overwrite )
      return ;

    if( is_dir( $dst ) ){ // copy file to new-path

      $path = $dst . MaxTools_DS . basename( $src ) ;
      if( is_file( $path ) && ! $overwrite )
        return ;
      @\copy( $src , $path );
      return ;

    } // else : file to new-file

    $path = dirname( $dst );
    if( ! is_dir( $path ) )
      @mkdir( $path );
    @\copy( $src , $dst );
    return ;

  } // else : copy folder to folder

  $dir = opendir( $src ); 

  if( ! is_dir( $dst ) )
    @mkdir( $dst ); 

  while( false !== ( $file = readdir($dir) ) ) { 

    if( $file === '.' || $file == '..' )
      continue ; // ignore!

    if ( is_file($src . MaxTools_DS . $file) ) 
      @\copy($src . MaxTools_DS . $file , $dst . MaxTools_DS . $file); 
    else Copy($src . MaxTools_DS . $file , $dst . MaxTools_DS . $file); 

  } closedir($dir); 
    
}

// Delete file or entire directory!
function Delete( $src ){

  if( is_file( $src ) )
    @unlink( $src );

  else if( is_dir( $src ) ) {

    // clean folder then delete!
    $sc = scandir( $src );

    foreach( $sc as $fi ){

      if( $fi === '.' || $fi === '..' )
        continue;

      if( is_file( $src . MaxTools_DS . $fi ) )
        @unlink( $src . MaxTools_DS . $fi );
      else Delete( $src . MaxTools_DS . $fi );

    } @rmdir( $src );

  }

}

// cut file/folder to new-file/folder
function Move( $src , $dest ){

  Copy( $src , $dest );
  Delete( $src );

}

// Find Directory in list folders
function FindDirectory( $root , $list = array() ) {

  if( ! $root || ! is_dir( $root ) )
    return false ;

  $address = null;
      
  $list = ( is_array( $list ) ) ? $list : [ $list ] ;
      
  foreach( $list as $directoryName ) {
        
    $newDire = $root . MaxTools_DS . $directoryName ;
    $newDire = trim( $newDire , " \\/" );
        
    if ( is_dir( $newDire ) ) {
          
      $address = $newDire;
      break;
        
    } else if ( is_dir( $newDire = $root . MaxTools_DS . ucwords( $directoryName ) ) ) {
          
      $address = $newDire;
      break;
        
    } else if ( is_dir( $newDire = $root . MaxTools_DS . ucfirst( $directoryName ) ) ) {
          
      $address = $newDire;
      break;
        
    } else if ( is_dir( $newDire = $root . MaxTools_DS . lcfirst( $directoryName ) ) ) {
          
      $address = $newDire;
      break;
        
    } else if ( is_dir( $newDire = $root . MaxTools_DS . strtolower( $directoryName ) ) ) {
          
      $address = $newDire;  
      break;
        
    } else if ( is_dir( $newDire = $root . MaxTools_DS . strtoupper( $directoryName ) ) ) {
          
      $address = $newDire;
      break;
        
    }
      
  } if ( $address && is_dir( $address ) ) 
    return realpath( $address );
  
  return false;

}

// Find File in list folders
function FindFile( $root = null , $list = array() ) {

  if( ! $root || ! is_dir( $root ) )
    return false ;

  $address = null;

  if( ! $root ) return false ;
      
  if ( ! is_dir( $root ) ) return false ;
      
  if ( is_array( $list ) ) {
        
    foreach( $list as $FileName ) {
          
      $newFile = $root . MaxTools_DS . $FileName;
          
      if ( is_file( $newFile ) ) {
            
        $address = $newFile;
        break;
          
      } else if ( is_file( $newFile = $root . MaxTools_DS . strtolower( $FileName ) ) ) {
            
        $address = $newFile;
        break;
      
      } else if ( is_file( $newFile = $root . MaxTools_DS . strtoupper( $FileName ) ) ) {
            
        $address = $newFile;
        break;
          
      } else if ( is_file( $newFile = $root . MaxTools_DS . ucwords( $FileName ) ) ) {
            
        $address = $newFile;
        break;
          
      } else if ( is_file( $newFile = $root . MaxTools_DS . ucfirst( $FileName ) ) ) {
            
        $address = $newFile;
        break;
          
      } else if ( is_file( $newFile = $root . MaxTools_DS . lcfirst( $FileName ) ) ) {
            
        $address = $newFile;
        break;
          
      } else if ( is_file( $newFile = FindFile( $root , $FileName ) ) ) {
            
        $address = $newFile;
        break;
          
      }
        
    }
      
  } else {
        
    $scand = scandir( $root );
        
    foreach( $scand as $arrayMember ) {
          
      $fileAddress = $root . MaxTools_DS . $arrayMember;
          
      $info = pathinfo( $fileAddress );
          
      $filename = strtolower( $info['filename'] );
          
      $basename = strtolower( $info['basename'] );
          
      $subject = strtolower( $list );
          
      if ( $subject == $filename || $subject == $basename ) {
            
        $address = $fileAddress;
        break;
          
      }
        
    }
      
  } if ( $address && is_file( $address ) ) 
    return realpath( $address );
      
  return false;
    
}

// Find File Absolute Path
function FindFilePath( $root = null , $RouteArray = array() , $fileType = array( 'php' ) ) {

  if( ! $root || ! is_dir( $root ) )
    return false ;
  
  $mainDire = $root ;
  $mainFile = null ;

  $backDire = null ;
  $backFile = null ;
  
  $RouteArray =  ( is_string( $RouteArray ) ) ? [ $RouteArray ] : $RouteArray;
  $RouteArray =  ( is_array( $RouteArray ) ) ? $RouteArray : array();

  $FileRoute = array_values( $RouteArray ) ;
  
  $fileType = ( is_array( $fileType ) ) ? $fileType : [ $fileType ] ;
  $fileType = ( empty( $fileType ) ) ? [ 'php' ] : $fileType ;
  
  foreach ( $FileRoute as $newDirectionName ) {

    $newBackDire = FindDirectory( $backDire , $newDirectionName )  ;
    $newMainDire = FindDirectory( $mainDire , $newDirectionName )  ;

    if ( $newMainDire ) {
        
      $backDire = ( $newBackDire ) ? $newBackDire : $mainDire ;
      $mainDire = $newMainDire ;
        
    } else if ( $mainDire === $backDire && $newBackDire ){
        
      $backDire = $mainDire ;
      $mainDire = $newBackDire ;
        
    } $searchArray = array() ;
    
    foreach( $fileType as $ft ) $searchArray[] = "{$newDirectionName}.{$ft}" ;
    
    $newBackMedia = FindFile( $backDire , $searchArray );
    $newMainMedia = FindFile( $mainDire , $searchArray );
    
    if ( $newMainMedia ){
        
      $backFile = ( $newBackMedia ) ? $newBackMedia : $mainFile ;
      $mainFile = $newMainMedia ;
        
    } else if ( $newBackMedia ){
        
      $backFile = $mainFile ;
      $mainFile = $newBackMedia ;
        
    }

  } if ( $mainFile ) 
    return $mainFile ;
  else if ( $backFile ) 
    return $backFile ;
  
  return null ; 
    
}