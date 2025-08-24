<?php namespace MaxTools;

// convert english numbers to persian
function PersianNumbers( $numsStr ){

  $numsStr = ( string ) $numsStr ;
  if( strlen( $numsStr ) == 0 ) 
    return '' ;

  $PNL = array( '۰' => '0' , '۱' => '1' , 
    '۲' => '2' , '۳' => '3' , '۴' => '4' , '۵' => '5' , 
    '۶' => '6' , '۷' => '7' , '۸' => '8' , '۹' => '9' );

  foreach ($PNL as $key => $value ) 
    $numsStr = str_ireplace( $value , $key , $numsStr ) ;

  return $numsStr ;

}

// convert persian numbers to english
function RealNumbers( $numsStr = null ){

  $numsStr = ( string ) $numsStr ;
  if( strlen( $numsStr ) == 0 ) 
    return '' ;

  $PNL = array( '۰' => '0' , '۱' => '1' , 
    '۲' => '2' , '۳' => '3' , '۴' => '4' , '۵' => '5' , 
    '۶' => '6' , '۷' => '7' , '۸' => '8' , '۹' => '9' );

  foreach ($PNL as $key => $value ) 
    $numsStr = str_ireplace( $key , $value, $numsStr ) ;

  return $numsStr ;

}

// convert arabic/non-unicode persian to real persian words
function RealPersian( $str ){

  $str = ( string ) $str ;

  if( strlen( $str ) == 0 ) return '' ;
  $str = RealNumbers( $str ) ;
  $str = str_ireplace( array( 'ﺂ', 'ﺂ' , 'آ' ) , 'آ' , $str );
  $str = str_ireplace( array( 'ﺎ', 'ﺎ' , 'ا' ) , 'ا' , $str );
  $str = str_ireplace( array( 'ﺐ', 'ﺒ' , 'ﺑ' ) , 'ب' , $str );
  $str = str_ireplace( array( 'ﭗ', 'ﭙ' , 'ﭘ' ) , 'پ' , $str );
  $str = str_ireplace( array( 'ﺖ', 'ﺘ' , 'ﺗ' ) , 'ت' , $str );
  $str = str_ireplace( array( 'ﺚ', 'ﺜ' , 'ﺛ' ) , 'ث' , $str );
  $str = str_ireplace( array( 'ﺞ', 'ﺠ' , 'ﺟ' ) , 'ج' , $str );
  $str = str_ireplace( array( 'ﭻ', 'ﭽ' , 'ﭼ' ) , 'چ' , $str );
  $str = str_ireplace( array( 'ﺢ', 'ﺤ' , 'ﺣ' ) , 'ح' , $str );
  $str = str_ireplace( array( 'ﺦ', 'ﺨ' , 'ﺧ' ) , 'خ' , $str );
  $str = str_ireplace( array( 'ﺪ', 'ﺪ' , 'ﺩ' ) , 'د' , $str );
  $str = str_ireplace( array( 'ﺬ', 'ﺬ' , 'ﺫ' ) , 'ذ' , $str );
  $str = str_ireplace( array( 'ﺮ', 'ﺮ' , 'ﺭ' ) , 'ر' , $str );
  $str = str_ireplace( array( 'ﺰ', 'ﺰ' , 'ﺯ' ) , 'ز' , $str );
  $str = str_ireplace( array( 'ﮋ', 'ﮋ' , 'ﮊ' ) , 'ژ' , $str );
  $str = str_ireplace( array( 'ﺲ', 'ﺴ' , 'ﺳ' ) , 'س' , $str );
  $str = str_ireplace( array( 'ﺶ', 'ﺸ' , 'ﺷ' ) , 'ش' , $str );
  $str = str_ireplace( array( 'ﺺ', 'ﺼ' , 'ﺻ' ) , 'ص' , $str );
  $str = str_ireplace( array( 'ﺾ', 'ﻀ' , 'ﺿ' ) , 'ض' , $str );
  $str = str_ireplace( array( 'ﻂ', 'ﻄ' , 'ﻃ' ) , 'ط' , $str );
  $str = str_ireplace( array( 'ﻆ', 'ﻈ' , 'ﻇ' ) , 'ظ' , $str );
  $str = str_ireplace( array( 'ﻊ', 'ﻌ' , 'ﻋ' ) , 'ع' , $str );
  $str = str_ireplace( array( 'ﻎ', 'ﻐ' , 'ﻏ' ) , 'غ' , $str );
  $str = str_ireplace( array( 'ﻒ', 'ﻔ' , 'ﻓ' ) , 'ف' , $str );
  $str = str_ireplace( array( 'ﻖ', 'ﻘ' , 'ﻗ' ) , 'ق' , $str );
  $str = str_ireplace( array( 'ك', 'ﻚ' , 'ﻜ' , 'ﻛ' ), 'ک' , $str );
  $str = str_ireplace( array( 'ﮓ', 'ﮕ' , 'ﮔ' ) , 'گ' , $str );
  $str = str_ireplace( array( 'ﻞ', 'ﻠ' , 'ﻟ' ) , 'ل' , $str );
  $str = str_ireplace( array( 'ﻢ', 'ﻤ' , 'ﻣ' ) , 'م' , $str );
  $str = str_ireplace( array( 'ﻦ', 'ﻨ' , 'ﻧ' ) , 'ن' , $str );
  $str = str_ireplace( array( 'ﻮ', 'ﻮ' , 'ﻭ' ) , 'و' , $str );
  $str = str_ireplace( array( 'ﻫ', 'ﻬ' , 'ﻪ' ) , 'ه' , $str );
  $str = str_ireplace( array( 'ی', 'ﯿ' , 'ﯾ' , 'ﻲ' , 'ﯽ' ), 'ي' , $str );
  $str = str_ireplace( array( 'ﺄ', 'ﺄ' , 'ﺃ' ) , 'أ' , $str );
  $str = str_ireplace( array( 'ﺆ', 'ﺆ' , 'ﺅ' ) , 'ؤ' , $str );
  $str = str_ireplace( array( 'ﺈ', 'ﺈ' , 'ﺇ' ) , 'إ' , $str );
  $str = str_ireplace( array( 'ﺊ', 'ﺌ' , 'ﺋ' ) , 'ئ' , $str );
  $str = str_ireplace( 'ﺔ' ,   'ة', $str );
  return $str ;
  
}

?>