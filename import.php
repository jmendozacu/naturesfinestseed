<?php 

//connect to the database
$connect = mysql_connect("localhost","naturesf_m4gU5D3","Ht23FEhaaVrlhtFvE8");
mysql_select_db("naturesf_m4gDbD3v",$connect); //select the table
//

if ($_FILES[csv][size] > 0) {

    //get the csv file
    $file = $_FILES[csv][tmp_name];
    $handle = fopen($file,"r");
 
    //loop through the csv file and insert into database
    while ($data = fgetcsv($handle,1000,",","'")) { 
        if ($data[3]) { 
    $res= mysql_query("select count(*) as count FROM mg_customer_entity where email=".$data[3]);
$arr=mysql_fetch_array($res);
 
echo "<br>".$arr['count']."===>select count(*) as count FROM mg_customer_entity where email=".$data[3];
          if($arr['count']==0)
            mysql_query("INSERT INTO mg_customer_entity (entity_type_id, attribute_set_id, website_id,email,group_id,store_id) VALUES
                (1,0,1,".$data[3].",1,1)"); 
        }
    }  
    //

    //redirect
    header('Location: import.php?success=1'); die;

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Import a CSV File with PHP & MySQL</title>
</head>

<body>

<?php if (!empty($_GET[success])) { echo "<b>Your file has been imported.</b><br><br>"; } //generic success notice ?>

<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
  Choose your file: <br />
  <input name="csv" type="file" id="csv" />
  <input type="submit" name="Submit" value="Submit" />
</form>

</body>
</html> 
