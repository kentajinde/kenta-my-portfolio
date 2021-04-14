<?php
  session_start();
  require("../app/functions.php");

  shutOut();

  if($_POST["word"]){
    $_SESSION["word"] = $_POST["word"];
    header("Location: search_result.php");
    exit();
  }
?>

<?php
  $css = "styles_search.css";
  require("header.php");
?>
<main>
  <div class="search-wrapper">
    <div class="under-line">
      <h2>本の検索</h2>
    </div>
    <form method="post" action="">
      <input type="text" placeholder="作品名、著者名で検索してください..." name="word" id="search">
      <input type="submit" value="検索" disabled id="button">
    </form>
  </div>
</main>
<?php
  $js = "search.js";
  require("footer.php");
?>