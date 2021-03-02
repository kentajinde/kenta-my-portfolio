<?php

require("../app/functions.php");
require("../app/dbconnect.php");
require("../app/upload.php");

session_start();

//post_id
$id = $_REQUEST["id"];

if(!isset($id)){
  header("Location: main.php");
  exit();
}


// 投稿情報、投稿内容
$stmt = $pdo->prepare("SELECT * FROM posts JOIN content ON posts.id = content.post_id WHERE posts.id = ?");
$stmt->execute([$id]);
$posts = $stmt->fetchAll();


// メンバー情報
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$posts[0]["member_id"]]);
$member = $stmt->fetch();


// ライブラリ登録
if(!empty($_POST)){
  if(isset($_POST["rank1"])){
    $stmt = $pdo->prepare("SELECT * FROM library WHERE member_id = ? AND post_id = ? AND title = ?");
    $stmt->execute([$_SESSION["id"], $posts[0]["post_id"], $posts[0]["title"]]);
    $mylib = $stmt->fetch();
    if(empty($mylib)){
      $stmt = $pdo->prepare(
        "INSERT INTO library
        SET post_id=?, member_id=?, category=?, title=?, author=?, description=?, created = NOW()"
      );
      $stmt->execute([$posts[0]["post_id"], $_SESSION["id"], $posts[0]["category"], $posts[0]["title"], $posts[0]["author"], $posts[0]["description"]]);
      if(isset($posts[0]["picture"])){
        $fileName = substr_replace($posts[0]["picture"], bin2hex(random_bytes(12)), 0, 24);
        $stmt = $pdo->prepare(
          "UPDATE library
          SET picture = ?
          WHERE member_id = ? AND post_id = ? AND title = ?"
        );
        $stmt->execute([$fileName, $_SESSION["id"], $posts[0]["post_id"], $posts[0]["title"]]);
        if($s3Api){
          cop_pos($fileName, $posts[0]["picture"]);
        }else{
          copy("post_img/" . $posts[0]["picture"], "library_img/" . $fileName);
        }
      }
      header("Location: view.php?id=" . $id . "&action=successed");
    }else{
      $error["library1"] = "duplicate";
    }
  }
  if(isset($_POST["rank2"])){
    $stmt = $pdo->prepare("SELECT * FROM library WHERE member_id = ? AND post_id = ? AND title = ?");
    $stmt->execute([$_SESSION["id"], $posts[1]["post_id"], $posts[1]["title"]]);
    $mylib = $stmt->fetch();
    if(empty($mylib)){
      $stmt = $pdo->prepare(
        "INSERT INTO library
        SET post_id=?, member_id=?, category=?, title=?, author=?, description=?, created = NOW()"
      );
      $stmt->execute([$posts[1]["post_id"], $_SESSION["id"], $posts[1]["category"], $posts[1]["title"], $posts[1]["author"], $posts[1]["description"]]);
      if(isset($posts[1]["picture"])){
        $fileName = substr_replace($posts[1]["picture"], bin2hex(random_bytes(12)), 0, 24);
        $stmt = $pdo->prepare("UPDATE library
          SET picture = ?
          WHERE member_id = ? AND post_id = ? AND title = ?"
        );
        $stmt->execute([$fileName, $_SESSION["id"], $posts[1]["post_id"], $posts[1]["title"]]);
        if($s3Api){
          cop_pos($fileName, $posts[1]["picture"]);
        }else{
          copy("post_img/" . $posts[1]["picture"], "library_img/" . $fileName);
        }
      }
      header("Location: view.php?id=" . $id . "&action=successed");
    }else{
      $error["library2"] = "duplicate";
    }
  }
  if(isset($_POST["rank3"])){
    $stmt = $pdo->prepare("SELECT * FROM library WHERE member_id = ? AND post_id = ? AND title = ?");
    $stmt->execute([$_SESSION["id"], $posts[2]["post_id"], $posts[2]["title"]]);
    $mylib = $stmt->fetch();
    if(empty($mylib)){
      $stmt = $pdo->prepare(
        "INSERT INTO library
        SET post_id=?, member_id=?, category=?, title=?, author=?, description=?, created = NOW()"
      );
      $stmt->execute([$posts[2]["post_id"], $_SESSION["id"], $posts[2]["category"], $posts[2]["title"], $posts[2]["author"], $posts[2]["description"]]);
      if(isset($posts[2]["picture"])){
        $fileName = substr_replace($posts[2]["picture"], bin2hex(random_bytes(12)), 0, 24);
        $stmt = $pdo->prepare("UPDATE library
          SET picture = ?
          WHERE member_id = ? AND post_id = ? AND title = ?"
        );
        $stmt->execute([$fileName, $_SESSION["id"], $posts[2]["post_id"], $posts[2]["title"]]);
        if($s3Api){
          cop_pos($fileName, $posts[2]["picture"]);
        }else{
          copy("post_img/" . $posts[2]["picture"], "library_img/" . $fileName);
        }
      }
      header("Location: view.php?id=" . $id . "&action=successed");
    }else{
      $error["library3"] = "duplicate";
    }
  }
}

