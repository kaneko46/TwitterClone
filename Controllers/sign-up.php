<?php

// サインアップコントローラー

// 設定読み込み
include_once '../config.php';
// 便利な関数を読み込む
include_once '../util.php';
// ユーザーデータ操作モデル読み込み
include_once '../Models/users.php';

// 登録項目がすべて入力されていれば
if(isset($_POST['nickname']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])){
    $data = [
        'nickname' => $_POST['nickname'],
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
    ];

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    //接続エラーがある場合 -> 処理停止
    if ($mysqli->connect_errno){
        echo 'MySQLの接続に失敗しました。:'.$mysqli -> connect_error."\n";
        exit;
    }
    $check = $_POST['email'];
    // 完成済みのSELECT文を実行する
    $sql = "SELECT * FROM users WHERE email='{$check}'";
    $judge=NULL;
    if ($result = $mysqli->query($sql)) {
    // 連想配列を取得
    while ($row = $result->fetch_assoc()) {
        $judge = $row["email"];
    }
    // 結果セットを閉じる
    $result->close();
    if(isset($judge)){
        echo "重複したemailがあります。";
        exit;
    }
}


    


    // ユーザーを作成し、成功すれば
    if(createUser($data)){
        // ログイン画面に遷移
        header('Location:' . HOME_URL . 'Controllers/sign-in.php');
        exit;
    }
}

// 画面表示
include_once '../Views/sign-up.php';
