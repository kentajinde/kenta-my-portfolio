<?php
session_start();
require("../app/functions.php");
require("../app/dbconnect.php");
require("../app/upload.php");

shutOut();

// メンバー情報、投稿情報、投稿内容の配列
$ranking = "true";
require("posts_info.php");
?>

<!-- header -->
<?php
$css = "styles_ranking.css";
$swiper = "false";
require("header.php");
?>

  <main>
    <div class="tab">
      <ul>
        <li id="busi-item" data-id="busi">ビジネス</li>
        <li data-id="enlight">自己啓発</li>
        <li data-id="lite">文芸</li>
        <li data-id="plac">趣味・実用</li>
        <li data-id="comic">漫画</li>
      </ul>
      <?php require("list_component.php"); ?>
    </div>
  </main>
<?php
  $js = "ranking.js";
  require("footer.php");
?>