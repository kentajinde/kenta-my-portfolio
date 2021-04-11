<?php

$categorys = [
  "busi" => $busi,
  "enlight" => $enlight,
  "lite" => $lite,
  "plac" => $plac,
  "comic" => $comic
];

?>

<?php foreach($categorys as $key => $category): ?>
  <div class="content" id="<?= $key; ?>">
    <?php if(!empty($category)): ?>
      <ul>
        <?php foreach($category as $index => $post): ?>
          <li class="posts">
            <a href="view.php?id=<?= h($post["post"]["id"]); ?>">
              <span class="square">
                #<?= $index + 1; ?>
              </span>
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
      <div class="nothing">投稿がありません</div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
