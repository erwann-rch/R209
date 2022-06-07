<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/favicon.ico">
    <title>Movie Planet</title>
    <script language="JavaScript" type="text/javascript">

    //Check all the checkbox when it's activated
    function checkall(){
        temp = document.categories.elements.length - 4;
        //alert(temp);
        if (document.categories.elements[0].checked){ // Check all the other checkboxes if this one is checked
            for (i=1; i < temp; i++){
                document.categories.elements[i].checked=1;
            }
        }
        else {
            for (i=1; i < temp; i++) { // Uncheck all the other checkboxes if this one is unchecked
                document.categories.elements[i].checked=0;
            }
        }
    }

    //Uncheck the main checkbox when it's activated
    function checkone(){
        m=0; // startcounting
        temp = document.categories.elements.length;
        for (i=1; i < temp; i++){
            if (document.categories.elements[i].checked){ // +1 to the count if current checkbox if checked
                m++;
            }
        }
        if (document.categories.elements[0].checked){ // verify if the main checkbox is checked and uncheck it
            document.categories.elements[0].checked=0;
        }
        else { // verify if the count of checked checkboxes is good
            if (m == (temp-1)) {
                document.categories.elements[0].checked=1;
            }
        }
    }
    </script>
</head>
<body>
    <header>
    <?php
        session_start();
        include("../templates/header.php");?>
    </header>
    <div class="main_page">
<?php 
$conn = new SQLite3('../db/db.db');
$category = $conn->query("SELECT * FROM categorie;");

function check($strArr){ // Permit to create a 'memory' and keep the selected checkboxes in case of the client wants to precise its filter
    $str = $strArr[0];
    $arr = $strArr[1]; 
    if(ISSET($arr)){
        if(in_array($str, $arr)) {
            return("checked"); // Get the value or not if it is checked
        } 
    } 
}
$strCheck ="check"; 

echo "<div class='rch_adv_choix'>";
    echo"<div class='rch_adv_gauche'>";
        echo"<h3>Catégories :</h3><br>";
            echo"<form id = 'cat' name ='categories' action='' method='POST'>";
            echo"<ul>";
                echo"<input type='checkbox' id='cat' name='cat[]' value='0' onclick='checkall()'><label for='0'>Tout</label><br>";
                while($ListCat = $category->fetchArray()){
                    if(ISSET($_POST['cat'])){
                        $paramTmp = array($ListCat[0],$_POST['cat']); // Create an array with an array and a string to be passed in the check() function
                    }
                    else{
                        $paramTmp = array($ListCat[0],array()); // Same but with an empty array
                    }
                    echo "<input type='checkbox' id='cat' name='cat[]' value='{$ListCat[0]}'{$strCheck($paramTmp)}onclick='checkone()'><label for='{$ListCat[0]}'>{$ListCat[1]}</label><br>"; // Get the checked prop or not   
                }
            echo"</ul>";
    echo"</div>";
    if(ISSET($_POST['order'])){ //Same but with a different array
        $paramTmp1 = array('nom',$_POST['order']);
        $paramTmp2 = array('prix',$_POST['order']);
        $paramTmp3 = array('note',$_POST['order']);
    }
    else {
        $paramTmp1 = array('nom',array());
        $paramTmp2 = array('prix',array());
        $paramTmp3 = array('note',array());
    }
    echo"<div class='rch_adv_droite'>";  
        echo"<h3>Ordre par :</h3><br>";
            echo"<ul>";
                echo"<input type='checkbox' id='a-z' name='order[]' onclick='checkone()' value='nom' {$strCheck($paramTmp1)}>";
                echo"<label for='a-z'>A-Z</label><br>";
                echo"<input type='checkbox' id='prix' name='order[] onclick='checkone()' value='prix' {$strCheck($paramTmp2)}>";
                echo"<label for='ajout'>Prix</label><br>";
                echo"<input type='checkbox' id='note' name='order[] onclick='checkone()' value='note' {$strCheck($paramTmp3)}>";
                echo"<label for='note'>Note</label><br>";
            echo"</ul>";
            echo"<div class='none'><input type='submit' name='valid'></div>";
    echo"</form>";
    echo"</div>";
echo"</div>";
?>

<?php
// Construct a query dynamically 
if(ISSET($_POST['cat'])){
    if(in_array('0',$_POST['cat'])){ // Check if the element 0 is in the array of catégories checkbox ==> check if all is selected
        $query = "SELECT nom,fid FROM produits "; // and select all
    }
    else {
        $Cid="";
        foreach($_POST['cat'] as $cat){
            $Cid .= ",$cat"; // Construct a list with all the selected categories
        }
        $cid = substr($Cid, 1);
        $query="SELECT DISTINCT nom,fid FROM produits WHERE (categorie1 IN (SELECT cid FROM categorie WHERE cid IN ($cid))) OR (categorie2 IN (SELECT cid FROM categorie WHERE cid IN ($cid))) "; // and put it in a query
    }
    // scenario 0 : cat & order are selected
    if(ISSET($_POST['order'])) {
        $Order = " ORDER BY "; // Construct a dynamic end of the query to get an order
        foreach($_POST['order'] as $ord){
            if($ord == 'note'){
                $query .= " AND (fid IN (SELECT fid FROM info_film ORDER BY note ASC))"; // Add something different because we did mistake in the creation of the db
            }else {
                $Order .= "$ord ASC,"; // Order in ascendant way
            }
        }
        $order = rtrim($Order,","); // Cut off the last coma of the query to be executable
        if($order != $Order) {
            $query .= "$order"; // and join it to the inital query
        }
        //echo $query;
    }
    // scenario 1 : cat selected but not order
    else {
        $query="SELECT DISTINCT nom,fid FROM produits WHERE (categorie1 IN (SELECT cid FROM categorie WHERE cid IN ($cid))) OR (categorie2 IN (SELECT cid FROM categorie WHERE cid IN ($cid))) "; // execute a query without order ==> all the result in chronological way
        //echo $query;
    }
}
//scenario 2 : no cat but order
else{
    $query = "SELECT nom,fid FROM produits "; // Select all
    if(ISSET($_POST['order'])) {
        $Order = " ORDER BY "; // Same as before
        foreach($_POST['order'] as $ord){
            if($ord == 'note'){
                $query .= " AND (fid IN (SELECT fid FROM info_film ORDER BY note ASC))";
            }else {
                $Order .= "$ord ASC,";
            }
        }
        $order = rtrim($Order,",");
        if($order != $Order) {
            $query .= "$order";
        }
        //echo $query;
    }
    //scenario 3 : no cat and order
    else {
        $query = "SELECT nom,fid FROM produits ORDER BY nom ASC;"; // Basic default query
        //echo $query;
    }
}

$srch= $conn->query($query);
while($Listsrch = $srch->fetchArray()){
    echo "<div class='film'>";
        echo "<div class='image'>";
            echo "<a href='fiche.php?movie={$Listsrch['fid']}' class='link'>";
            echo "<center><img src='../img/film_img/{$Listsrch['fid']}.jpg' alt='{$Listsrch['nom']}' width='200'><br></a>";
        echo"</div>";
        echo"<div class='title'>";
            echo "<p>";
                echo "<a href='fiche.php?movie={$Listsrch['fid']}'> {$Listsrch['nom']}</a>";
            echo"</p>";
        echo"</div>";
    echo"</div>";
}
?>
    </div>
    <footer class="main_footer">
        <?php include("../templates/footer.php");?>
    </footer>
</body>
</html>

