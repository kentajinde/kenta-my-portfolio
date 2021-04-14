<?php

// サニタイズ
function h($s){
  return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

//空白削除
function blk($str){
  return trim(str_replace("　", " ", $str));
}

// 締め出し
function shutOut(){
  if(!isset($_SESSION["id"])){
    header("Location: login.php");
    exit();
  }
}