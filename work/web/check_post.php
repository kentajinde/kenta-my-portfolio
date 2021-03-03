<?php

require("../app/functions.php");
require("../app/dbconnect.php");
require("../app/upload.php");

session_start();

if(!empty($_POST)){
  if(!empty($_FILES["image"]["name"][0])){
    $image = bin2hex(random_bytes(12)) . $_FILES["image"]["name"][0];
    $tmp = $_FILES["image"]["tmp_name"][0];
    if($s3Api){
      put_pos($image, $tmp);
    }else{
      move_uploaded_file($_FILES["image"]["tmp_name"][0], "post_img/" . $image);
    }
    $image1 = $image;
  }
  if(!empty($_FILES["image"]["name"][1])){
    $image = bin2hex(random_bytes(12)) . $_FILES["image"]["name"][1];
    $tmp = $_FILES["image"]["tmp_name"][1];
    if($s3Api){
      put_pos($image, $tmp);
    }else{
      move_uploaded_file($_FILES["image"]["tmp_name"][1], "post_img/" . $image);
    }
    $image2 = $image;
  }
  if(!empty($_FILES["image"]["name"][2])){
    $image = bin2hex(random_bytes(12)) . $_FILES["image"]["name"][2];
    $tmp = $_FILES["image"]["tmp_name"][2];
    if($s3Api){
      put_pos($image, $tmp);
    }else{
      move_uploaded_file($_FILES["image"]["tmp_name"][2], "post_img/" . $image);
    }
    $image3 = $image;
  }

  $content1 = $_SESSION["post"]["content"][0];
  $content2 = $_SESSION["post"]["content"][1];
  $content3 = $_SESSION["post"]["content"][2];

  $stmt = $pdo->prepare("INSERT INTO posts SET member_id=?, likes=0, category=?, created=NOW()");
  $stmt->execute([$_SESSION["id"], $_SESSION["post"]["category"]]);

  $stmt = $pdo->prepare("INSERT INTO content(post_id, title, author, description, picture) VALUES
    (LAST_INSERT_ID(), :title1, :author1, :desc1, :pic1),
    (LAST_INSERT_ID(), :title2, :author2, :desc2, :pic2),
    (LAST_INSERT_ID(), :title3, :author3, :desc3, :pic3);
  ");
  $stmt->execute([
    "title1"=>$content1["title"], "author1"=>$content1["author"], "desc1"=>$content1["desc"], "pic1"=>$image1,
    "title2"=>$content2["title"], "author2"=>$content2["author"], "desc2"=>$content2["desc"], "pic2"=>$image2,
    "title3"=>$content3["title"], "author3"=>$content3["author"], "desc3"=>$content3["desc"], "pic3"=>$image3,
    ]);
  unset($_SESSION["post"]);
  header("Location: posts.php?action=successed");
  exit();
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
  <link rel ="stylesheet" href="css/styles_check_post.css">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

  <!-- Google icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
  <!-- <pre>
    <//?= var_dump($_FILES["image"]); ?>
  </pre> -->
  <main>
    <h1>入力内容の確認(画像の選択)</h1>
    <div class="confirm-wrapper">
      <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="submit">
        <div class="category">
          <h1>
            <?php if(h($_SESSION["post"]["category"]) === "busi"): ?>
              <?= "ビジネス"; ?>
            <?php endif; ?>
            <?php if(h($_SESSION["post"]["category"]) === "enlight"): ?>
              <?= "自己啓発"; ?>
            <?php endif; ?>
            <?php if(h($_SESSION["post"]["category"]) === "lite"): ?>
              <?= "文芸"; ?>
            <?php endif; ?>
            <?php if(h($_SESSION["post"]["category"]) === "plac"): ?>
              <?= "趣味・実用"; ?>
            <?php endif; ?>
            <?php if(h($_SESSION["post"]["category"]) === "comic"): ?>
              <?= "漫画"; ?>
            <?php endif; ?>
          </h1>
        </div>
        <div class="rank">
          <h2>１位</h2>
          <div class="post-fb">
            <div class="post-fi image">
              <div id="rank1">
                <img src="img/no-image.png">
              </div>
              <label class="edit-btn">
                画像を選択する
                <input type="file" name="image[]" size="35" accept="image/*" data-id="rank1">
              </label>
            </div>
            <div class="post-fi title">
              <h3><?= h($_SESSION["post"]["content"][0]["title"]); ?></h3>
              <p><?= h($_SESSION["post"]["content"][0]["author"]); ?></p>
            </div>
          </div>
          <div class="desc-wrapper">
            <h3>説明</h3>
            <div class="desc">
              <p><?= h($_SESSION["post"]["content"][0]["desc"]); ?></p>
            </div>
          </div>
        </div>
        <?php if(h($_SESSION["post"]["content"][1]["title"]) !== ""): ?>
          <div class="rank">
            <h2>２位</h2>
            <div class="post-fb">
              <div class="post-fi image">
                <div id="rank2">
                  <img src="img/no-image.png">
                </div>
                <label class="edit-btn">
                  画像を選択する
                  <input type="file" name="image[]" size="35" accept="image/*" data-id="rank2">
                </label>
              </div>
              <div class="post-fi title">
                <h3><?= h($_SESSION["post"]["content"][1]["title"]); ?></h3>
                <p><?= h($_SESSION["post"]["content"][1]["author"]); ?></p>
              </div>
            </div>
            <div class="desc-wrapper">
              <h3>説明</h3>
              <div class="desc">
                <p><?= h($_SESSION["post"]["content"][1]["desc"]); ?></p>
              </div>
            </div>
          </div>
        <?php endif; ?>
        <?php if(h($_SESSION["post"]["content"][2]["title"]) !== ""): ?>
          <div class="rank">
            <h2>３位</h2>
            <div class="post-fb">
              <div class="post-fi image">
                <div id="rank3">
                  <img src="img/no-image.png">
                </div>
                <label class="edit-btn">
                  画像を選択する
                  <input type="file" name="image[]" size="35" accept="image/*" data-id="rank3">
                </label>
              </div>
              <div class="post-fi title">
                <h3><?= h($_SESSION["post"]["content"][2]["title"]); ?></h3>
                <p><?= h($_SESSION["post"]["content"][2]["author"]); ?></p>
              </div>
            </div>
            <div class="desc-wrapper">
              <h3>説明</h3>
              <div class="desc">
                <p><?= h($_SESSION["post"]["content"][2]["desc"]); ?></p>
              </div>
            </div>
          </div>
        <?php endif; ?>
        <div class="decision">
          <a href="posts.php?action=rewrite" class="confirm-btn">編集する</a>
          <label class="confirm-btn">
            この内容で投稿する
            <input type="submit">
          </label>
        </div>
      </form>
    </div>
  </main>
  <script src="js/check_post.js"></script>
</body>
