<?php

require("../app/functions.php");
require("../app/dbconnect.php");
require("../app/upload.php");

session_start();

// メンバー情報、投稿情報、投稿内容(連想多次元配列)

// ビジネス
$stmt = $pdo->query("SELECT * FROM posts WHERE category = 'busi' ORDER BY likes DESC, member_id");
$posts = $stmt->fetchAll();
foreach($posts as $post){
  $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
  $stmt->execute([$post["member_id"]]);
  $result["member"] = $stmt->fetch();
  $stmt = $pdo->prepare("SELECT * FROM content WHERE post_id = ?");
  $stmt->execute([$post["id"]]);
  $result["content"] = $stmt->fetchAll();
  $result["post"] = $post;
  $busi[] = $result;
}
// 自己啓発
$stmt = $pdo->query("SELECT * FROM posts WHERE category = 'enlight' ORDER BY likes DESC, member_id");
$posts = $stmt->fetchAll();
foreach($posts as $post){
  $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
  $stmt->execute([$post["member_id"]]);
  $result["member"] = $stmt->fetch();
  $stmt = $pdo->prepare("SELECT * FROM content WHERE post_id = ?");
  $stmt->execute([$post["id"]]);
  $result["content"] = $stmt->fetchAll();
  $result["post"] = $post;
  $enlight[] = $result;
}
// 文芸
$stmt = $pdo->query("SELECT * FROM posts WHERE category = 'lite' ORDER BY likes DESC, member_id");
$posts = $stmt->fetchAll();
foreach($posts as $post){
  $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
  $stmt->execute([$post["member_id"]]);
  $result["member"] = $stmt->fetch();
  $stmt = $pdo->prepare("SELECT * FROM content WHERE post_id = ?");
  $stmt->execute([$post["id"]]);
  $result["content"] = $stmt->fetchAll();
  $result["post"] = $post;
  $lite[] = $result;
}
// 趣味・実用
$stmt = $pdo->query("SELECT * FROM posts WHERE category = 'plac' ORDER BY likes DESC, member_id");
$posts = $stmt->fetchAll();
foreach($posts as $post){
  $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
  $stmt->execute([$post["member_id"]]);
  $result["member"] = $stmt->fetch();
  $stmt = $pdo->prepare("SELECT * FROM content WHERE post_id = ?");
  $stmt->execute([$post["id"]]);
  $result["content"] = $stmt->fetchAll();
  $result["post"] = $post;
  $plac[] = $result;
}
// 漫画
$stmt = $pdo->query("SELECT * FROM posts WHERE category = 'comic' ORDER BY likes DESC, member_id");
$posts = $stmt->fetchAll();
foreach($posts as $post){
  $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
  $stmt->execute([$post["member_id"]]);
  $result["member"] = $stmt->fetch();
  $stmt = $pdo->prepare("SELECT * FROM content WHERE post_id = ?");
  $stmt->execute([$post["id"]]);
  $result["content"] = $stmt->fetchAll();
  $result["post"] = $post;
  $comic[] = $result;
}

