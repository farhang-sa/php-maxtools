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

function generate_uuid(){

    // Generate 16 bytes of random data
    $data = random_bytes(16);

    // Set version bits (4 for UUID version 4)
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);

    // Set version bits (1, 2, or 3 for UUID variant 1)
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    // Output the UUID in hexadecimal format
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

}