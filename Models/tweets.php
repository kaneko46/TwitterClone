<?php

/**
 * ツイート作成
 * @param array $data
 * @return bool
 */

function createTweet(array $data)
{
    // DB接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    //接続エラーがある場合 -> 処理停止
    if ($mysqli->connect_errno){
        echo 'MySQLの接続に失敗しました。:'.$mysqli -> connect_error."\n";
        exit;
    }

    //新規登録のSQLクエリを作成
    $query = 'INSERT INTO tweets(user_id, body, image_name) VALUES(?,?,?)';

    // プリペアドステートメントに、作成したクエリを登録
    $statement = $mysqli->prepare($query);
    
    // プレスホルダにカラム値を紐つけ (i=int,s=string)
    $statement->bind_param('iss',$data['user_id'],$data['body'],$data['image_name']);

    // クエリを実行
    $response = $statement->execute();

    // 実行に失敗した場合 ->エラー表示
    if ($response === false){
        echo 'エラーメッセージ:' .$mysquli->error."\n";
    }

    // DB接続を解放
    $statement->close();
    $mysqli->close();

    return $response;
}

/**
 * ツイート一覧取得
 * 
 * @param array $user ログインしているユーザー取得
 * @param string $keyword 検索キーワード
 * @return array|false
 */

function findTweets(array $user, string $keyword = null)
{
    // DB接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    //接続エラーがある場合 -> 処理停止
    if ($mysqli->connect_errno){
        echo 'MySQLの接続に失敗しました。:'.$mysqli -> connect_error."\n";
        exit;
    }

    // ログインユーザーIDをエスケープ
    $login_user_id = $mysqli->real_escape_string($user['id']);

    // 検索のSQLエリを作成
    $query = <<<SQL
        SELECT
            T.id AS tweet_id,
            T.status AS tweet_status,
            T.body AS tweet_body,
            T.image_name AS tweet_image_name,
            T.created_at AS tweet_created_at,
            U.id AS user_id,
            U.name AS user_name,
            U.nickname AS user_nickname,
            U.image_name AS user_image_name,
            -- ログインユーザーがいいね！したか（いいね！している場合、値が入る）
            L.id AS like_id,
            -- いいね！数
            (SELECT COUNT(*) FROM likes WHERE status = 'active' AND tweet_id = T.id) AS like_count
        FROM
            tweets AS T
            -- ユーザーテーブルをusers.idとtweets.user_idで紐つける
            JOIN
            users AS U ON U.id = T.user_id AND U.status = 'active'
            -- いいね！テーブルをlikes.tweet_idとtweets.ideで紐つける
            LEFT JOIN
            likes AS L ON L.tweet_id = T.id AND L.status = 'active' AND L.user_id = '$login_user_id'
        WHERE
            T.status = 'active'
    SQL;

    // 検索キーワードが入力されていた場合
    if (isset($keyword)){
        // エスケープ
        $keyword = $mysqli->real_escape_string($keyword);
        // ツイート主のニックネーム・ユーザー名・本文からの部分一致検索
        $query  .= ' AND CONCAT(U.nickname, U.name, T.body) LIKE "%' . $keyword . '%"';
    }

    // 新しい順に並び替え
    $query .= ' ORDER BY T.created_at DESC';
    // 表示件数50件
    $query .= ' LIMIT 50';


    // クエリ実行
    $result = $mysqli->query($query);
    if ($result){
        // データを配列で受け取る
        $response = $result->fetch_all(MYSQLI_ASSOC);
    }else {
        $response = false;
        echo 'エラーメッセージ:' .$mysqli->error. "\n";
    }

    $mysqli->close();

    return $response;
}