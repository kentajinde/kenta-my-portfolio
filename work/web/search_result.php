<?php
  session_start();
  require("../app/functions.php");
  require("../app/dbconnect.php");
  require("../app/upload.php");

  shutOut();

  $title = $_SESSION["word"];
  $word = "%" . $_SESSION["word"] . "%";

  if($word){
    $stmt = $pdo->prepare(
      "SELECT *
      FROM content
      WHERE title LIKE ? OR author LIKE ?
    ");
    $stmt->execute([$word, $word]);
    $contents = $stmt->fetchAll();
    foreach($contents as $content){
      $post_ids[] = $content["post_id"];
    };
    if($post_ids){
      array_unique($post_ids);
      foreach($post_ids as $post_id){
        $stmt = $pdo->prepare(
          "SELECT *
          FROM posts
          WHERE id = ?
          ORDER BY created DESC, member_id
          ");
        $stmt->execute([$post_id]);
        $posts = $stmt->fetchAll();
        foreach($posts as $post){
          $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
          $stmt->execute([$post["member_id"]]);
          $result["member"] = $stmt->fetch();
          $stmt = $pdo->prepare("SELECT * FROM content WHERE post_id = ?");
          $stmt->execute([$post["id"]]);
          $result["content"] = $stmt->fetchAll();
          $result["post"] = $post;
          $results[] = $result;
        }
      }
    }
  }
?>

<?php
  $css = "styles_search_result.css";
  require("header.php");
?>
<main>
  <?php if(!empty($results)): ?>
    <div class="result-title">
      <h3>"<?= $title; ?>" の検索結果</h3>
    </div>
    <ul>
      <?php foreach($results as $post): ?>
        <li class="posts">
          <a href="view.php?id=<?= h($post["post"]["id"]); ?>">
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
              <?php foreach($post["content"] as $index => $content): ?>
                <div class="post-wrapper">
                  <div class="post">
                    <div class="post-img rank<?= $index + 1; ?>">
                      <?php if($content["picture"]): ?>
                        <?php if($s3Api): ?>
                          <img src="<?= h(get_pos($content["picture"])); ?>">
                        <?php else: ?>
                          <img src="post_img/<?= h($content["picture"]); ?>">
                        <?php endif; ?>
                      <?php else: ?>
                        <img src="img/no-image.png">
                      <?php endif; ?>
                    </div>
                    <div class="content-desc">
                      <h3><?= h($content["title"]); ?></h3>
                      <h4><?= h($content["author"]); ?></h4>
                    </div>
                  </div>
                  <div class="description">
                    <p>
                      <?= h($content["description"]); ?>
                    </p>
                  </div>
                </div>
              <?php endforeach; ?>
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
    <div class="nothing">
      "<?= $title; ?>" の検索結果はございませんでした
    </div>
  <?php endif; ?>
</main>
<?php
  require("footer.php");
?>