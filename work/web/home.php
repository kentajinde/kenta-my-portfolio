<?php

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="お気に入りの本を紹介し、シェアするWebサイトです。">
  <title>Best Book</title>

  <!-- 自作css -->
  <link rel ="stylesheet" href="css/styles_home.css">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" zintegrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
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
        <div class="cta1">
          <h1>Best Book</h1>
          <a href="signup.php">
            <button class="btn-white">新規登録</button>
          </a>
          <a href="login.php">
            <button class="btn-white">ログイン</button>
          </a>
        </div>
      </div>
    </div>
    <div class="wrapper">
      <div class="desc">
        <h1>Best Bookは、自分のお気に入りの本を紹介し、共有するサービスです。</h1>
      </div>
      <div class="features">
        <div class="feature-box fade-in2">
          <img class="fade-in fade-in-up" src="img/features_1.jpg" alt="">
          <div class="feature-item fade-in fade-in-left">
            <h2>新たな本と出合うきっかけに</h2>
            <p>今まで読んだ本の中で「面白かった」「役に立った」「勉強になった」と思える本があったかと思います。ここは、そんな自分のお気に入りの本を皆さんで共有する場です。あなたがまだ知らない素敵な本も、見つかるかもしれません。</p>
          </div>
        </div>
        <div class="feature-box fade-in2">
          <img class="fade-in fade-in-up" src="img/features_2.jpeg" alt="">
          <div class="feature-item fade-in fade-in-left">
            <h2>ベスト３を紹介してください</h2>
            <p>本のカテゴリーごとに、あなたの中のベスト3の本を紹介できます。その本をなぜ推すのか、どこが魅力なのか、ぜひあなたの言葉でプレゼンしてみてください！</p>
          </div>
        </div>
      </div>
      <div class="howto">
        <h1>Best Bookの特徴</h1>
        <ul>
          <li>本のカテゴリーごとに、マイベスト3を投稿できる</li>
          <li>気に入った投稿に<span>❤</span>(like)を送れる</li>
          <li><span>❤</span>(like)の数によって、ランキングで表示される</li>
          <li>ユーザーが投稿した本を、ライブラリに登録できる</li>
        </ul>
      </div>
      <div class="cta2">
        <div class="cta2-mask">
          <div class="begin">
            <h1>はじめてみよう！</h1>
            <a href="signup.php"><button class="btn-white">新規登録</button></a>
          </div>
        </div>
      </div>
    </div>
  </main>
  <footer>
    <p>© sample sample</p>
  </footer>

  <script src="js/home.js"></script>

</body>
</html>