<?php

require("/var/www/html/vendor/autoload.php");

// 開発環境
// require("secret.php");

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

//ON
$s3Api = "true";


$s3 = new S3Client([

  // 開発環境
  // "credentials" =>[
  //   "key" => $key,
  //   "secret" => $secret,
  // ],
  
  // 本番環境
  "credentials" =>[
    'key' => getenv('AWS_ACCESS_KEY_ID'),
    'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
  ],

  "version" => "latest",
  "region" => "us-east-2",
]);


//member-imgフォルダ アップロード
function put_mem($name, $tmp_name){
  global $s3;
  try{
    $s3->putObject([
      "ACL" => "public-read",
      "Bucket" => "kenta-my-portfolio",
      "Key" => "member-img/" . $name,
      "SourceFile" => $tmp_name,
    ]);
  }catch(S3Exeption $e){
    echo $e->getMessage();
  }
}

//post-imgフォルダ アップロード
function put_pos($name, $tmp_name){
  global $s3;
  try{
    $s3->putObject([
      "ACL" => "public-read",
      "Bucket" => "kenta-my-portfolio",
      "Key" => "post-img/" . $name,
      "SourceFile" => $tmp_name,
    ]);
  }catch(S3Exeption $e){
    echo $e->getMessage();
  }
}

//library-imgフォルダ アップロード
function put_lib($name, $tmp_name){
  global $s3;
  try{
    $s3->putObject([
      "ACL" => "public-read",
      "Bucket" => "kenta-my-portfolio",
      "Key" => "library-img/" . $name,
      "SourceFile" => $tmp_name,
    ]);
  }catch(S3Exeption $e){
    echo $e->getMessage();
  }
}


//member-imgフォルダ ダウンロード
function get_mem($name){
  global $s3;
  return $s3->getObjectUrl("kenta-my-portfolio", "member-img/" . $name);
}

//post-imgフォルダ ダウンロード
function get_pos($name){
  global $s3;
  return $s3->getObjectUrl("kenta-my-portfolio", "post-img/" . $name);
}

//library-imgフォルダ ダウンロード
function get_lib($name){
  global $s3;
  return $s3->getObjectUrl("kenta-my-portfolio", "library-img/" . $name);
}


//member-imgフォルダ デリート
function del_mem($name){
  global $s3;
  $s3->deleteObject([
    "Bucket" => "kenta-my-portfolio",
    "Key" => "member-img/" . $name
  ]);
}

//post-imgフォルダ デリート
function del_pos($name){
  global $s3;
  $s3->deleteObject([
    "Bucket" => "kenta-my-portfolio",
    "Key" => "post-img/" . $name
  ]);
}

//library-imgフォルダ デリート
function del_lib($name){
  global $s3;
  $s3->deleteObject([
    "Bucket" => "kenta-my-portfolio",
    "Key" => "library-img/" . $name
  ]);
}

// from post-imgフォルダ to library-imgフォルダ コピー
function cop_pos($name, $src){
  global $s3;
  $s3->copyObject([
    "ACL" => "public-read",
    "Bucket" => "kenta-my-portfolio",
    "Key" => "library-img/" . $name,
    "CopySource" => "kenta-my-portfolio/post-img/" . $src,
  ]);
}
