<?php

require("../app/functions.php");
require("../app/dbconnect.php");
require("../app/upload.php");

session_start();

$stmt = $pdo->prepare("SELECT * FROM members WHERE id=?");
$stmt->execute([$_SESSION["id"]]);
$member = $stmt->fetch();


// プロフィール編集

if(!empty($_POST["profile"]) || !empty($_FILES)){
  if(blk($_POST["profile"]["name"]) !== ""){
    $stmt = $pdo->prepare("UPDATE members SET name=? WHERE id=?");
    $stmt->execute([$_POST["profile"]["name"], $_SESSION["id"]]);
  }
  if(blk($_POST["profile"]["mail"]) !== ""){
    $stmt = $pdo->prepare("UPDATE members SET mail=? WHERE id=?");
    $stmt->execute([$_POST["profile"]["mail"], $_SESSION["id"]]);
  }
  if(!empty($_FILES["image"]["name"])){
    $fileName = bin2hex(random_bytes(12)) . $_FILES["image"]["name"];
    $tmp = $_FILES["image"]["tmp_name"];
    if($s3Api){
      put_mem($fileName, $tmp);
      del_mem($member["picture"]);
    }else{
      move_uploaded_file($tmp, "member_img/" . $fileName);
      unlink("member_img/" . $member["picture"]);
    }
    $stmt = $pdo->prepare("UPDATE members SET picture=? WHERE id=?");
    $stmt->execute([$fileName, $_SESSION["id"]]);
  }
  header("Location: posts.php");
  exit();
}


// 新規投稿

if(!empty($_POST["content"])){
  if(blk($_POST["category"]) === ""){
    $error["category"] = "blank";
  }
  if(blk($_POST["content"][0]["title"]) === ""){
    $error["post1"] = "blank";
  }
  if(blk($_POST["content"][1]["author"]) !== "" || blk($_POST["content"][1]["desc"]) !== ""){
    if(blk($_POST["content"][1]["title"]) === ""){
      $error["post2"] = "blank";
    }
  }
  if(blk($_POST["content"][2]["author"]) !== "" || blk($_POST["content"][2]["desc"]) !== ""){
    if(blk($_POST["content"][2]["title"]) === ""){
      $error["post3"] = "blank";
    }
  }
  if(empty($error)){
    $stmt = $pdo->prepare(
      "SELECT * 
       FROM (SELECT * FROM posts WHERE member_id = ? AND category = ?) AS posts"
    );
    $stmt->execute([$member["id"], $_POST["category"]]);
    $result = $stmt->fetch();
    if($result){
      $error["category"] = "duplicate";
    }
  }
  if(empty($error)){
    $_SESSION["post"] = $_POST;
    header("Location: check_post.php");
    exit();
  }
}

if($_REQUEST["action"] === "rewrite" && isset($_SESSION["post"])){
  $_POST = $_SESSION["post"];
}

if($_REQUEST["action"] === "successed"){
  $successed = "successed";
}


// 投稿内容取得
$stmt = $pdo->prepare("SELECT * FROM posts WHERE member_id = ?");
$stmt->execute([$member["id"]]);
$all = $stmt->fetchAll();

// ビジネス
$stmt = $pdo->prepare(
  "SELECT title, author, description, picture, likes, post_id
  FROM (SELECT * FROM posts WHERE member_id = ? AND category = 'busi') AS posts
  JOIN content
  ON posts.id = content.post_id"
  );
$stmt->execute([$_SESSION["id"]]);
$busi = $stmt->fetchAll();

// 自己啓発
$stmt = $pdo->prepare(
  "SELECT title, author, description, picture, likes, post_id 
  FROM (SELECT * FROM posts WHERE member_id = ? AND category = 'enlight') AS posts
  JOIN content
  ON posts.id = content.post_id"
  );
$stmt->execute([$_SESSION["id"]]);
$enlight = $stmt->fetchAll();

// 文芸
$stmt = $pdo->prepare(
  "SELECT title, author, description, picture, likes, post_id 
  FROM (SELECT * FROM posts WHERE member_id = ? AND category = 'lite') AS posts
  JOIN content
  ON posts.id = content.post_id"
  );
$stmt->execute([$_SESSION["id"]]);
$lite = $stmt->fetchAll();

