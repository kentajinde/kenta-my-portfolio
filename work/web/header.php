<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="お気に入りの本をシェアするWebサイトです。">
  <title>Best Book</title>

  <!-- 自作css -->
  <link rel ="stylesheet" href=<?= "css/{$css}" ?>>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

  <!-- Google icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <!-- swiperjs -->
  <?php if($swiper): ?>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
  <?php endif; ?>
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
        <li><a href="search.php">検索</a></li>
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
      <li class="nav-item"><a class="nav-link" href="search.php">検索</a></li>
      <li class="nav-item"><a class="nav-link" href="logout.php">ログアウト</a></li>
    </ul>
  </nav>
</header>