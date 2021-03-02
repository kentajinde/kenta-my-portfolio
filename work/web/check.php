<?php

require("../app/functions.php");
require("../app/dbconnect.php");
require("../app/upload.php");

session_start();

if(!isset($_SESSION["join"])){
  header("Location: signup.php");
  exit();
}

if(!empty($_POST)){
  $fileName = $_FILES["image"]["name"];
  if(!empty($fileName)){
    $ext = substr($fileName, -3);
    if($ext !== "gif" && $ext !== "jpg" && $ext !== "png"){
      $error["image"] = "type";
    }
  }
  
  if(empty($error)){
    $image = bin2hex(random_bytes(12)) . $_FILES["image"]["name"];
    $tmp = $_FILES["image"]["tmp_name"];

    // プロフィール画像アップロード
      // 開発環境
    // move_uploaded_file($tmp, "member_img/" . $image);
      // 本番環境(S3)
    put_mem($image, $tmp);

    $_SESSION["join"]["image"] = $image;

    $stmt = $pdo->prepare("INSERT INTO members SET name=?, mail=?, pass=?, picture=?, created=NOW()");
    $stmt->execute([
      $_SESSION["join"]["name"],
      $_SESSION["join"]["mail"],
      sha1($_SESSION["join"]["pass"]),
      $_SESSION["join"]["image"],
    ]);
    $stmt = $pdo->prepare("SELECT * FROM members WHERE mail=?");
    $stmt->execute([$_SESSION["join"]["mail"]]);
    $result = $stmt->fetch();
    $_SESSION["id"] = $result["id"];
    unset($_SESSION["join"]);
    header("Location: main.php");
    exit();
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
  <link rel ="stylesheet" href="css/styles_check.css">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

  <!-- Google icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
  <div class="confirm">
    <h1>登録内容の確認(画像の選択)</h1>
    <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="submit">
      <dl>
        <dt>ユーザー名</dt>
        <dd>【 <?= h($_SESSION["join"]["name"]); ?> 】</dd>
        <dt>メールアドレス</dt>
        <dd>【 <?= h($_SESSION["join"]["mail"]); ?> 】</dd>
        <dt>パスワード</dt>
        <dd>【 非表示 】</dd>
        <dt>プロフィール画像（任意）</dt>
        <dd>
          <div id="image"><img src="img/select_none.jpg" class="image"></div> 
          <label class="edit-btn">
            プロフィール画像の選択
            <input type="file" name="image" size="35" accept="image/*" id="file">
          </label>
          <?php if($error["image"] === "type"): ?>
            <p class="error">*画像ファイルは「.jpg」「.png」「.gif」を選択してください</p>
          <?php endif; ?>
        </dd>
      </dl>
      <div class="whether">
        <a href="signup.php?action=rewrite">編集する</a>
        <span>/</span>
        <input type="submit" value="この内容で登録する">
        <img src="img/pen.png" class="pen">
      </div>
    </form>
    <img src="img/check_image.png" class="owl">
  </div>
  <footer>
    <p>© sample sample</p>
  </footer>

  <script src="js/check.js"></script>
</body>