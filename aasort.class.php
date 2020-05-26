<?

  /*! \file aasort.class.php
      \brief Order associative array by field name
      \author Voznyak Nazar (freelancer), 13 Jan 2005
      \email narko@mail.lviv.ua
      \description
       This package is mean to order associative array by field name.
       It is taken from the php official manual and slightly modified for own 
       purposes. The package comes with base class that perform ordering. 
       There is also example which demonstrate how to use this class.
   */

class AASort {

  // ordering data array
  var $l_aArray;

  function AASort($a_aArray) {
    $this->l_aArray = $a_aArray;
  }

/**
* 
* function to sort an "arrow of rows" by its columns
* exracts the columns to be sorted and then
* uses eval to flexibly apply the standard
* array_multisort function
*
* uses a temporary copy of the array whith "_" prefixed to  the keys
* this makes sure that array_multisort is working with an associative
* array with string type keys, which in turn ensures that the keys 
* will be preserved.
*
* TODO: find a way of modifying the keys of $array directly, without using
* a copy of the array. 
* 
* flexible syntax:
* $new_array = sort($array [, 'col1' [, SORT_FLAG [, SORT_FLAG]]]...);
* 
* original code credited to Ichier (www.ichier.de) here:
* http://uk.php.net/manual/en/function.array-multisort.php
*
* prefixing array indeces with "_" idea credit to steve at mg-rover dot org, also here:
* http://uk.php.net/manual/en/function.array-multisort.php
* 
*/

 function sort() { 
   $args = func_get_args();
   $array = $this->l_aArray;

   // make a temporary copy of array for which will fix the 
   // keys to be strings, so that array_multisort() doesn't
   // destroy them
   $array_mod = array();
   foreach ($array as $key => $value)
     $array_mod['_' . $key] = $value;
 
   $i = 0;
   $multi_sort_line = "return array_multisort( ";
   foreach ($args as $arg) {
     $i++;
     if ( is_string($arg) ) {
       foreach ($array_mod as $row_key => $row) {
         $sort_array[$i][] = $row[$arg];
       }
     } 
     else {
         $sort_array[$i] = $arg;
     }
     $multi_sort_line .= "\$sort_array[" . $i . "], ";
   }
   $multi_sort_line .= "\$array_mod );";

   eval($multi_sort_line);
   // now copy $array_mod back into $array, stripping off the "_"
   // that we added earlier.
   $array = array();
   foreach ($array_mod as $key => $value)
     $array[ substr($key, 1) ] = $value;
   return $array;
 }

}

?>