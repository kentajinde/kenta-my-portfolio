<?php
require '/var/www/html/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;


$s3 = new S3Client([
  "credentials" => [
    "key" => "AKIAWF3FAJZDEJ7YWLGX",
    "secret" => "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",
  ],
  "version" => "latest",
  "region" => "us-east-2",
]);

$image = bin2hex(random_bytes(32)) . $_FILES["image"]["name"];
$fp = fopen($_FILES["image"]["tmp_name"], "rb");

try{
  $result = $s3->putObject([
    "Bucket" => "kenta-my-portfolio",
    "Key" => "member-img/" . $image,
    "body" => $fp,
  ]);
}catch(S3Exeption $e){
  echo $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>test</title>
  <link rel="stylesheet" href="css/styles_test.css" />
</head>
<body>
  <img src="<?= $result["ObjectURL"]; ?>">
  <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action">
    <input type="file" size="35" accept="image/*" name="image">
    <input type="submit" value="送信">
  </form>
</body>
</html>