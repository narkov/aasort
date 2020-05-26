<? 

  /*! \file example2.php
      \brief Retrieve # of each keyword from search results on google page
      \author Voznyak Nazar, 7 Jul 2005
      \email narko@mail.lviv.ua
   */

session_start(); 

$NumberOfRecordsOnPage = 100;

if ($_POST["operation"] == "" && $_GET["operation"] == "" && $_GET["record"] == "") {
  unset($_SESSION["Results"]);
?>
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
<html>
    <FRAMESET ROWS="36%,*">
    <FRAME NAME="Search" ID="Search" SRC="example2.php?operation=showsearch">
    <FRAME NAME="Results" ID="Results" SRC="example2.php?operation=showindex">
  </FRAMESET>
</html>
<?
exit;
}
?><html>
<head>
  <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
  <title>Keywords</title>
</head>
<body>
<?
  
if ($_GET["operation"] == "showsearch") {

?>
<form action="" method="POST">
<input type=hidden name=operation value="search">
<table width='70%' border=0>
<tr>
<td valign='top'><b>Keywords</b></td>

<td><textarea name='Keyword' cols=65 rows=10><? echo $_POST["Keyword"] ?></textarea></td>
<td valign='top'><input type=submit value="Search">
</tr></table>

</form>
<?
}

if ($_POST["operation"] == "search") {
  $ResultKeywords = array();
  $Errors = array();
  
  // initializing and preparations
  $Keyword = trim($_POST["Keyword"]);

  if (strstr($Keyword, "\r\n")) 
    $l_sKeywords = explode("\r\n", $Keyword);
  else
  if (strstr($Keyword, ";")) 
    $l_sKeywords = explode(";", $Keyword);
  else
  if (strstr($Keyword, "\t")) 
    $l_sKeywords = explode("\t", $Keyword);
  else
  if (strstr($Keyword, ",")) 
    $l_sKeywords = explode(",", $Keyword);

  $l_aResult[] = array('name' => 'TOTAL', 'n1' => 0, 'total' => 0);
  $TotalN1 = 0;
  $Total = 0;

  foreach ($l_sKeywords as $Keyword) {
   if ($Keyword != "") {

    // begin of retrieving all the similar keywords
    $Keyword = ucwords($Keyword);

    // try to read HTML document succefully for 10 times
    for ($i=0; $i<20; $i++) {
      $PageContent = file_get_contents("http://www.google.com.ua/search?hl=uk&q=" . (str_replace(" ", "+", $Keyword)));
      if ($PageContent) break;
    }

    if ($PageContent === false) 
      $Errors[] = $Keyword;

    // add results for current keyword to common list
    $l_iTotal = substr_count($PageContent, $Keyword);
    $l_aResult[] = array('name' => $Keyword, 'total' => $l_iTotal);
    $Total += $l_iTotal;
   }
  }

  $l_aResult[0]['total'] = $Total;

  $_SESSION["Results"] = $l_aResult;

  echo("<script>window.parent.Results.location.href=\"example2.php?operation=showindex\";</script>");
  exit;
}

if ($_GET["operation"] == "showindex") {
  echo("<p align=center>");

  // no matches
  if (sizeof($_SESSION["Results"]) == 0) {
    echo ("There is no records found");
  }

  // all retrieved matches fit page
  elseif (sizeof($_SESSION["Results"]) <= $NumberOfRecordsOnPage)
    $ShowRecords = true;

  else {
    // prepare pagination
    $Index = 0;
    if (sizeof($_SESSION["Results"]) > 0) {
      while ($Index < sizeof($_SESSION["Results"])) {
        $Index++;
        echo("<a href=\"?record=" . ($Index - 1) ."\">" . $Index . "-");
        $Index += $NumberOfRecordsOnPage - 1;
        if ($Index > sizeof($_SESSION["Results"]))
          $Index = sizeof($_SESSION["Results"]);
        echo($Index . "</a><br>");
      }
    }
  }
  
  if (sizeof($_SESSION["Errors"]) > 0) {
    echo("<br><br><b>Errors:</b><br>");
    foreach ($Errors as $value)
      echo($value . "<br>");
  }
  echo("</p>");
}

// shows up resulting table
if ($_GET["record"] != "" || ($ShowRecords)) {
?>

<TABLE WIDTH="100%" BORDER="1">
<TH><a href='javascript: window.parent.Results.location.href="example2.php?operation=showindex&sort=name"'>Name</a></TH>
<TH><a href='javascript: window.parent.Results.location.href="example2.php?operation=showindex&sort=total"'>Words #</a></TH>

<?
  if ($_GET["record"] == "") $_GET["record"] = 0;

  require_once('aasort.class.php');

  $l_aResults = $_SESSION["Results"];
  $l_aTotal = array_shift($l_aResults);

  $aasort = &new AASort($l_aResults);
  $l_aResults = $aasort->sort(($_GET['sort']) ? ($_GET['sort']) : 'name', SORT_ASC);

  array_unshift($l_aResults, $l_aTotal);

  foreach ($l_aResults as $l_aRes) {
    print "<tr>".
          "<td>".$l_aRes['name']."</td>".
          "<td>".number_format($l_aRes['total'], 0, '.', ',')."</td>".
          "</tr>";
  }
}

?>

</TABLE>

</body>
</html>