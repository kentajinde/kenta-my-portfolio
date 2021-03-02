<?php

try{
  
  // 開発環境
  // $pdo = new PDO(
  //   "mysql:host=db;dbname=base;charset=utf8mb4",
  //   "base",
  //   "base123",
  //   [
  //     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  //     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  //   ]
  // );

  // 本番環境
  $pdo = new PDO(
    "mysql:host=us-cdbr-east-03.cleardb.com;dbname=heroku_a04b3ce4b7ee565;charset=utf8mb4",
    "b3e9847fb125f0",
    "915d6388",
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );

}catch(PDOException $e){
  echo  $e->getMessage();
  exit();
}