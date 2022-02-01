<?php

// ホームコントローラー

// 設定を読み込み
include_once '../config.php';
// 便利な関数読み込み
include_once '../util.php';

// ツイートデータ操作モデルを読み込む
include_once '../Models/tweets.php';

// ログインチェック
$user = getUserSession();
// var_dump($user);
// exit;
if(!$user){
// var_dump($user);
// exit;
    // ログインしていない
    header('Location: ' . HOME_URL . 'Controllers/sign-in.php');
    exit;
}

// 表示用の変数
$view_user = $user;
// ツイートと一覧
$view_tweets = findTweets($user);

// 画面表示
include_once '../Views/home.php';
