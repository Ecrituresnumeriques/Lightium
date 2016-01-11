<?php

   //Installation process
   $query = $file_db->query("SELECT count(*) as yes FROM sqlite_master WHERE type='table' AND name='user'");
   $query->execute() or die("Could'nt exec user table check");
   $query = $query->fetch();
   if($query['yes']){
      $query = $file_db->query("SELECT count(*) as users FROM user");
      $query->execute() or die("Could'nt exec users check");
      $query = $query->fetch();
      if($query['users'] == 0){
        //No user inputed, can't continue till user > 0 not satisfied
        if(!empty($_POST['user']) AND !empty($_POST['password'])){
          //add new user
          	$user = array(
          				array(
          					'username' => $_POST['user'],
          					'pswd' => $_POST['password']
          				)
          			);
          	$insert = $file_db->prepare("INSERT INTO user (id_user,token,username,salt,hash) VALUES (NULL,NULL,:username,:salt,:hash)");
          	$insert->bindParam(":username",$username);
          	$insert->bindParam(":salt",$salt);
          	$insert->bindParam(":hash",$hash);
          	foreach($user as $u){
          		$salt = base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
              	$password = crypt($u['pswd'], '$6$rounds=1000$'.$salt);
              	$password = explode("$",$password);
              	$hash = $password[4];
          		$username = $u['username'];
          		$insert->execute() or die('Unable to add new user');
          	}
          //then continue on the update process
        }
        else{
          //submit form for login infos
          //TODO make it more beautifull
          ?>
          <!DOCTYPE html>
          <head></head>
          <body>
            <form action="" method="post">
            <input type="text" name="user" placeholder="Username">
            <input type="password" name="password" placeholder="Password">
            <input type="submit">
          <?php
          die();
        }
      }
      $query = $file_db->query("SELECT count(*) as settings FROM settings");
      $query->execute() or die("Could'nt exec users check");
      $query = $query->fetch();
      if($query['settings'] == 0){
        if(!empty($_POST['name']) AND !empty($_POST['description']) AND !empty($_POST['title']) AND !empty($_POST['meta']) AND !empty($_POST['lang'])){
            $insert = "INSERT INTO settings (name, description, title, meta, lang) VALUES (:name,:description,:title,:meta,:lang)";
            $stmt = $file_db->prepare($insert);
            $stmt->bindParam(':name', $name, SQLITE3_TEXT);
            $stmt->bindParam(':description', $description, SQLITE3_TEXT);
            $stmt->bindParam(':title', $title, SQLITE3_TEXT);
            $stmt->bindParam(':meta', $meta, SQLITE3_TEXT);
            $stmt->bindParam(':lang', $lang, SQLITE3_TEXT);
            for ($i = 0; $i < count($_POST['lang']); $i++) {
                // Execute statement
                $description = $_POST['description'][$i];
                $name = $_POST['name'][$i];
                $lang = $_POST['lang'][$i];
                $title = $_POST['title'][$i];
                $meta = $_POST['meta'][$i];
                $stmt->execute();
            }
          //then continue on the update process
        }
        else{
          //submit form for login infos
          //TODO make it more beautifull
          ?>
          <!DOCTYPE html>
          <head>
          <script src="/side/jquery.js"></script>
          <script type="text/javascript">
            $(document).ready(function(){
              $("#addLanguage").on("click",function(){
                $("#submit").before(' <input id="languages" type="text" name="name[]" placeholder="Name"><textarea name="description[]" placeholder="Description"></textarea><textarea name="meta[]" placeholder="Meta"></textarea><input type="text" name="title[]" placeholder="Title"><input type="text" name="lang[]" placeholder="Lang"><hr>');
              });
            });
          </script>

          </head>
          <body>
            <p id="addLanguage">Add a language</p>
            <form action="" method="post">
            <input id="submit" type="submit">
          <?php
          die();
        }


      }


   }
   else{

    /**************************************
    * Create tables                       *
    **************************************/

    $file_db->exec("CREATE TABLE IF NOT EXISTS settings (name TEXT, description TEXT, title TEXT, meta TEXT,lang TEXT)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS category (id_cat INTEGER PRIMARY KEY)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS category_sub (id_subcat INTEGER PRIMARY KEY, id_cat INTEGER)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS category_lang (id_cat INTEGER, name TEXT, lang TEXT, image TEXT, description TEXT, cleanstring TEXT)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS category_sub_lang (id_subcat INTEGER, name TEXT, lang TEXT, image TEXT, short TEXT, description TEXT, cleanstring TEXT)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS item (id_item INTEGER PRIMARY KEY, year INTEGER, month INTEGER, day INTEGER, published INTEGER, time INTEGER)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS item_assoc (id_item INTEGER, id_subcat INTEGER)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS item_lang (id_item INTEGER, title TEXT, short TEXT, content TEXT, cleanstring TEXT, lang TEXT)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS item_maj (id_item INTEGER, maj INTEGER, who TEXT)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS user (id_user INTEGER PRIMARY KEY, token TEXT,username TEXT, salt TEXT, hash TEXT)");
}

?>