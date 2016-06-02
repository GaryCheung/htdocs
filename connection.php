<?php
   
    $link=mysql_connect('localhost','root','19860112');
    if(!$link) echo "失败!<br>".mysql_error();
    else echo "成功<br>";
    mysql_close();
   
?>