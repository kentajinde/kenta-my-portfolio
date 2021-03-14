<?php

require("../app/functions.php");
require("../app/dbconnect.php");
require("../app/upload.php");

session_start();

if(isset($_SESSION["id"])){
  $stmt = $pdo->prepare("SELECT * FROM members WHERE id=?");
  $stmt->execute([$_SESSION["id"]]);
  $member = $stmt->fetch();
}else{
  header("Location: login.php");
  exit();
}

// 投稿一覧
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created DESC, member_id LIMIT 10");
$posts = $stmt->fetchAll();
foreach($posts as $post){
  $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
  $stmt->execute([$post["member_id"]]);
  $result["member"] = $stmt->fetch();
  $stmt = $pdo->prepare("SELECT * FROM content WHERE post_id = ?");
  $stmt->execute([$post["id"]]);
  $result["content"] = $stmt->fetchAll();
  $result["post"] = $post;
  $list[] = $result;
}

// ランキング
$stmt = $pdo->query("SELECT * FROM posts ORDER BY likes DESC, member_id LIMIT 10");
$posts = $stmt->fetchAll();
foreach($posts as $post){
  $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
  $stmt->execute([$post["member_id"]]);
  $result["member"] = $stmt->fetch();
  $stmt = $pdo->prepare("SELECT * FROM content WHERE post_id = ?");
  $stmt->execute([$post["id"]]);
  $result["content"] = $stmt->fetchAll();
  $result["post"] = $post;
  $ranking[] = $result;
}
$counter = 0;

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="お気に入りの本をシェアするWebサイトです。">
  <title>未定</title>

  <!-- 自作css -->
  <link rel ="stylesheet" href="css/styles_main.css">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

  <!-- Google icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <!-- swiperjs -->
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.css">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
  <script src="https://unpkg.com/swiper/swiper-bundle.js"></script>
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
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
        <li><a href="logout.php">ログアウト</a></li>
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
<div class="top-img">
  <div class="cta-wrapper">
    <h1>
      ようこそ<?= h($member["name"]); ?>さん！<br>
      素敵な本に出合えますように。
    </h1>
    <div class="post-btn">
      <a href="posts.php">投稿する</a>
    </div>
    <p>あなたのおススメの本を紹介してください</p>
  </div>
