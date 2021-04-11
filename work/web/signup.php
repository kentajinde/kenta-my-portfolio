<?php

require("../app/functions.php");
require("../app/dbconnect.php");

session_start();

if(!empty($_POST)){
  if(blk($_POST["name"]) === ""){
    $error["name"] = "blank";
  }
  if(blk($_POST["mail"]) === ""){
    $error["mail"] = "blank";
  }
  // if(){
  //   $error["mail"] = "blank";
  // }
  if(blk($_POST["pass"]) === ""){
    $error["pass"] = "blank";
  }
  if(blk(strlen($_POST["pass"])) >= 1 && blk(strlen($_POST["pass"])) < 4){
    $error["pass"] = "length";
  }

  // if(empty($error)){
  //   $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM members WHERE mail=?");
  //   $stmt->execute([$_POST["mail"]]);
  //   $result = $stmt->fetch();
  //   if($result["cnt"] > 0){
  //     $error["mail"] = "duplicate";
  //   }
  // }
  if(empty($error)){
    $stmt = $pdo->prepare("SELECT * FROM members WHERE mail=?");
    $stmt->execute([$_POST["mail"]]);
    $result = $stmt->fetch();
    if($result){
      $error["mail"] = "duplicate";
    }
  }

  if(empty($error)){
    $_SESSION["join"] = $_POST;
    header("Location: check.php");
    exit();
  }
}

if($_REQUEST["action"] === "rewrite" && isset($_SESSION["join"])){
  $_POST = $_SESSION["join"];
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="お気に入りの本をシェアするWebサイトです。">
  <title>Best Book</title>

  <!-- 自作css -->
  <link rel ="stylesheet" href="css/styles_signup.css">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

  <!-- Google icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
  <header>
    <nav class="navbar navbar-expand">
      <a href="home.php">
        <img class="navbar-brand" src="img/logo.png" alt="logo">
      </a>
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="signup.php">サインアップ</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">ログイン</a></li>
      </ul>
    </nav>
  </header>
  <main>
    <div class="top-img">
      <div class="top-mask">
        <div>
          <h1>Best Book</h1>
        </div>
        <div id="root">
          <div class="signupForm">
            <h1>新規ユーザー登録</h1>
            <form action="" method="post">
              <label>
                ユーザー名
                <input type="text" value="<?= blk(h($_POST["name"])); ?>" placeholder="username" name="name"/>
                <?php if($error["name"] === "blank"): ?>
                  <p class="error">*ユーザー名を入力してください</p>
                <?php endif; ?>
              </label>
              <label>
                メールアドレス
                <input type="text" value="<?= blk(h($_POST["mail"])); ?>" placeholder="your@example.com" name="mail"/>
                <?php if($error["mail"] === "blank"): ?>
                  <p class="error">*メールアドレスを入力してください</p>
                <?php endif; ?>
                <?php if($error["mail"] === "duplicate"): ?>
                  <p class="error">*既に登録されているメールアドレスです</p>
                <?php endif; ?>
              </label>
              <label>
                パスワード
                <input type="password" value="<?= blk(h($_POST["pass"])); ?>" placeholder="password" name="pass"/>
                <?php if($error["pass"] === "blank"): ?>
                  <p class="error">*パスワードを入力してください</p>
                <?php elseif($error["pass"] === "length"): ?>
                  <p class="error">*4文字以上のパスワードを入力してください</p>
                <?php endif; ?>
              </label>
              <input type="submit" value="ユーザー登録する"/>
            </form>
            <a href="login.php">
              すでにご登録済みの方はこちら
              <span class="material-icons">
                keyboard_arrow_right
              </span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </main>
<?php require("footer.php"); ?>