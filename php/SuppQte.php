<?php
$db = new SQLite3('../db/db.db');

session_start();
$uname = $_SESSION['uname'];
$uid = $db->query("SELECT uid FROM compte WHERE username = '$uname'");
$Uid = $uid->fetchArray()['uid'];

$fid = $_GET['fid']; // Get the fid from the cart page

$qte = $db->query("SELECT qte FROM panier WHERE uid IN (SELECT uid FROM compte WHERE username = '$uname') AND fid = '$fid';");
$NewQte = $qte->fetchArray()['qte']-1;

# Updating the qte by deleting the whole produit of the cart and insert it with a new qte
$db->exec("DELETE FROM panier WHERE uid ='$Uid' AND fid = '$fid';");
if ($NewQte == 0){
	$db->exec("DELETE FROM panier WHERE uid ='$Uid' AND fid = '$fid';");
}else{	
	$db->exec("INSERT INTO panier (uid,fid,qte) VALUES ($Uid,$fid,$NewQte)");
}
header('Refresh:0;url=panier.php');                    
?>