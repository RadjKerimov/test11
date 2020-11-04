<?php
    session_start();
    require('sql.php');
    require('../function.php');
    registered_user('Пожалуйста авторизуйтесь!', '../page_login.php');

    // получаем пользователя с помощью id 
    $user = user_by_id($_GET['id'], $pdo);
    //Название изображение
    $img = get_user_media($_GET['id'], $pdo)['img'];
    //админ? свой аккаунт? 
    $access_admin_user = access_admin_user($user['email'], 'Нет доступа!', '../users.php');
    
    //print_r($img); die;


    if ($access_admin_user) {
        //Удаляем пользывателя
        unlink('../img/user/'. $img); //Удаляем  изображение
        $delete = delete($_GET['id'], $pdo);
        //Если admin возвращает true
        if(role()){
            set_flash_message('success', 'Пользователь удален!'); 
            redirect_to('../users.php');   
            die;      
        }
        redirect_to('../page_register.php');
        die;
    }