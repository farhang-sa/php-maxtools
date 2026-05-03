<?php namespace MaxTools ;

function findBestMatchKey( $needle , $stack ){

	if( ! is_array( $stack ) || empty( $stack ) || empty( $needle ) )
		return false ;

	$nNeedle = strtolower( trim( MaxTools\RealPersian($needle) ) );

	$match = 0 ;
	$distance = 10000000000 ;
	$final = null ;

	foreach ($stack as $key => $value) {

		$nValue = strtolower( trim( MaxTools\RealPersian($value) ) );

		$mch = 0 ;
		$sim = similar_text( $nNeedle , $nValue , $mch );
		
		if( $mch === $match ) :

			$dis = levenshtein( $nNeedle , $nValue );
			if( $dis <= $distance ):
				$distance = $dis ;
				$match = $mch ;
				$final = $key ;
			endif ;

		elseif( $mch > $match ) :

			$match = $mch ;
			$final = $key ;

		endif ;

	}

	return $final ;

}

function findBestMatchValue( $needle , $stack ){

	if( ! is_array( $stack ) || empty( $stack ) || empty( $needle) )
		return false ;

	$searchValues = MaxTools\isIndexedArray( $stack ) ;

	$nNeedle = strtolower( trim( MaxTools\RealPersian($needle) ) );

	$match = 0 ;
	$distance = 10000000000 ;
	$final = null ;
	foreach ($stack as $key => $value) {

		$nValue = $searchValues ? $value : $key ;
		$nValue = strtolower( trim( MaxTools\RealPersian($nValue) ) );

		$mch = 0 ;
		$sim = similar_text( $nNeedle , $nValue , $mch );
		if( $mch === $match ) :

			$dis = levenshtein( $nNeedle , $nValue );
			if( $dis <= $distance ):
				$distance = $dis ;
				$match = $mch ;
				$final = $value ;
			endif ;

		elseif( $mch > $match ) :

			$match = $mch ;
			$final = $value ;

		endif ;

	}

	return $final ;

}

