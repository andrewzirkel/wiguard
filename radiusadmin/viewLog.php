<?php include "auth/checkLevel1.php"?>

<html>
<head>
<link rel="stylesheet" href=style.css>
<META HTTP-EQUIV="Refresh" CONTENT="30, URL=viewLog.php">
</head>
<body>
<? 
   // Include & Call Class
   include_once("classes/class.displayLogfile.php");
   $lfDispl = new displayLogfile;

   // Path/Name of Logfile
   // Choose a short one for example b (!) 
   $filename = "/var/log/freeradius/radius.log";




   // Example a: ///////////////////////////////
?>
<pre style="font-size:10px;">
<?
   $lfDispl->setRowsToRead(100);    // Read 100 rows
   $lfDispl->setAlign("top");       // Last row on top
   $lfDispl->setFilepath($filename); // from this logfile
   $lfDispl->setLineBreak(150);  // Break the row after 150 chars
   $lfDispl->returnFormated();   // Output 
?>
</pre>

<?
/*
/////////////////////////////////////////////////



   // Example b: (We read the Whole File into an array) ///
   $lfDispl->setAlign("bottom");      // Last row on bottom
   $lfDispl->setFilepath($filename); // from this logfile
   $countRows = $lfDispl->rowSize();    // Count the rows (allways the complete file)
   $rowArray  = $lfDispl->readRows();   // All Rows in array 
        
   
   /*
   // Debug output:
   echo "File got ".$countRows." Rows <br />";
   echo "<pre>";
   print_r($rowArray);
   echo "</pre>";
   */


   /////////////////////////////////////////////////

?>
</body>
</html>
