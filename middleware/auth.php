<?php
session_start();

function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function login($username, $password)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT id_user, nama, username, password, usertype FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id_user'];
        return true;
    }



    return false;
}

function logout()
{
    session_unset();
    session_destroy();
}
