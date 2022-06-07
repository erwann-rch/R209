<?php
$db = new SQLite3('../db/db.db');

session_start();
$uname = $_SESSION['uname'];
$uid = $db->query("SELECT uid FROM compte WHERE username = '$uname'");
$Uid = $uid->fetchArray()['uid']; # Get the uid of the session

$fid = $_GET['fid']; 

$qte = $db->query("SELECT qte FROM panier WHERE uid IN (SELECT uid FROM compte WHERE username = '$uname') AND fid = '$fid';"); # Get the qte of the select product
$NewQte = $qte->fetchArray()['qte']+1; # Add one to get the new qte

$db->exec("DELETE FROM panier WHERE uid ='$Uid' AND fid = '$fid';"); #Delete the produit and re-add it with new qte
$db->exec("INSERT INTO panier (uid,fid,qte) VALUES ($Uid,$fid,$NewQte)");

header('Refresh:0;url=panier.php');                    
?>