// 趣味・実用
$stmt = $pdo->prepare(
  "SELECT title, author, description, picture, likes, post_id 
  FROM (SELECT * FROM posts WHERE member_id = ? AND category = 'plac') AS posts
  JOIN content
  ON posts.id = content.post_id"
  );
$stmt->execute([$_SESSION["id"]]);
$plac = $stmt->fetchAll();

// 漫画
$stmt = $pdo->prepare(
  "SELECT title, author, description, picture, likes, post_id 
  FROM (SELECT * FROM posts WHERE member_id = ? AND category = 'comic') AS posts
  JOIN content
  ON posts.id = content.post_id"
  );
$stmt->execute([$_SESSION["id"]]);
$comic = $stmt->fetchAll();


// ライブラリ取得

// ビジネス
$stmt = $pdo->prepare(
  "SELECT *, (SELECT COUNT(id) FROM library WHERE member_id = :id AND category = 'busi') as cnt
  FROM library 
  WHERE member_id = :id AND category = 'busi'
  ORDER BY created DESC");
$stmt->execute(["id"=>$_SESSION["id"]]);
$lib_busi = $stmt->fetchAll();

// 自己啓発
$stmt = $pdo->prepare(
  "SELECT *, (SELECT COUNT(id) FROM library WHERE member_id = :id AND category = 'enlight') as cnt
  FROM library 
  WHERE member_id = :id AND category = 'enlight'
  ORDER BY created DESC");
$stmt->execute(["id"=>$_SESSION["id"]]);
$lib_enlight = $stmt->fetchAll();

// 文芸
$stmt = $pdo->prepare(
  "SELECT *, (SELECT COUNT(id) FROM library WHERE member_id = :id AND category = 'lite') as cnt
  FROM library 
  WHERE member_id = :id AND category = 'lite'
  ORDER BY created DESC");
$stmt->execute(["id"=>$_SESSION["id"]]);
$lib_lite = $stmt->fetchAll();

// 趣味・実用
$stmt = $pdo->prepare(
  "SELECT *, (SELECT COUNT(id) FROM library WHERE member_id = :id AND category = 'plac') as cnt
  FROM library 
  WHERE member_id = :id AND category = 'plac'
  ORDER BY created DESC");
$stmt->execute(["id"=>$_SESSION["id"]]);
$lib_plac = $stmt->fetchAll();

// 漫画
$stmt = $pdo->prepare(
  "SELECT *, (SELECT COUNT(id) FROM library WHERE member_id = :id AND category = 'comic') as cnt
  FROM library 
  WHERE member_id = :id AND category = 'comic'
  ORDER BY created DESC");
$stmt->execute(["id"=>$_SESSION["id"]]);
$lib_comic = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="お気に入りの本をシェアするWebサイトです。">
  <title>未定</title>

  <!-- 自作css -->
  <link rel ="stylesheet" href="css/styles_posts.css">

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
    <div class="successed">投稿が完了しました</div>
  <?php endif; ?>
  <!-- プロフィール編集 -->
  <div class="myp-wrapper">
    <div class="profile">
      <?php if($member["picture"]): ?>
        <?php if($s3Api): ?>
          <img src="<?= h(get_mem($member["picture"])); ?>">
        <?php else: ?>
          <img src="member_img/<?= h($member["picture"]); ?>">
        <?php endif; ?>
      <?php else: ?>
        <img src="img/select_none.jpg">
      <?php endif; ?>
      <div class="detail">
        <h1><?= h($member["name"]); ?></h1>
        <div id="edit" class="edit-btn">プロフィールを編集する</div>
        <!-- モーダルウィンドウ -->
        <div id="mask" class="hidden"></div>
        <div id="modal">
          <form action="" method="post" enctype="multipart/form-data">
            <label>
              ユーザー名
              <input type="text" name="profile[name]">
            </label>
            <label>
              メールアドレス
              <input type="text" name="profile[mail]">
            </label>
            <label class="edit-btn">
              プロフィール画像の選択
              <input type="file" name="image" size="35" accept="image/*">
            </label>
            <div class="confirm">
              <label class="confirm-btn">
                OK
                <input type="submit">
              </label>
              <div class="confirm-btn cansel">キャンセル</div>
            </div>
          </form>
        </div>
      </div>
    </div>
  
    <!-- タブメニュー -->
    <div class="tab">
      <input type="checkbox" id="delete">
      <div id="modal-delete"></div>
      <ul>
        <li class="active" data-id="post">新規投稿</li>
        <li data-id="posts">自分の投稿</li>
        <li data-id="library">ライブラリ</li>
      </ul>
  
      <!-- 新規投稿 -->
      <div class="content active" id="post">
        <p>
          本のカテゴリーごとに、あなたのベスト３を投稿してください！
        </p>
        <form action="" method="post">
          <select name="category">
            <option selected disabled value="">カテゴリー</option>
            <option value="busi">ビジネス</option>
            <option value="enlight">自己啓発</option>
            <option value="lite">文芸</option>
            <option value="plac">趣味・実用</option>
            <option value="comic">漫画</option>
          </select>
          <?php if($error["category"] === "blank"): ?>
            <p class="error">*カテゴリーを選択してください</p>
          <?php endif; ?>
          <?php if($error["category"] === "duplicate"): ?>
            <p class="error">
              *このカテゴリーは既に投稿済みです。新規投稿を行う場合は、「自分の投稿」から削除してください。
            </p>
          <?php endif; ?>
  
          <div class="post-rank">
            <p>１位<strong>（必須）</strong></p>
            <input type="text" name="content[0][title]" placeholder="本のタイトル" value="<?= blk(h($_POST["content"][0]["title"])); ?>">
            <input type="text" name="content[0][author]"  placeholder="著者" value="<?= blk(h($_POST["content"][0]["author"])); ?>">
            <textarea  rows=5 cols=60 name="content[0][desc]" placeholder="この本について、自由に紹介してください"><?= blk(h($_POST["content"][0]["desc"])); ?></textarea>
            <?php if($error["post1"] === "blank"): ?>
              <p class="error">*タイトルを入力してください</p>
            <?php endif; ?>
          </div>
  
          <div class="post-rank">
            <p>２位（任意）</p>
            <input type="text" name="content[1][title]" placeholder="本のタイトル" value="<?= blk(h($_POST["content"][1]["title"])); ?>">
            <input type="text" name="content[1][author]"  placeholder="著者" value="<?= blk(h($_POST["content"][1]["author"])); ?>">
            <textarea  rows=5 cols=60 name="content[1][desc]" placeholder="この本について、自由に紹介してください"><?= blk(h($_POST["content"][1]["desc"])); ?></textarea>
            <?php if($error["post2"] === "blank"): ?>
              <p class="error">*タイトルを入力してください</p>
            <?php endif; ?>
          </div>
  
          <div class="post-rank">
            <p>３位（任意）</p>
            <input type="text" name="content[2][title]" placeholder="本のタイトル" value="<?= blk(h($_POST["content"][2]["title"])); ?>">
            <input type="text" name="content[2][author]"  placeholder="著者" value="<?= blk(h($_POST["content"][2]["author"])); ?>">
            <textarea  rows=5 cols=60 name="content[2][desc]" placeholder="この本について、自由に紹介してください"><?= blk(h($_POST["content"][2]["desc"])); ?></textarea>
            <?php if($error["post3"] === "blank"): ?>
              <p class="error">*タイトルを入力してください</p>
            <?php endif; ?>
          </div>
  
          <label class="edit-btn">
            内容を確認する（画像の選択）
            <input type="submit">
          </label>
        </form>
      </div>
  
      <!-- 自分の投稿 -->
      <div class="content" id="posts">
        <ul>
          <li>
            <label for="busi-post" class="posts-label" data-id="busi-span">
              ビジネス
              <?php if(!empty($busi)): ?>
                <div class="likes">
                  <i class="material-icons">favorite</i>
                  <span><?= h($busi[0]["likes"]); ?></span>
                </div>
              <?php endif; ?>
              <span class="material-icons" id="busi-span">
                keyboard_arrow_down
              </span>
            </label>
            <input type="checkbox" id="busi-post" class="accordion">
            <?php if(!empty($busi)): ?>
              <div class="busi-post post-content">
                <div class="rank">
                  <h2>１位</h2>
                  <div class="post-fb">
                    <div class="post-fi image">
                      <div id="rank1">
                        <?php if(isset($busi[0]["picture"])): ?>
                          <?php if($s3Api): ?>
                            <img src="<?= h(get_pos($busi[0]["picture"])); ?>">
                          <?php else: ?>
                            <img src="post_img/<?= $busi[0]["picture"]; ?>">
                          <?php endif; ?>
                        <?php else: ?>
                          <img src="img/no-image.png">
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="post-fi title">
                      <h3><?= h($busi[0]["title"]); ?></h3>
                      <p><?= h($busi[0]["author"]); ?></p>
                    </div>
                  </div>
                  <div class="desc-wrapper">
                    <h3>説明</h3>
                    <div class="desc">
                      <p><?= h($busi[0]["description"]); ?></p>
                    </div>
                  </div>
                </div>
                <?php if($busi[1]["title"] !== ""): ?>
                  <div class="rank">
                    <h2>２位</h2>
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank2">
                          <?php if(isset($busi[1]["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($busi[1]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= $busi[1]["picture"]; ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($busi[1]["title"]); ?></h3>
                        <p><?= h($busi[1]["author"]); ?></p>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($busi[1]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if($busi[2]["title"] !== ""): ?>
                  <div class="rank">
                    <h2>３位</h2>
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank3">
                          <?php if(isset($busi[2]["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($busi[2]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= $busi[2]["picture"]; ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($busi[2]["title"]); ?></h3>
                        <p><?= h($busi[2]["author"]); ?></p>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($busi[2]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="delete">
                  </label>
                  <a href="delete.php?id=<?= h($busi[0]["post_id"]); ?>" class="confirm-btn">
                    投稿を削除する
                  </a>
                </div>
              </div>
            <?php else: ?>
              <div class="post-nothing">投稿がありません<div>
            <?php endif; ?>
          </li>
          <li>
            <label for="enlight-post" class="posts-label" data-id="enlight-span">
              自己啓発
              <?php if(!empty($enlight)): ?>
                <div class="likes">
                  <i class="material-icons">favorite</i>
                  <span><?= h($enlight[0]["likes"]); ?></span>
                </div>
              <?php endif; ?>
              <span class="material-icons" id="enlight-span">
                keyboard_arrow_down
              </span>
            </label>
            <input type="checkbox" id="enlight-post" class="accordion">
            <?php if(!empty($enlight)): ?>
              <div class="enlight-post post-content">
                <div class="rank">
                  <h2>１位</h2>
                  <div class="post-fb">
                    <div class="post-fi image">
                      <div id="rank1">
                        <?php if(isset($enlight[0]["picture"])): ?>
                          <?php if($s3Api): ?>
                            <img src="<?= h(get_pos($enlight[0]["picture"])); ?>">
                          <?php else: ?>
                            <img src="post_img/<?= $enlight[0]["picture"]; ?>">
                          <?php endif; ?>
                        <?php else: ?>
                          <img src="img/no-image.png">
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="post-fi title">
                      <h3><?= h($enlight[0]["title"]); ?></h3>
                      <p><?= h($enlight[0]["author"]); ?></p>
                    </div>
                  </div>
                  <div class="desc-wrapper">
                    <h3>説明</h3>
                    <div class="desc">
                      <p><?= h($enlight[0]["description"]); ?></p>
                    </div>
                  </div>
                </div>
                <?php if($enlight[1]["title"] !== ""): ?>
                  <div class="rank">
                    <h2>２位</h2>
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank2">
                          <?php if(isset($enlight[1]["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($enlight[1]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= $enlight[1]["picture"]; ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($enlight[1]["title"]); ?></h3>
                        <p><?= h($enlight[1]["author"]); ?></p>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($enlight[1]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if($enlight[2]["title"] !== ""): ?>
                  <div class="rank">
                    <h2>３位</h2>
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank3">
                          <?php if(isset($enlight[2]["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($enlight[2]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= $enlight[2]["picture"]; ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($enlight[2]["title"]); ?></h3>
                        <p><?= h($enlight[2]["author"]); ?></p>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($enlight[2]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="delete">
                  </label>
                  <a href="delete.php?id=<?= h($enlight[0]["post_id"]); ?>" class="confirm-btn">
                    投稿を削除する
                  </a>
                </div>
              </div>
            <?php else: ?>
              <div class="post-nothing">投稿がありません<div>
            <?php endif; ?>
          </li>
          <li>
            <label for="lite-post" class="posts-label" data-id="lite-span">
              文芸
              <?php if(!empty($lite)): ?>
                <div class="likes">
                  <i class="material-icons">favorite</i>
                  <span><?= h($lite[0]["likes"]); ?></span>
                </div>
              <?php endif; ?>
              <span class="material-icons" id="lite-span">
                keyboard_arrow_down
              </span>
            </label>
            <input type="checkbox" id="lite-post" class="accordion">
            <?php if(!empty($lite)): ?>
              <div class="lite-post post-content">
                <div class="rank">
                  <h2>１位</h2>
                  <div class="post-fb">
                    <div class="post-fi image">
                      <div id="rank1">
                        <?php if(isset($lite[0]["picture"])): ?>
                          <?php if($s3Api): ?>
                            <img src="<?= h(get_pos($lite[0]["picture"])); ?>">
                          <?php else: ?>
                            <img src="post_img/<?= $lite[0]["picture"]; ?>">
                          <?php endif; ?>
                        <?php else: ?>
                          <img src="img/no-image.png">
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="post-fi title">
                      <h3><?= h($lite[0]["title"]); ?></h3>
                      <p><?= h($lite[0]["author"]); ?></p>
                    </div>
                  </div>
                  <div class="desc-wrapper">
                    <h3>説明</h3>
                    <div class="desc">
                      <p><?= h($lite[0]["description"]); ?></p>
                    </div>
                  </div>
                </div>
                <?php if($lite[1]["title"] !== ""): ?>
                  <div class="rank">
                    <h2>２位</h2>
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank2">
                          <?php if(isset($lite[1]["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($lite[1]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= $lite[1]["picture"]; ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($lite[1]["title"]); ?></h3>
                        <p><?= h($lite[1]["author"]); ?></p>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($lite[1]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if($lite[2]["title"] !== ""): ?>
                  <div class="rank">
                    <h2>３位</h2>
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank3">
                          <?php if(isset($lite[2]["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($lite[2]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= $lite[2]["picture"]; ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($lite[2]["title"]); ?></h3>
                        <p><?= h($lite[2]["author"]); ?></p>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($lite[2]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="delete">
                  </label>
                  <a href="delete.php?id=<?= h($lite[0]["post_id"]); ?>" class="confirm-btn">
                    投稿を削除する
                  </a>
                </div>
              </div>
            <?php else: ?>
              <div class="post-nothing">投稿がありません<div>
            <?php endif; ?>
          </li>
          <li>
            <label for="plac-post" class="posts-label" data-id="plac-span">
              趣味・実用
              <?php if(!empty($plac)): ?>
                <div class="likes">
                  <i class="material-icons">favorite</i>
                  <span><?= h($plac[0]["likes"]); ?></span>
                </div>
              <?php endif; ?>
              <span class="material-icons" id="plac-span">
                keyboard_arrow_down
              </span>
            </label>
            <input type="checkbox" id="plac-post" class="accordion">
            <?php if(!empty($plac)): ?>
              <div class="plac-post post-content">
                <div class="rank">
                  <h2>１位</h2>
                  <div class="post-fb">
                    <div class="post-fi image">
                      <div id="rank1">
                        <?php if(isset($plac[0]["picture"])): ?>
                          <?php if($s3Api): ?>
                            <img src="<?= h(get_pos($plac[0]["picture"])); ?>">
                          <?php else: ?>
                            <img src="post_img/<?= $plac[0]["picture"]; ?>">
                          <?php endif; ?>
                        <?php else: ?>
                          <img src="img/no-image.png">
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="post-fi title">
                      <h3><?= h($plac[0]["title"]); ?></h3>
                      <p><?= h($plac[0]["author"]); ?></p>
                    </div>
                  </div>
                  <div class="desc-wrapper">
                    <h3>説明</h3>
                    <div class="desc">
                      <p><?= h($plac[0]["description"]); ?></p>
                    </div>
                  </div>
                </div>
                <?php if($plac[1]["title"] !== ""): ?>
                  <div class="rank">
                    <h2>２位</h2>
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank2">
                          <?php if(isset($plac[1]["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($plac[1]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= $plac[1]["picture"]; ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($plac[1]["title"]); ?></h3>
                        <p><?= h($plac[1]["author"]); ?></p>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($plac[1]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if($plac[2]["title"] !== ""): ?>
                  <div class="rank">
                    <h2>３位</h2>
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank3">
                          <?php if(isset($plac[2]["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($plac[2]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= $plac[2]["picture"]; ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($plac[2]["title"]); ?></h3>
                        <p><?= h($plac[2]["author"]); ?></p>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($plac[2]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="delete">
                  </label>
                  <a href="delete.php?id=<?= h($plac[0]["post_id"]); ?>" class="confirm-btn">
                    投稿を削除する
                  </a>
                </div>
              </div>
            <?php else: ?>
              <div class="post-nothing">投稿がありません<div>
            <?php endif; ?>
          </li>
          <li>
            <label for="comic-post" class="posts-label" data-id="comic-span">
              漫画
              <?php if(!empty($comic)): ?>
                <div class="likes">
                  <i class="material-icons">favorite</i>
                  <span><?= h($comic[0]["likes"]); ?></span>
                </div>
              <?php endif; ?>
              <span class="material-icons" id="comic-span">
                keyboard_arrow_down
              </span>
            </label>
            <input type="checkbox" id="comic-post" class="accordion">
            <?php if(!empty($comic)): ?>
              <div class="comic-post post-content">
                <div class="rank">
                  <h2>１位</h2>
                  <div class="post-fb">
                    <div class="post-fi image">
                      <div id="rank1">
                        <?php if(isset($comic[0]["picture"])): ?>
                          <?php if($s3Api): ?>
                            <img src="<?= h(get_pos($comic[0]["picture"])); ?>">
                          <?php else: ?>
                            <img src="post_img/<?= $comic[0]["picture"]; ?>">
                          <?php endif; ?>
                        <?php else: ?>
                          <img src="img/no-image.png">
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="post-fi title">
                      <h3><?= h($comic[0]["title"]); ?></h3>
                      <p><?= h($comic[0]["author"]); ?></p>
                    </div>
                  </div>
                  <div class="desc-wrapper">
                    <h3>説明</h3>
                    <div class="desc">
                      <p><?= h($comic[0]["description"]); ?></p>
                    </div>
                  </div>
                </div>
                <?php if($comic[1]["title"] !== ""): ?>
                  <div class="rank">
                    <h2>２位</h2>
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank2">
                          <?php if(isset($comic[1]["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($comic[1]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= $comic[1]["picture"]; ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($comic[1]["title"]); ?></h3>
                        <p><?= h($comic[1]["author"]); ?></p>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($comic[1]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if($comic[2]["title"] !== ""): ?>
                  <div class="rank">
                    <h2>３位</h2>
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank3">
                          <?php if(isset($comic[2]["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($comic[2]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= $comic[2]["picture"]; ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($comic[2]["title"]); ?></h3>
                        <p><?= h($comic[2]["author"]); ?></p>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($comic[2]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="delete">
                  </label>
                  <a href="delete.php?id=<?= h($comic[0]["post_id"]); ?>" class="confirm-btn">
                    投稿を削除する
                  </a>
                </div>
              </div>
            <?php else: ?>
              <div class="post-nothing">投稿がありません<div>
            <?php endif; ?>
          </li>
        </ul>
      </div>
  
  
      <!-- ライブラリ -->
      <div class="content" id="library">
        <ul>
          <li>
            <label for="lib-busi" class="posts-label" data-id="lib_busi-span">
              ビジネス
              <span>
                <?php if($lib_busi[0]["cnt"] > 0): ?>
                  (<?= $lib_busi[0]["cnt"]; ?>)
                <?php else: ?>
                  (0)
                <?php endif; ?>
              </span>
              <span class="material-icons" id="lib_busi-span">
                keyboard_arrow_down
              </span>
            </label>
            <input type="checkbox" id="lib-busi" class="accordion">
            <?php if(!empty($lib_busi)): ?>
              <div class="lib-busi">
                <?php foreach($lib_busi as $post): ?>
                  <div class="rank">
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank1">
                          <?php if(isset($post["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_lib($post["picture"])); ?>">
                            <?php else: ?>
                              <img src="library_img/<?= h($post["picture"]); ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($post["title"]); ?></h3>
                        <p><?= h($post["author"]); ?></p>
                            登録を解除する
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($post[0]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="post-nothing">登録はありません<div>
            <?php endif; ?>
          </li>
          <li>
            <label for="lib-enlight" class="posts-label" data-id="lib_enlight-span">
              自己啓発
              <span>
                <?php if($lib_enlight[0]["cnt"] > 0): ?>
                  (<?= $lib_enlight[0]["cnt"]; ?>)
                <?php else: ?>
                  (0)
                <?php endif; ?>
              </span>
              <span class="material-icons" id="lib_enlight-span">
                keyboard_arrow_down
              </span>
            </label>
            <input type="checkbox" id="lib-enlight" class="accordion">
            <?php if(!empty($lib_enlight)): ?>
              <div class="lib-enlight">
                <?php foreach($lib_enlight as $post): ?>
                  <div class="rank">
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank1">
                          <?php if(isset($post["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_lib($post["picture"])); ?>">
                            <?php else: ?>
                              <img src="library_img/<?= h($post["picture"]); ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($post["title"]); ?></h3>
                        <p><?= h($post["author"]); ?></p>
                            登録を解除する
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($post[0]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="post-nothing">登録はありません<div>
            <?php endif; ?>
          </li>
          <li>
            <label for="lib-lite" class="posts-label" data-id="lib_lite-span">
              文芸
              <span>
                <?php if($lib_lite[0]["cnt"] > 0): ?>
                  (<?= $lib_lite[0]["cnt"]; ?>)
                <?php else: ?>
                  (0)
                <?php endif; ?>
              </span>
              <span class="material-icons" id="lib_lite-span">
                keyboard_arrow_down
              </span>
            </label>
            <input type="checkbox" id="lib-lite" class="accordion">
            <?php if(!empty($lib_lite)): ?>
              <div class="lib-lite">
                <?php foreach($lib_lite as $post): ?>
                  <div class="rank">
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank1">
                          <?php if(isset($post["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_lib($post["picture"])); ?>">
                            <?php else: ?>
                              <img src="library_img/<?= h($post["picture"]); ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($post["title"]); ?></h3>
                        <p><?= h($post["author"]); ?></p>
                            登録を解除する
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($post[0]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="post-nothing">登録はありません<div>
            <?php endif; ?>
          </li>
          <li>
            <label for="lib-plac" class="posts-label" data-id="lib_plac-span">
              趣味・実用
              <span>
                <?php if($lib_plac[0]["cnt"] > 0): ?>
                  (<?= $lib_plac[0]["cnt"]; ?>)
                <?php else: ?>
                  (0)
                <?php endif; ?>
              </span>
              <span class="material-icons" id="lib_plac-span">
                keyboard_arrow_down
              </span>
            </label>
            <input type="checkbox" id="lib-plac" class="accordion">
            <?php if(!empty($lib_plac)): ?>
              <div class="lib-plac">
                <?php foreach($lib_plac as $post): ?>
                  <div class="rank">
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank1">
                          <?php if(isset($post["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_lib($post["picture"])); ?>">
                            <?php else: ?>
                              <img src="library_img/<?= h($post["picture"]); ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($post["title"]); ?></h3>
                        <p><?= h($post["author"]); ?></p>
                            登録を解除する
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($post[0]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="post-nothing">登録はありません<div>
            <?php endif; ?>
          </li>
          <li>
            <label for="lib-comic" class="posts-label" data-id="lib_comic-span">
              漫画
              <span>
                <?php if($lib_comic[0]["cnt"] > 0): ?>
                  (<?= $lib_comic[0]["cnt"]; ?>)
                <?php else: ?>
                  (0)
                <?php endif; ?>
              </span>
              <span class="material-icons" id="lib_comic-span">
                keyboard_arrow_down
              </span>
            </label>
            <input type="checkbox" id="lib-comic" class="accordion">
            <?php if(!empty($lib_comic)): ?>
              <div class="lib-comic">
                <?php foreach($lib_comic as $post): ?>
                  <div class="rank">
                    <div class="post-fb">
                      <div class="post-fi image">
                        <div id="rank1">
                          <?php if(isset($post["picture"])): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_lib($post["picture"])); ?>">
                            <?php else: ?>
                              <img src="library_img/<?= h($post["picture"]); ?>">
                            <?php endif; ?>
                          <?php else: ?>
                            <img src="img/no-image.png">
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="post-fi title">
                        <h3><?= h($post["title"]); ?></h3>
                        <p><?= h($post["author"]); ?></p>
                            登録を解除する
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="desc-wrapper">
                      <h3>説明</h3>
                      <div class="desc">
                        <p><?= h($post[0]["description"]); ?></p>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="post-nothing">登録はありません<div>
            <?php endif; ?>
          </li>
        </ul>
      </div>
  
    </div>
  </div>
</main>
<footer>
  <p>Copyright Kenta All right reserved.</p>
</footer>
<script src="js/posts.js"></script>
</body>
</html>