if($_REQUEST["action"] === "successed"){
  $successed = "successed";
}

// likes
$stmt = $pdo->prepare("SELECT * FROM likes WHERE member_id = ? AND post_id = ?");
$stmt->execute([$_SESSION["id"], $posts[0]["post_id"]]);
$like = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="お気に入りの本をシェアするWebサイトです。">
  <title>未定</title>

  <!-- 自作css -->
  <link rel ="stylesheet" href="css/styles_view.css">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

  <!-- Google icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
  <header class="nav-mobile">
    <a href="main.php">
      <img class="navbar-brand" src="img/logo.png" alt="logo">
    </a>
    <input id="menu" type="checkbox">
    <label for="menu" class="open">
      <span class="material-icons">menu</span>
    </label>
    <label for="menu" class="back"></label>
    <aside>
      <label for="menu" class="cls">
        <span class="material-icons">close</span>
      </label>
      <nav>
        <ul>
          <li><a href="posts.php">マイページ</a></li>
          <li><a href="posts_list.php">投稿一覧</a></li>
          <li><a href="ranking.php">ランキング</a></li>
          <li><a href="posts.php">ログアウト</a></li>
        </ul>
      </nav>
    </aside>
  </header>
  <header class="nav-pc">
    <nav class="navbar navbar-expand">
      <a href="main.php">
        <img class="navbar-brand" src="img/logo.png" alt="logo">
      </a>
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="posts.php">マイページ</a></li>
        <li class="nav-item"><a class="nav-link" href="posts_list.php">投稿一覧</a></li>
        <li class="nav-item"><a class="nav-link" href="ranking.php">ランキング</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">ログアウト</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <?php if(!empty($successed)): ?>
      <div class="successed">ライブラリに登録しました</div>
    <?php endif; ?>
    <a href="ranking.php" class="more">
      ランキングに戻る
      <span class="material-icons">
        keyboard_arrow_right
      </span>
    </a>
    <a href="posts_list.php" class="more">
      一覧に戻る
      <span class="material-icons">
        keyboard_arrow_right
      </span>
    </a>
    <div class="category">
      <h1>
        <?php if(h($posts[0]["category"]) === "busi"): ?>
          <?= "ビジネス"; ?>
        <?php endif; ?>
        <?php if(h($posts[0]["category"]) === "enlight"): ?>
          <?= "自己啓発"; ?>
        <?php endif; ?>
        <?php if(h($posts[0]["category"]) === "lite"): ?>
          <?= "文芸"; ?>
        <?php endif; ?>
        <?php if(h($posts[0]["category"]) === "plac"): ?>
          <?= "趣味・実用"; ?>
        <?php endif; ?>
        <?php if(h($posts[0]["category"]) === "comic"): ?>
          <?= "漫画"; ?>
        <?php endif; ?>
      </h1>
    </div>
    <div class="profile">
      <div class="view-detail">
          <?php if($member["picture"]): ?>
            <?php if($s3Api): ?>
              <img src="<?= h(get_mem($member["picture"])); ?>">
            <?php else: ?>
              <img src="member_img/<?= h($member["picture"]); ?>">
            <?php endif ?>
          <?php else: ?>
            <img src="img/select_none.jpg">
          <?php endif; ?>
        <h1><?= h($member["name"]); ?></h1>
      </div>

      <!-- likeボタン -->
      <label class="likes">
        <input type="hidden" id="member_id" value="<?= h($_SESSION["id"]); ?>">
        <input type="hidden" id="post_id" value="<?= h($posts[0]["post_id"]); ?>">
        <?php if(empty($like)): ?>
          <input type="checkbox" id="likes">
        <?php else: ?>
          <input type="checkbox" checked  id="likes">
        <?php endif; ?>
        <i class="material-icons heart">favorite</i>
        <div class="ripple" id="favorite"></div>
        <span id="like-num"><?= h($posts[0]["likes"]) ?></span>
      </label>

    </div>
    <div class="tab">
      <ul>
        <li class="active" data-id="one">1位</li>
        <li data-id="two">2位</li>
        <li data-id="three">3位</li>
      </ul>
    </div>
    <div class="rank content active" id="one">
      <h2>１位</h2>
      <div class="post-fb">
        <div class="post-fi image">
          <div id="rank1">
            <?php if(isset($posts[0]["picture"])): ?>
              <?php if($s3Api): ?>
                <img src="<?= h(get_pos($posts[0]["picture"])); ?>">
              <?php else: ?>
                <img src="post_img/<?= $posts[0]["picture"]; ?>">
              <?php endif; ?>
            <?php else: ?>
              <img src="img/no-image.png">
            <?php endif; ?>
          </div>
        </div>
        <div class="post-fi title">
          <div>
            <h3><?= h($posts[0]["title"]); ?></h3>
            <p><?= h($posts[0]["author"]); ?></p>
            <form action="" method="post">
              <input type="hidden" name="rank1">
              <input type="submit" value="ライブラリに登録する">
              <?php if($error["library1"] === "duplicate"): ?>
                <p class="error">*この本は既に登録されています</p>
              <?php endif; ?>
            </form>
          </div>
        </div>
      </div>
      <div class="desc-wrapper">
        <h3>説明</h3>
        <div class="desc">
          <p><?= h($posts[0]["description"]); ?></p>
        </div>
      </div>
    </div>
    <div class="rank content" id="two">
      <?php if($posts[1]["title"] !== ""): ?>
        <h2>２位</h2>
        <div class="post-fb">
          <div class="post-fi image">
            <div id="rank2">
              <?php if(isset($posts[1]["picture"])): ?>
                <?php if($s3Api): ?>
                  <img src="<?= h(get_pos($posts[1]["picture"])); ?>">
                <?php else: ?>
                  <img src="post_img/<?= $posts[1]["picture"]; ?>">
                <?php endif; ?>
              <?php else: ?>
                <img src="img/no-image.png">
              <?php endif; ?>
            </div>
          </div>
          <div class="post-fi title">
            <div>
              <h3><?= h($posts[1]["title"]); ?></h3>
              <p><?= h($posts[1]["author"]); ?></p>
              <form action="" method="post">
                <input type="hidden" name="rank2">
                <input type="submit" value="ライブラリに登録する">
                <?php if($error["library2"] === "duplicate"): ?>
                  <p class="error">*この本は既に登録されています</p>
                <?php endif; ?>
            </form>
            </div>
          </div>
        </div>
        <div class="desc-wrapper">
          <h3>説明</h3>
          <div class="desc">
            <p><?= h($posts[1]["description"]); ?></p>
          </div>
        </div>
      <?php else: ?>
        <div class="nothing">投稿がありません</div>
      <?php endif; ?>
    </div>
    <div class="rank content" id="three">
      <?php if($posts[2]["title"] !== ""): ?>
        <h2>３位</h2>
        <div class="post-fb">
          <div class="post-fi image">
            <div id="rank3">
              <?php if(isset($posts[2]["picture"])): ?>
                <?php if($s3Api): ?>
                  <img src="<?= h(get_pos($posts[2]["picture"])); ?>">
                <?php else: ?>
                  <img src="post_img/<?= $posts[2]["picture"]; ?>">
                <?php endif; ?>
              <?php else: ?>
                <img src="img/no-image.png">
              <?php endif; ?>
            </div>
          </div>
          <div class="post-fi title">
            <div>
              <h3><?= h($posts[2]["title"]); ?></h3>
              <p><?= h($posts[2]["author"]); ?></p>
              <form action="" method="post">
                <input type="hidden" name="rank3">
                <input type="submit" value="ライブラリに登録する">
                <?php if($error["library3"] === "duplicate"): ?>
                  <p class="error">*この本は既に登録されています</p>
                <?php endif; ?>
            </form>
            </div>
          </div>
        </div>
        <div class="desc-wrapper">
          <h3>説明</h3>
          <div class="desc">
            <p><?= h($posts[2]["description"]); ?></p>
          </div>
        </div>
      <?php else: ?>
        <div class="nothing">投稿がありません</div>
      <?php endif; ?>
    </div>
  </main>
  <script src="js/view.js"></script>
  <script type="text/javascript">
    {
      let number = <?= h($posts[0]["likes"]); ?>;
      const likes = document.getElementById("likes");
      const span = document.getElementById("like-num");

      likes.onclick = (e)=>{
        if(likes.checked){
          number += 1;
          span.innerHTML = number;
        }else{
          number -= 1;
          span.innerHTML = number;
        }
      };

    }
  </script>
</body>