</div>
<main>
  <div class="cta-wrapper">
    <h1>
      ようこそ<?= h($member["name"]); ?>さん！<br>
      素敵な本をご紹介ください。
    </h1>
    <div class="post-btn">
      <a href="posts.php">投稿する</a>
    </div>
    <p>あなたのおススメの本を紹介してください</p>
  </div>
  <div class="horizon"></div>
  <div class="wrapper-main list">
    <h1>みなさんの投稿</h1>
    <div class="swiper-container">
      <?php if(!empty($list)): ?>
        <ul class="swiper-wrapper">
          <?php foreach($list AS $post): ?>
            <li class="posts swiper-slide">
              <a href="view.php?id=<?= h($post["post"]["id"]); ?>">
                <div class="member-pro">
                  <?php if($post["member"]["picture"]): ?>
                    <?php if($s3Api): ?>
                      <img src="<?= h(get_mem($post["member"]["picture"])); ?>">
                    <?php else: ?>
                      <img src="member_img/<?= h($post["member"]["picture"]); ?>">
                    <?php endif ?>
                  <?php else: ?>
                    <img src="img/select_none.jpg">
                  <?php endif; ?>
                  <p><?= h($post["member"]["name"]); ?>さん</p>
                </div>
                <div class="post-list">
                  <div class="post">
                    <div class="post-img rank1">
                      <?php if($post["content"][0]["picture"]): ?>
                        <?php if($s3Api): ?>
                          <img src="<?= h(get_pos($post["content"][0]["picture"])); ?>">
                        <?php else: ?>
                          <img src="post_img/<?= h($post["content"][0]["picture"]); ?>">
                        <?php endif; ?>
                      <?php else: ?>
                        <img src="img/no-image.png">
                      <?php endif; ?>
                    </div>
                    <div class="content-desc">
                      <h3><?= h($post["content"][0]["title"]); ?></h3>
                      <h4><?= h($post["content"][0]["author"]); ?></h4>
                    </div>
                  </div>
                  <div class="post">
                    <div class="post-img rank2">
                      <?php if($post["content"][1]["picture"]): ?>
                        <?php if($s3Api): ?>
                          <img src="<?= h(get_pos($post["content"][1]["picture"])) ?>">
                        <?php else: ?>
                          <img src="post_img/<?= h($post["content"][1]["picture"]) ?>">
                        <?php endif; ?>
                      <?php else: ?>
                        <img src="img/no-image.png">
                      <?php endif; ?>
                    </div>
                    <div class="content-desc">
                      <h3><?= h($post["content"][1]["title"]); ?></h3>
                      <h4><?= h($post["content"][1]["author"]); ?></h4>
                    </div>
                  </div>
                  <div class="post">
                    <div class="post-img rank3">
                      <?php if($post["content"][2]["picture"]): ?>
                        <?php if($s3Api): ?>
                          <img src="<?= h(get_pos($post["content"][2]["picture"])) ?>">
                        <?php else: ?>
                          <img src="post_img/<?= h($post["content"][2]["picture"]) ?>">
                        <?php endif; ?>
                      <?php else: ?>
                        <img src="img/no-image.png">
                      <?php endif; ?>
                    </div>
                    <div class="content-desc">
                      <h3><?= h($post["content"][2]["title"]); ?></h3>
                      <h4><?= h($post["content"][2]["author"]); ?></h4>
                    </div>
                  </div>
                </div>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <div class="nothing">投稿がありません</div>
      <?php endif; ?>
      <div class="swiper-pagination"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>
    <a href="posts_list.php" class="more">
      もっと見る
      <span class="material-icons">
        keyboard_arrow_right
      </span>
    </a>
  </div>
  <div class="horizon"></div>
  <div class="wrapper-main ranklist">
    <h1>総合ランキング</h1>
    <div class="swiper-container ranking">
      <?php if(!empty($list)): ?>
        <ul class="swiper-wrapper">
          <?php foreach($ranking AS $post): ?>
            <li class="posts swiper-slide">
              <a href="view.php?id=<?= h($post["post"]["id"]); ?>">
                <?php $counter++; ?>
                <span class="square">#<?= $counter; ?></span>
                <span class="triangle"></span>
                <div class="member-pro">
                  <?php if($post["member"]["picture"]): ?>
                    <?php if($s3Api): ?>
                      <img src="<?= h(get_mem($post["member"]["picture"])); ?>">
                    <?php else: ?>
                      <img src="member_img/<?= h($post["member"]["picture"]); ?>">
                    <?php endif; ?>
                  <?php else: ?>
                    <img src="img/select_none.jpg">
                  <?php endif; ?>
                  <div class="main-detail">
                    <p><?= h($post["member"]["name"]); ?>さん</p>
                    <div class="heart-box">
                      <i class="material-icons heart">favorite</i>
                      <span><?= h($post["post"]["likes"]); ?></span>
                    </div>
                  </div>
                </div>
                <div class="post-list">
                  <div class="post">
                    <div class="post-img rank1">
                      <?php if($post["content"][0]["picture"]): ?>
                        <?php if($s3Api): ?>
                          <img src="<?= h(get_pos($post["content"][0]["picture"])); ?>">
                        <?php else: ?>
                          <img src="post_img/<?= h($post["content"][0]["picture"]) ?>">
                        <?php endif; ?>
                      <?php else: ?>
                        <img src="img/no-image.png">
                      <?php endif; ?>
                    </div>
                    <div class="content-desc">
                      <h3><?= h($post["content"][0]["title"]); ?></h3>
                      <h4><?= h($post["content"][0]["author"]); ?></h4>
                    </div>
                  </div>
                  <div class="post">
                    <div class="post-img rank2">
                      <?php if($post["content"][1]["picture"]): ?>
                        <?php if($s3Api): ?>
                          <img src="<?= h(get_pos($post["content"][1]["picture"])); ?>">
                        <?php else: ?>
                          <img src="post_img/<?= h($post["content"][1]["picture"]) ?>">
                        <?php endif; ?>
                      <?php else: ?>
                        <img src="img/no-image.png">
                      <?php endif; ?>
                    </div>
                    <div class="content-desc">
                      <h3><?= h($post["content"][1]["title"]); ?></h3>
                      <h4><?= h($post["content"][1]["author"]); ?></h4>
                    </div>
                  </div>
                  <div class="post">
                    <div class="post-img rank3">
                      <?php if($post["content"][2]["picture"]): ?>
                        <?php if($s3Api): ?>
                          <img src="<?= h(get_pos($post["content"][2]["picture"])); ?>">
                        <?php else: ?>
                          <img src="post_img/<?= h($post["content"][2]["picture"]) ?>">
                        <?php endif; ?>
                      <?php else: ?>
                        <img src="img/no-image.png">
                      <?php endif; ?>
                    </div>
                    <div class="content-desc">
                      <h3><?= h($post["content"][2]["title"]); ?></h3>
                      <h4><?= h($post["content"][2]["author"]); ?></h4>
                    </div>
                  </div>
                </div>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <div class="nothing">投稿がありません</div>
      <?php endif; ?>
      <div class="swiper-pagination"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>
    <a href="ranking.php" class="more">
      もっと見る
      <span class="material-icons">
        keyboard_arrow_right
      </span>
    </a>
  </div>
  <div class="horizon"></div>
  <div class="wrapper-main category">
    <h1>カテゴリーランキング</h1>
    <ul>
      <li><a href="ranking.php">ビジネス<span class="material-icons">keyboard_arrow_right</span></a></li>
      <li><a href="ranking.php?category=enlight">自己啓発<span class="material-icons">keyboard_arrow_right</span></a></li>
      <li><a href="ranking.php?category=lite">文芸<span class="material-icons">keyboard_arrow_right</span></a></li>
      <li><a href="ranking.php?category=plac">趣味・実用<span class="material-icons">keyboard_arrow_right</span></a></li>
      <li><a href="ranking.php?category=comic">漫画<span class="material-icons">keyboard_arrow_right</span></a></li>
    </ul>
  </div>
  <div class="top-link">
    <a href="#">
      トップへ戻る
      <span class="material-icons">
        keyboard_arrow_up
      </span>
    </a>
  </div>
</main>
<footer>
  <p>Copyright Kenta All right reserved.</p>
</footer>
<script src="js/main.js"></script>
</body>
</html>