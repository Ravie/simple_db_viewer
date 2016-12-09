<?php
session_start();
if(isset($_POST['username']) && isset($_POST['password']))
{
	$_SESSION['username'] = $_POST['username'];
	$_SESSION['password'] = $_POST['password']; // password_hash для php 5.5
}
if(isset($_POST['database']))
	$_SESSION['database'] = $_POST['database'];
if(isset($_POST['slider']))
	$_SESSION['per_page'] = $_POST['slider'];

if (isset($_GET['page']))
	$page=($_GET['page']-1);
else 
	$page=0;

$pdo_connect = new PDO('mysql:host=localhost;dbname='.$_SESSION['database'], $_SESSION['username'], $_SESSION['password']); 

$tables_list = $pdo_connect->query("SHOW TABLES FROM ".$_SESSION['database']);
echo "Таблица: <form action='listing.php' method='post'><select multiple name='table'>";
$flag=true;
while ($row = $tables_list->fetch())
{
	echo "<option>".$row[0]."</option>";
	if($flag)
	{
		$_SESSION['table'] = $row[0];
		$flag = false;
	}
}
echo "</select></br><input type='submit'></form>";
if(isset($_POST['table']))
	$_SESSION['table'] = $_POST['table'];

$columns_list = $pdo_connect->query("SHOW COLUMNS FROM ".$_SESSION['table']);
$query = $pdo_connect->query("SELECT * FROM ".$_SESSION['table']." LIMIT ".$page * $_SESSION['per_page'].", ".$_SESSION['per_page']);
$query->setFetchMode(PDO::FETCH_BOTH);
echo "<center><h1>Таблица ".$_SESSION['table']."</h1><table><tr>"; 
while ($row = $columns_list->fetch())
	echo "<td>[".$row[0]."]</td>";
echo "</tr>";
while ($row = $query->fetch())
{
	$i=0;
	echo "<tr>";
	while(isset($row[$i]))
	{
		echo "<td>[".$row[$i]."]</td>";
		$i++;
	}
	echo "</tr>";
}
echo "</table>"; 

$res = $pdo_connect->query('SELECT COUNT(*) FROM '.$_SESSION['table']);
$total_rows = $res->fetch();
$num_pages=ceil($total_rows[0]/$_SESSION['per_page']);

for($i=1; $i<=$num_pages; $i++)
{
	if ($i-1 == $page) 
	{
		echo $i." ";
	}
	else 
	{
		echo '<a href="'.$_SERVER['PHP_SELF'].'?page='.$i.'">'.$i."</a> ";
	}
}
echo "</center>";
$pdo_connect = null;
?>