<?php

try{
  $pdo = new PDO(
    "mysql:host=db;dbname=base;charset=utf8mb4",
    "base",
    "base123",
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );

  //ユーザーアカウント
  // $pdo->query("DROP TABLE IF EXISTS members");
  // $pdo->query(
  //   "CREATE TABLE members(
  //     id INT NOT NULL AUTO_INCREMENT,
  //     name VARCHAR(255),
  //     mail VARCHAR(255),
  //     pass VARCHAR(100),
  //     picture VARCHAR(255),
  //     created DATETIME,
  //     modified TIMESTAMP,
  //     PRIMARY KEY(id)
  //   )"
  // );

  //投稿内容
  // $pdo->query("DROP TABLE IF EXISTS posts");
  // $pdo->query(
  //   "CREATE TABLE posts(
  //     id INT NOT NULL AUTO_INCREMENT,
  //     title VARCHAR(255),
  //     author VARCHAR(30),
  //     publish VARCHAR(255),
  //     `desc` TEXT,
  //     `rank` INT,
  //     member_id INT,
  //     created DATETIME,
  //     modified TIMESTAMP,
  //     PRIMARY KEY(id)
  //   )"
  // );
}catch(PDOException $e){
  echo  $e->getMessage();
  exit();
}