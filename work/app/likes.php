<?php
require("dbconnect.php");

// likes
// +-----------+---------+------+-----+---------+----------------+
// | Field     | Type    | Null | Key | Default | Extra          |
// +-----------+---------+------+-----+---------+----------------+
// | id        | int(11) | NO   | PRI | NULL    | auto_increment |
// | member_id | int(11) | YES  |     | NULL    |                |
// | post_id   | int(11) | YES  |     | NULL    |                |
// +-----------+---------+------+-----+---------+----------------+

if(!empty($_POST["member_id"]) && !empty($_POST["post_id"])){
  $stmt = $pdo->prepare("SELECT * FROM likes WHERE member_id = ? AND post_id = ?");
  $stmt->execute([$_POST["member_id"], $_POST["post_id"]]);
  $result = $stmt->fetch();
  if(empty($result)){
    $stmt = $pdo->prepare("UPDATE posts SET likes = likes + 1 WHERE id = ?");
    $stmt->execute([$_POST["post_id"]]);
    $stmt = $pdo->prepare("INSERT INTO likes SET member_id = ?, post_id = ?");
    $stmt->execute([$_POST["member_id"], $_POST["post_id"]]);
  }else{
    $stmt = $pdo->prepare("UPDATE posts SET likes = likes - 1 WHERE id = ?");
    $stmt->execute([$_POST["post_id"]]);
    $stmt = $pdo->prepare("DELETE FROM likes WHERE member_id = ? AND post_id = ?");
    $stmt->execute([$_POST["member_id"], $_POST["post_id"]]);
  }
}
