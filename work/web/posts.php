<?php
session_start();
require("../app/functions.php");
require("../app/dbconnect.php");
require("../app/upload.php");

shutOut();

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
$categorys = ["busi", "enlight", "lite", "plac", "comic"];

$stmt = $pdo->prepare("SELECT * FROM posts WHERE member_id = ?");
$stmt->execute([$member["id"]]);
$all = $stmt->fetchAll();

foreach($categorys as $category){
  $stmt = $pdo->prepare(
    "SELECT *
    FROM (SELECT * FROM posts WHERE member_id = ? AND category = ?) AS posts
    JOIN content ON posts.id = content.post_id
    ");
  $stmt->execute([$_SESSION["id"], $category]);
  $$category = $stmt->fetchAll();
};


// ライブラリ取得
foreach($categorys as $category){
  $stmt = $pdo->prepare(
    "SELECT *, (SELECT COUNT(id) FROM library WHERE member_id = :id AND category = :category) as cnt
    FROM library
    WHERE member_id = :id AND category = :category
    ORDER BY created DESC, member_id
    ");
  $stmt->execute(["id"=>$_SESSION["id"], "category"=>$category]);
  $lib = "lib_" . $category;
  $$lib = $stmt->fetchAll();
}
?>

<!-- header -->
<?php
$css = "styles_posts.css";
$swiper = "false";

require("header.php");
?>

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
          <?php
            $forms = [
              ["１", true, 0, 1],
              ["２", false, 1, 2],
              ["３", false, 2, 3],
            ];
            foreach($forms as $form): 
          ?>
            <div class="post-rank">
              <?php if($form[1]): ?>
                <p><?= $form[0]; ?>位<strong>（必須）</strong></p>
              <?php else: ?>
                <p><?= $form[0]; ?>位（任意）</p>
              <?php endif ?>
              <input type="text" name="content[<?= $form[2]; ?>][title]" placeholder="本のタイトル" value="<?= blk(h($_POST["content"][$form[2]]["title"])); ?>">
              <input type="text" name="content[<?= $form[2]; ?>][author]"  placeholder="著者" value="<?= blk(h($_POST["content"][$form[2]]["author"])); ?>">
              <textarea  rows=5 cols=60 name="content[<?= $form[2]; ?>][desc]" placeholder="この本について、自由に紹介してください"><?= blk(h($_POST["content"][$form[2]]["desc"])); ?></textarea>
              <?php if($error["post" . $form[3]] === "blank"): ?>
                <p class="error">*タイトルを入力してください</p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
          <label class="edit-btn">
            内容を確認する（画像の選択）
            <input type="submit">
          </label>
        </form>
      </div>
  

      <!-- 自分の投稿 -->
      <div class="content" id="posts">
        <ul>
          <?php
            $my_posts = [
              ["busi", "ビジネス", $busi],
              ["enlight", "自己啓発", $enlight],
              ["lite", "文芸", $lite],
              ["plac", "趣味・実用", $plac],
              ["comic", "漫画", $comic],
            ];
            foreach($my_posts as $my_post):
          ?>
            <li>
              <label for="<?= $my_post[0]; ?>-post" class="posts-label" data-id="<?= $my_post[0]; ?>-span">
                <?= $my_post[1]; ?>
                <?php if(!empty($my_post[2])): ?>
                  <div class="likes">
                    <i class="material-icons">favorite</i>
                    <span><?= h($my_post[2][0]["likes"]); ?></span>
                  </div>
                <?php endif; ?>
                <span class="material-icons" id="<?= $my_post[0]; ?>-span">
                  keyboard_arrow_down
                </span>
              </label>
              <input type="checkbox" id="<?= $my_post[0]; ?>-post" class="accordion">
              <?php if(!empty($my_post[2])): ?>
                <div class="<?= $my_post[0]; ?>-post post-content">
                  <?php foreach($my_post[2] as $index => $post): ?>
                    <?php if($post): ?>
                      <div class="rank">
                        <h2><?= $index + 1; ?>位</h2>
                        <div class="post-fb">
                          <div class="post-fi image">
                            <div id="rank<?= $index + 1; ?>">
                              <?php if(isset($post["picture"])): ?>
                                <?php if($s3Api): ?>
                                  <img src="<?= h(get_pos($post["picture"])); ?>">
                                <?php else: ?>
                                  <img src="post_img/<?= $post["picture"]; ?>">
                                <?php endif; ?>
                              <?php else: ?>
                                <img src="img/no-image.png">
                              <?php endif; ?>
                            </div>
                          </div>
                          <div class="post-fi title">
                            <div>
                              <h3><?= h($post["title"]); ?></h3>
                              <p><?= h($post["author"]); ?></p>
                            </div>
                          </div>
                        </div>
                        <div class="desc-wrapper">
                          <h3>説明</h3>
                          <div class="desc">
                            <p><?= h($post["description"]); ?></p>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                  <?php endforeach; ?>
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
          <?php endforeach; ?>
        </ul>
      </div>
  
  
      <!-- ライブラリ -->
      <div class="content" id="library">
        <ul>
          <?php
            $librarys = [
              ["busi", "ビジネス", $lib_busi],
              ["enlight", "自己啓発", $lib_enlight],
              ["lite", "文芸", $lib_lite],
              ["plac", "趣味・実用", $lib_plac],
              ["comic", "漫画", $lib_comic],
            ];
            foreach($librarys as $library):
          ?>
            <li>
              <label for="lib-<?= $library[0]; ?>" class="posts-label" data-id="lib_<?= $library[0]; ?>-span">
              <?= $library[1]; ?>
                <span>
                  <?php if($library[2][0]["cnt"] > 0): ?>
                    (<?= $library[2][0]["cnt"]; ?>)
                  <?php else: ?>
                    (0)
                  <?php endif; ?>
                </span>
                <span class="material-icons" id="lib_<?= $library[0]; ?>-span">
                  keyboard_arrow_down
                </span>
              </label>
              <input type="checkbox" id="lib-<?= $library[0]; ?>" class="accordion">
              <?php if(!empty($library[2])): ?>
                <div class="lib-<?= $library[0]; ?>">
                  <?php foreach($library[2] as $post): ?>
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
                          <div>
                            <h3><?= h($post["title"]); ?></h3>
                            <p><?= h($post["author"]); ?></p>
                            <a href="delete.php?lib_id=<?= h($post["id"]); ?>" class="lib-del">
                              登録を解除する
                            </a>
                          </div>
                        </div>
                      </div>
                      <div class="desc-wrapper">
                        <h3>説明</h3>
                        <div class="desc">
                          <p><?= h($post["description"]); ?></p>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="post-nothing">登録はありません<div>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
  
    </div>
  </div>
</main>
<?php
  $js = "posts.js";
  require("footer.php");
?>