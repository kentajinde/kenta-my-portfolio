<?php

$categorys = ["busi", "enlight", "lite", "plac", "comic"];


if($ranking){
  foreach($categorys as $category){
    $stmt = $pdo->prepare(
      "SELECT *
      FROM posts
      WHERE category = ?
      ORDER BY likes DESC, member_id
    ");
    $stmt->execute([$category]);
    $posts = $stmt->fetchAll();
    foreach($posts as $post){
      $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
      $stmt->execute([$post["member_id"]]);
      $result["member"] = $stmt->fetch();
      $stmt = $pdo->prepare("SELECT * FROM content WHERE post_id = ?");
      $stmt->execute([$post["id"]]);
      $result["content"] = $stmt->fetchAll();
      $result["post"] = $post;
      $$category[] = $result;
    }
  }
}else{
  foreach($categorys as $category){
    $stmt = $pdo->prepare(
      "SELECT *
      FROM posts
      WHERE category = ?
      ORDER BY created DESC, member_id
    ");
    $stmt->execute([$category]);
    $posts = $stmt->fetchAll();
    foreach($posts as $post){
      $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
      $stmt->execute([$post["member_id"]]);
      $result["member"] = $stmt->fetch();
      $stmt = $pdo->prepare("SELECT * FROM content WHERE post_id = ?");
      $stmt->execute([$post["id"]]);
      $result["content"] = $stmt->fetchAll();
      $result["post"] = $post;
      $$category[] = $result;
    }
  }
}

?>