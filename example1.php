<? 

  /*! \file example1.php
      \brief Order table data by clicking on column caption
      \author Voznyak Nazar (freelancer), 13 Jan 2005
      \email narko@mail.lviv.ua
   */

  require_once('aasort.class.php');

  // initialize array with data 
  $l_aResults = array();
  $l_aResults[] = array('name' => "John Smith", 'age' => 38, 'birth' => 1978);
  $l_aResults[] = array('name' => "Ulex Sweat", 'age' => 40, 'birth' => 1965);
  $l_aResults[] = array('name' => "Nuno Mariz", 'age' => 35, 'birth' => 1970);

?>

<TABLE WIDTH="100%" BORDER="1">
<TH><a href='<? echo $_SERVER['SCRIPT_SELF']; ?>?sort=name'>Name</a></TH>
<TH><a href='<? echo $_SERVER['SCRIPT_SELF']; ?>?sort=age'>Age</a></TH>
<TH><a href='<? echo $_SERVER['SCRIPT_SELF']; ?>?sort=birth'>Birth</a></TH>

<?
  // create instance of class which provide ordering
  $aasort = &new AASort($l_aResults);
  // order array
  $l_aResults = $aasort->sort(($_GET['sort']) ? ($_GET['sort']) : 'name', SORT_ASC);

  // print out ordered array
  foreach ($l_aResults as $Result) {
?>
<tr>
<td><? echo $Result['name']; ?></td>
<td><? echo $Result['age']; ?></td>
<td><? echo $Result['birth']; ?></td>
</tr>
<?
  }
?>

</TABLE>