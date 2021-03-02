<?php

require("../app/dbconnect.php");
session_start();
require("../app/upload.php");

// 投稿削除
if(isset($_REQUEST["id"])){

  // post_id
  $id = $_REQUEST["id"];

  $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
  $stmt->execute([$id]);
  $post = $stmt->fetch();

  if($_SESSION["id"] == $post["member_id"]){

    // 投稿情報削除
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$id]);

    // 投稿画像削除
    $stmt = $pdo->prepare("SELECT * FROM content WHERE post_id = ?");
    $stmt->execute([$id]);
    $images = $stmt->fetchAll();
    foreach($images as $image){
      if($image["picture"]){
        if($s3Api){
          del_pos($image["picture"]);
        }else{
          unlink("post_img/" . $image["picture"]);
        }
      }
    }
    
    // 投稿内容削除
    $stmt = $pdo->prepare("DELETE FROM content WHERE post_id = ?");
    $stmt->execute([$id]);

    // 関連likes 削除
    $stmt = $pdo->prepare("DELETE FROM likes WHERE post_id = ?");
    $stmt->execute([$id]);
  }

  header("Location: posts.php");
  exit();
}

// ライブラリ削除
if(isset($_REQUEST["lib_id"])){
  $id = $_REQUEST["lib_id"];
  $stmt = $pdo->prepare("SELECT * FROM library WHERE id=?");
  $stmt->execute([$id]);
  $lib = $stmt->fetch();
  if($lib["member_id"] === $_SESSION["id"]){
    if($lib["picture"]){
      if($s3Api){
        del_lib($lib["picture"]);
      }else{
        unlink("library_img/" . $lib["picture"]);
      }
    }
    $stmt = $pdo->prepare("DELETE FROM library WHERE id=?");
    $stmt->execute([$id]);
  }

  header("Location: posts.php");
  exit();
}
