<?php


  $pwd = "";
  if (isset($_POST['pwd'])) $pwd = $_POST['pwd'];

  $password = "ks6006le";
  if ($_POST['pwd'] == $password) $flag = true;
  else $flag = false;
  if ($flag == false) {
    printf("fail");
  } else {
    printf("true");
  }

?>
