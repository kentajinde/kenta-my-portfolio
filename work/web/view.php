<?php
session_start();
require("../app/functions.php");
require("../app/dbconnect.php");
require("../app/upload.php");

shutOut();


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

<!-- header -->
<?php
$css = "styles_view.css";
$swiper = "false";

require("header.php");
?>

  <main>
    <?php if(!empty($successed)): ?>
      <div class="successed">ライブラリに登録しました</div>
    <?php endif; ?>
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
    <div class="myp-wrapper">
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
        <div class="rank content active" id="one">
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
      </div>
    </div>
  </main>
  <footer>
    <p>Copyright Kenta All right reserved.</p>
  </footer>
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