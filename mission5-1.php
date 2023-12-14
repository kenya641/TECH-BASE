<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission5-1</title>
    </head>
    <body>
        <form method="POST" action="">
            名前:<input type="text" name="name"><br>
            コメント:<input type="text" name="comment"><br>
            パスワード:<input type="password" name="pass"><br>
            <input type="submit" name="submit" value="送信"><br>
        </form>
        <form method="POST" action="">
            削除(数字入力):<input type="number" name="delete"><br>
            パスワード:<input type="password" name="pass"><br>
            <input type="submit" name="submit_delete" value="削除">
        </form>
        <form method="POST" action="">
            編集(数字入力):<input type="number" name="edit"><br>
            パスワード:<input type="password" name="pass"><br>
            <input type="submit" name="submit_edit" value="編集">
        </form>
        <?php
            $dsn = 'mysql:dbname=kenyadb;host=localhost';
            $user = 'kenya641';
            $password = 'suzuken641';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $sql = "CREATE TABLE IF NOT EXISTS tbtest"
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "name CHAR(32),"
            . "comment TEXT,"
            . "pass CHAR(32),"
            . "date DATETIME"
            .");";
            $stmt = $pdo->query($sql);
           
        if(isset($_POST["submit"])) {
            if(isset($_POST["comment"]) && isset($_POST["name"]) && isset($_POST["pass"])) {
                $comment = $_POST["comment"];
                $name = $_POST["name"];
                $pass = $_POST["pass"];
                $date = date("Y/m/d H:i:s");
                
                $sql = "INSERT INTO tbtest (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->execute();
                
        }
    }elseif(isset($_POST["submit_delete"])) {
        if(isset($_POST["delete"]) && isset($_POST["pass"])) {
            $delete = $_POST["delete"];
            $pass = $_POST["pass"];
            
            $sql = 'SELECT * FROM tbtest WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result && $result['pass'] == $pass){
            $id = $delete;
            $sql = 'delete from tbtest where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            }else{
                echo "パスワードが一致しません。";
            }
        }
    } elseif (isset($_POST["submit_edit"])) {
    if (isset($_POST["edit"]) && isset($_POST["pass"])) {
        $edit = $_POST["edit"];
        $pass = $_POST["pass"];

        $sql = 'SELECT * FROM tbtest WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['pass'] == $pass) {
            $editId = $result['id'];
            $editName = $result['name'];
            $editComment = $result['comment'];
            $editPassword = $result['pass'];

            echo '<form method="POST" action="">
                    名前:<input type="text" name="edited_name" value="'.$editName.'"><br>
                    コメント:<input type="text" name="edited_comment" value="'.$editComment.'"><br>
                    パスワード:<input type="password" name="edited_pass" value="'.$editPassword.'"><br>
                    <input type="hidden" name="edit_id" value="'.$editId.'">
                    <input type="submit" name="submit_update" value="更新"><br>
                </form>';
        } else {
            echo "パスワードが一致しません。";
        }
    }
    }  elseif (isset($_POST["submit_update"])) {
        if (isset($_POST["edited_name"]) && isset($_POST["edited_comment"]) && isset($_POST["edited_pass"]) && isset($_POST["edit_id"])) {
            $editedName = $_POST["edited_name"];
            $editedComment = $_POST["edited_comment"];
            $editedPass = $_POST["edited_pass"];
            $editId = $_POST["edit_id"];  // ここで $editId を適切に定義

        // パスワードの一致を確認
            $sql = 'SELECT * FROM tbtest WHERE id = :id AND pass = :pass';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $editId, PDO::PARAM_INT);
            $stmt->bindParam(':pass', $editedPass, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
            // パスワードが一致する場合、更新処理を実行
                $sql = 'UPDATE tbtest SET name = :name, comment = :comment WHERE id = :id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $editedName, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $editedComment, PDO::PARAM_STR);
                $stmt->bindParam(':id', $editId, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // パスワードが一致しない場合はエラーメッセージを表示
                echo "パスワードが一致しません。更新できません。";
            }
        }

    }
        $sql = 'SELECT * FROM tbtest';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
                foreach ($results as $row){
                //$rowの中にはテーブルのカラム名が入る
                    echo $row['id'].',';
                    echo $row['name'].',';
                    echo $row['comment'].',';
                    echo $row['pass'].',';
                    echo $row['date'].'<br>';
                    echo "<hr>";
                }
        ?>
    </body>
</html>