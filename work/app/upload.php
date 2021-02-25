<?php
require '/var/www/html/vendor/autoload.php';
// require 'secret.php';

// use Aws\S3\S3Client;
// use Aws\Exception\AwsException;


// $s3 = new S3Client([

//   開発環境
//   "credentials" =>[
//     "key" => $key,
//     "secret" => $secret,
//   ],

//   "credentials" =>[
//     'key' => getenv('AWS_ACCESS_KEY_ID'),
//     'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
//   ],
//   "version" => "latest",
//   "region" => "us-east-2",
// ]);

// if($_FILES){
//   $image = bin2hex(random_bytes(32)) . $_FILES["image"]["name"];
//   $fp = fopen($_FILES["image"]["tmp_name"], "rb");
// }

// try{
//   $result = $s3->putObject([
//     "Bucket" => "kenta-my-portfolio",
//     "Key" => "member-img/" . $image,
//     "body" => $fp,
//   ]);
// }catch(S3Exeption $e){
//   echo $e->getMessage();
// }

?>

<pre>
  <?= $secret; ?>
</pre>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>test</title>
</head>
<body>
  <!-- <img src="<?= $result["ObjectURL"]; ?>"> -->
  <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action">
    <input type="file" size="35" accept="image/*" name="image">
    <input type="submit" value="送信">
  </form>
</body>
</html>