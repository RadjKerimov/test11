<?php

  function str_selec($option1, $option2){
    if ($option1 == $option2) {
      echo 'selected';
    }
  }
  /* 
  * Авторизован пользователь?  return true
  * Входные данные email пользывателя и ссылку 
  */
  function registered_user($text, $redirect){
    if (!isset($_SESSION['email'])) {
      set_flash_message('danger', $text);
      redirect_to($redirect);
      die;
    }return true;
  }


/*
* Проверяем  пользыватель админ если да то доступ true
* если user то только  свои данные
* Входные данные email пользывателя текст и ссылку 
* Пример :  access_admin_user($user['email'], 'Нет доступа!', 'users.php');
*/
  function access_admin_user($email, $text, $redirect){
    if($_SESSION['email'] != $email && $_SESSION['role'] != 'admin') {
      set_flash_message('danger', $text);
      redirect_to($redirect);
      die;
    } return true;
  }









  //Подготовка сообщения 
  function set_flash_message($name, $message){
    $_SESSION[$name] = $message;
  };

  function display_flash_message($name){ 
    if (isset($_SESSION[$name])) {
      echo "<div class=\"alert alert-$name\">$_SESSION[$name]</div>";
      unset($_SESSION[$name]);
    }
  };

  function redirect_to($path){
    header("Location: $path");
  };



  //Возвращает id пользователя по email
  function user_id($email,$pdo){
    $prepare = $pdo->prepare("SELECT * FROM `users` WHERE `email` = ?");
    $prepare->execute([$email]);
    return $prepare->fetch(PDO::FETCH_ASSOC);
  }



  //Возвращает пользователя по id
  function user_by_id($id,$pdo){
    $prepare = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ?");
    $prepare->execute([$id]);
    return $prepare->fetch(PDO::FETCH_ASSOC);
  }

  
  //Возвращает true если admin 
  function role(){
    if ($_SESSION['role'] == 'admin') 
      return true;
    else
      return false;
  }
  ////////////////////////// 

  /*
    Проверяем если такой email БД 
    return array [email, pass]
  */
  function if_the_user($email, $pdo){
    $prepare = $pdo->prepare("SELECT * FROM `users` WHERE `email` = :email ");
    $prepare->execute(['email' => $email]);
    return $prepare->fetch(PDO::FETCH_ASSOC); 
  }







    //Проверяем сушествует ли такой пользыватель, если да то return email и password
  function get_user_by_email ($email, $pdo){
    $prepare = $pdo->prepare("SELECT * FROM `users` WHERE `email` = :email");
    $prepare->execute(['email' => $email]);
    return $prepare->fetch(PDO::FETCH_ASSOC);
  };

  // Добавляет пользователя return id user
  function add_user($email, $pass, $pdo){
    $pass =  password_hash($pass, PASSWORD_DEFAULT);
    $prepare = $pdo->prepare("INSERT INTO `users`(`email`, `pass`) VALUES(:email, :pass)");
    $prepare->execute(['email' => $email, 'pass' => $pass]);
    return $pdo->lastInsertId();
  };
  // Общая информация
  function user_info($name, $work, $tel, $address, $new_user_id, $pdo){
    $prepare = $pdo->prepare("INSERT INTO `user_info`(`name`, `work`, `tel`, `address`, `id_user`) VALUES(?,?,?,?,?)");
    $prepare->execute([$name, $work, $tel, $address, $new_user_id]);
  };
  // MEDIA
  function user_media($status, $img, $vk, $telegram, $insta, $new_user_id, $pdo){
    $prepare = $pdo->prepare("INSERT INTO `media`(`status`, `img`, `vk`, `telegram`, `insta`, `id_user`) VALUES(?,?,?,?,?,?)");
    $prepare->execute([$status, $img, $vk, $telegram, $insta, $new_user_id]);
  };



  //Получаем всех пользователей return array
  function is_not_logged_in($pdo){
    $prepare = $pdo->prepare("SELECT * FROM `users` ");
    $prepare->execute();
    return $prepare->fetchAll(PDO::FETCH_ASSOC);
  }
  function get_user_info($id_user, $pdo){
    $prepare = $pdo->prepare("SELECT * FROM `user_info`  WHERE `id_user` = ?");
    $prepare->execute([$id_user]);
    return $prepare->fetch(PDO::FETCH_ASSOC);
  }
  function get_user_media($id_user, $pdo){
    $prepare = $pdo->prepare("SELECT * FROM `media`  WHERE `id_user` = ?");
    $prepare->execute([$id_user]);
    return $prepare->fetch(PDO::FETCH_ASSOC);
  }



    //Получаем пользоватя по id return array
  function get_user_by_id($id, $pdo){
    $prepare = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ?");
    $prepare->execute([$id]);
    return $prepare->fetch(PDO::FETCH_ASSOC);
  }

  //Обновляем данные пользователя
  function update_user_info($name, $work, $tel, $address, $id_user, $pdo){
    $prepare = $pdo->prepare("UPDATE `user_info` SET `name` = :name, `work` = :work, `tel` = :tel, `address` = :address  WHERE `id_user` = :id_user");
    return $prepare->execute([
        'name' => $name, 
        'work' => $work, 
        'tel' => $tel, 
        'address' => $address ,
        'id_user' => $id_user
      ]);
  }

  function update_security($id, $email, $pass, $pdo){
    $pass =  password_hash($pass, PASSWORD_DEFAULT);
    $prepare = $pdo->prepare("UPDATE `users` SET `email` = :email, `pass` = :pass  WHERE `id` = :id");
    $result = $prepare->execute(['id' => $id, 'email' => $email, 'pass' => $pass, ]);
    return $result;
  }

  function update_status($id_user, $status, $pdo){
    $prepare = $pdo->prepare("UPDATE `media` SET `status` = :status  WHERE `id_user` = :id_user");
    $prepare->execute(['id_user' => $id_user, 'status' => $status]);
  }

  function update_img($id_user, $img, $pdo){
    $prepare = $pdo->prepare("UPDATE `media` SET `img` = :img  WHERE `id_user` = :id_user");
    $prepare->execute(['id_user' => $id_user, 'img' => $img]);
  }

  function delete($user_id, $pdo){
    $prepare1 = $pdo->prepare("DELETE  FROM users WHERE id= ?");
    $prepare1->execute([$user_id]);

    $prepare2 = $pdo->prepare("DELETE  FROM media WHERE id_user= ?");
    $prepare2->execute([$user_id]);

    $prepare3 = $pdo->prepare("DELETE  FROM user_info WHERE id_user= ?");
    $prepare3->execute([$user_id]);
  }
//'DELETE t1, t2 FROM t1 INNER JOIN t2 INNER JOIN t3  WHERE t1.id=t2.id AND t2.id=t3.id;'