$counterBusi = 0;
$counterEnlight = 0;
$counterLite = 0;
$counterPlac = 0;
$counterComic = 0;

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="お気に入りの本をシェアするWebサイトです。">
  <title>未定</title>

  <!-- 自作css -->
  <link rel ="stylesheet" href="css/styles_ranking.css">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

  <!-- Google icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

  <!-- <pre><?= var_dump($busi[0]); ?></pre> -->

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
    <div class="tab">
      <ul>
        <li data-id="busi" class="active">ビジネス</li>
        <li data-id="enlight">自己啓発</li>
        <li data-id="lite">文芸</li>
        <li data-id="plac">趣味・実用</li>
        <li data-id="comic">漫画</li>
      </ul>
      <div class="content active" id="busi">
        <?php if(!empty($busi)): ?>
          <ul>
            <?php foreach($busi AS $post): ?>
              <li class="posts">
                <a href="view.php?id=<?= h($post["post"]["id"]); ?>">
                  <?php $counterBusi++; ?>
                  <span class="square">#<?= $counterBusi; ?></span>
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
                    <div class="detail">
                      <p><?= h($post["member"]["name"]); ?>さん</p>
                      <div class="heart-box">
                        <i class="material-icons heart">favorite</i>
                        <span><?= h($post["post"]["likes"]); ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="post-list">
                    <div class="post-wrapper">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][0]["description"]); ?>
                        </p>
                      </div>
                    </div>
                    <div class="post-wrapper">
                      <div class="post">
                        <div class="post-img rank2">
                          <?php if($post["content"][1]["picture"]): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($post["content"][1]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= h($post["content"][1]["picture"]); ?>">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][1]["description"]); ?>
                        </p>
                      </div>
                    </div>
                    <div class="post-wrapper">
                      <div class="post">
                        <div class="post-img rank3">
                          <?php if($post["content"][2]["picture"]): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($post["content"][2]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= h($post["content"][2]["picture"]); ?>">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][2]["description"]); ?>
                        </p>
                      </div>
                    </div>
                  </div>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
          <div class="top-link">
            <a href="#">
              トップへ戻る
              <span class="material-icons">
                keyboard_arrow_up
              </span>
            </a>
          </div>
        <?php else: ?>
          <div class="nothing">投稿がありません</div>
        <?php endif; ?>
      </div>
      <div class="content" id="enlight">
        <?php if(!empty($enlight)): ?>
          <ul>
            <?php foreach($enlight AS $post): ?>
              <li class="posts">
                <a href="view.php?id=<?= h($post["post"]["id"]); ?>">
                  <?php $counterEnlight++; ?>
                  <span class="square">#<?= $counterEnlight; ?></span>
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
                    <div class="detail">
                      <p><?= h($post["member"]["name"]); ?>さん</p>
                      <div class="heart-box">
                        <i class="material-icons heart">favorite</i>
                        <span><?= h($post["post"]["likes"]); ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="post-list">
                    <div class="post-wrapper">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][0]["description"]); ?>
                        </p>
                      </div>
                    </div>
                    <div class="post-wrapper">
                      <div class="post">
                        <div class="post-img rank2">
                          <?php if($post["content"][1]["picture"]): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($post["content"][1]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= h($post["content"][1]["picture"]); ?>">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][1]["description"]); ?>
                        </p>
                      </div>
                    </div>
                    <div class="post-wrapper">
                      <div class="post">
                        <div class="post-img rank3">
                          <?php if($post["content"][2]["picture"]): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($post["content"][2]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= h($post["content"][2]["picture"]); ?>">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][2]["description"]); ?>
                        </p>
                      </div>
                    </div>
                  </div>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
          <div class="top-link">
            <a href="#">
              トップへ戻る
              <span class="material-icons">
                keyboard_arrow_up
              </span>
            </a>
          </div>
        <?php else: ?>
          <div class="nothing">投稿がありません</div>
        <?php endif; ?>
      </div>
      <div class="content" id="lite">
        <?php if(!empty($lite)): ?>
          <ul>
            <?php foreach($lite AS $post): ?>
              <li class="posts">
                <a href="view.php?id=<?= h($post["post"]["id"]); ?>">
                  <?php $counterLite++; ?>
                  <span class="square">#<?= $counterLite; ?></span>
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
                    <div class="detail">
                      <p><?= h($post["member"]["name"]); ?>さん</p>
                      <div class="heart-box">
                        <i class="material-icons heart">favorite</i>
                        <span><?= h($post["post"]["likes"]); ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="post-list">
                    <div class="post-wrapper">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][0]["description"]); ?>
                        </p>
                      </div>
                    </div>
                    <div class="post-wrapper">
                      <div class="post">
                        <div class="post-img rank2">
                          <?php if($post["content"][1]["picture"]): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($post["content"][1]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= h($post["content"][1]["picture"]); ?>">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][1]["description"]); ?>
                        </p>
                      </div>
                    </div>
                    <div class="post-wrapper">
                      <div class="post">
                        <div class="post-img rank3">
                          <?php if($post["content"][2]["picture"]): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($post["content"][2]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= h($post["content"][2]["picture"]); ?>">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][2]["description"]); ?>
                        </p>
                      </div>
                    </div>
                  </div>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
          <div class="top-link">
            <a href="#">
              トップへ戻る
              <span class="material-icons">
                keyboard_arrow_up
              </span>
            </a>
          </div>
        <?php else: ?>
          <div class="nothing">投稿がありません</div>
        <?php endif; ?>
      </div>
      <div class="content" id="plac">
        <?php if(!empty($plac)): ?>
          <ul>
            <?php foreach($plac AS $post): ?>
              <li class="posts">
                <a href="view.php?id=<?= h($post["post"]["id"]); ?>">
                  <?php $counterPlac++; ?>
                  <span class="square">#<?= $counterPlac; ?></span>
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
                    <div class="detail">
                      <p><?= h($post["member"]["name"]); ?>さん</p>
                      <div class="heart-box">
                        <i class="material-icons heart">favorite</i>
                        <span><?= h($post["post"]["likes"]); ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="post-list">
                    <div class="post-wrapper">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][0]["description"]); ?>
                        </p>
                      </div>
                    </div>
                    <div class="post-wrapper">
                      <div class="post">
                        <div class="post-img rank2">
                          <?php if($post["content"][1]["picture"]): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($post["content"][1]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= h($post["content"][1]["picture"]); ?>">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][1]["description"]); ?>
                        </p>
                      </div>
                    </div>
                    <div class="post-wrapper">
                      <div class="post">
                        <div class="post-img rank3">
                          <?php if($post["content"][2]["picture"]): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($post["content"][2]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= h($post["content"][2]["picture"]); ?>">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][2]["description"]); ?>
                        </p>
                      </div>
                    </div>
                  </div>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
          <div class="top-link">
            <a href="#">
              トップへ戻る
              <span class="material-icons">
                keyboard_arrow_up
              </span>
            </a>
          </div>
        <?php else: ?>
          <div class="nothing">投稿がありません</div>
        <?php endif; ?>
      </div>
      <div class="content" id="comic">
        <?php if(!empty($comic)): ?>
          <ul>
            <?php foreach($comic AS $post): ?>
              <li class="posts">
                <a href="view.php?id=<?= h($post["post"]["id"]); ?>">
                  <?php $counterComic++; ?>
                  <span class="square">#<?= $counterComic; ?></span>
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
                    <div class="detail">
                      <p><?= h($post["member"]["name"]); ?>さん</p>
                      <div class="heart-box">
                        <i class="material-icons heart">favorite</i>
                        <span><?= h($post["post"]["likes"]); ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="post-list">
                    <div class="post-wrapper">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][0]["description"]); ?>
                        </p>
                      </div>
                    </div>
                    <div class="post-wrapper">
                      <div class="post">
                        <div class="post-img rank2">
                          <?php if($post["content"][1]["picture"]): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($post["content"][1]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= h($post["content"][1]["picture"]); ?>">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][1]["description"]); ?>
                        </p>
                      </div>
                    </div>
                    <div class="post-wrapper">
                      <div class="post">
                        <div class="post-img rank3">
                          <?php if($post["content"][2]["picture"]): ?>
                            <?php if($s3Api): ?>
                              <img src="<?= h(get_pos($post["content"][2]["picture"])); ?>">
                            <?php else: ?>
                              <img src="post_img/<?= h($post["content"][2]["picture"]); ?>">
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
                      <div class="description">
                        <p>
                          <?= h($post["content"][2]["description"]); ?>
                        </p>
                      </div>
                    </div>
                  </div>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
          <div class="top-link">
            <a href="#">
              トップへ戻る
              <span class="material-icons">
                keyboard_arrow_up
              </span>
            </a>
          </div>
        <?php else: ?>
          <div class="nothing">投稿がありません</div>
        <?php endif; ?>
      </div>
    </div>
  </main>
  <footer>
  <p>Copyright Kenta All right reserved.</p>
  </footer>
  <script src="js/view.js"></script>
</body>