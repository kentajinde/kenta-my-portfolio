<?php

function h($s){
  return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

//空白削除
function blk($str){
  return trim(str_replace("　", " ", $str));
}
