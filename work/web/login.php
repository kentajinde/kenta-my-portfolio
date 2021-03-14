<?php

require("../app/functions.php");
require("../app/dbconnect.php");

session_start();

if(!empty($_POST)){
  if(blk($_POST["mail"]) === ""){
    $error["mail"] = "blank";
  }
  if(blk($_POST["pass"]) === ""){
    $error["pass"] = "blank";
  }

  if(blk($_POST["mail"]) !== "" && blk($_POST["pass"] !== "")){
    $stmt = $pdo->prepare("SELECT * FROM members WHERE mail=? AND pass=?");
    $stmt->execute([
      $_POST["mail"],
      sha1($_POST["pass"]),
    ]);
    $result = $stmt->fetch();
    if($result){
      $_SESSION["id"] = $result["id"];
      // $_SESSION["time"] = time();

      header("Location: main.php");
      exit();
    }else{
      $error["login"] = "failed";
    }
  }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="お気に入りの本をシェアするWebサイトです。">
  <title>未定</title>

  <!-- 自作css -->
  <link rel ="stylesheet" href="css/styles_login.css">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

  <!-- React -->
  <script src="https://unpkg.com/react@17/umd/react.development.js"></script>
  <script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js"></script>
  <!-- for demo -->
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

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
          <h1>ログイン</h1>
            <form action="" method="post">
              <label>
                メールアドレス
                <input type="text" value="<?= blk(h($_POST["mail"])); ?>" placeholder="your@example.com" name="mail">
                <?php if($error["mail"] === "blank"): ?>
                  <p class="error">*メールアドレスを入力してください</p>
                <?php endif; ?>
              </label>
              <label>
                パスワード
                <input type="password" value="<?= blk(h($_POST["pass"])); ?>" placeholder="password" name="pass">
                <?php if($error["pass"] === "blank"): ?>
                  <p class="error">*パスワードを入力してください</p>
                <?php endif; ?>
                <?php if($error["login"] === "failed"): ?>
                  <p class="error">*メールアドレスまたはパスワードをご確認ください</p>
                <?php endif; ?>
              </label>
              <input type="submit" value="ログインする"/>
            </form>
            <a href="signup.php">
              新規ご登録の方はこちら
              <span class="material-icons">
                keyboard_arrow_right
              </span> 
            </a>
          </div>
        </div>
      </div>
    </div>
  </main>
  <footer>
    <p>Copyright Kenta All right reserved.</p>
  </footer>
</body>
</html>