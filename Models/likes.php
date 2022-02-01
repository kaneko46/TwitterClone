<?php

// いいね！データを処理

/**
 * いいね！を作成
 * @param array $data
 * @return int|false
 */
function createLike(array $data)
{
    // DB接続
    $mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
    if ($mysqli->connect_errno){
        echo 'MYSQLの接続に失敗しました。:' .$mysqli->connect_error. "\n";
        exit;
    }
    //
    // SQLクエリを作成(登録)
    //
    $query = 'INSERT INTO likes (user_id,tweet_id) VALUES (?,?)';
    $statement = $mysqli -> prepare($query);

    // プレースホルダーに値をセット
    $statement ->bind_param('ii',$data['user_id'],$data['tweet_id']);

    //
    // 戻り値を作成
    //
    // クエリを実行、SQLエラーでない場合
    if ($statement->execute()){
        // 戻り値用に変数をセット：インサートID(like.id)
        $response = $mysqli->insert_id;
    } else {
        // 戻り値ように変数をセット：失敗
        $response = false;
        echo 'エラーメッセージ: ' . $mysqli->error . "\n";
    }

    //
    // 後処理
    //
    // DB接続を開放
    $statement -> close();
    $mysqli -> close();

    return $response;
}

/**
 * いいね！を取り消し
 * @param array $data
 * @return bool
 */
function deleteLike(array $data)
{
    // DB接続
    $mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
    if ($mysqli->connect_errno){
        echo 'MYSQLの接続に失敗しました。:'.$mysqli->connect_error . "\n";
        exit;
    }

    //
    // SQLクエリを作成
    //
    // 論理削除のクエリを作成
    $query = 'UPDATE likes SET status ="daleted" WHERE id = ? AND user_id = ?';
    $statement = $mysqli->prepare($query);

    // プレースホルダーに値をセット
    $statement -> bind_param('ii',$data['like_id'],$data['user_id']);

    //
    // 戻り値を作成
    //
    $response = $statement->execute();

    // クエリを実行、SQLエラーでない場合
    if ($response === false){
        echo 'エラーメッセージ:' .$mysqli->error. "\n";
    }

    //
    // 後処理
    //
    // DB接続を開放
    $statement -> close();
    $mysqli -> close();

    return $response;
}
