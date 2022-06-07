<?php
if (date('Y') > 2022){
    $date = "2022 - {date('Y')}";
}
else{
    $date = date('Y');
}
echo "<div class='footer_container'>";
    #echo"<a href='#top'><img src='../img/fleche-haut.png' alt='up' style='width:50px;height:50px'></a>";
    echo "<div class='footer_info_left'>";
        echo "<p> © Movie Planet ~ BESOMBES | RICHIER | IYAMU ~ {$date}</p>";
    echo "</div>";
    echo "<div class='footer_info_right'>";
        echo "<ul>";
            echo "<li>";
                echo "<a href='/index.php'>Accueil</a> |";
            echo "</li>";
            echo "<li>";
                echo "<a href='../php/contact.php'>Contact</a>";
            echo "</li>";
        echo "</ul>";
    echo "</div>";
echo "</div>";
?>
