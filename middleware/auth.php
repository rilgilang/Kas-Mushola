<?php
session_start();

function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    if (isSessionExpired()) {
        header("Location: login.php");
        exit();
    }
}

function isAdmin()
{
    if ($_SESSION['roles'] != "admin") {
        return false;
    }
    return true;
}

function isTakmir()
{
    if ($_SESSION['roles'] != "takmir") {
        return false;
    }
    return true;
}

function isKetuaTakmir()
{
    if ($_SESSION['roles'] != "ketua_takmir") {
        return false;
    }
    return true;
}

function isAdminOrTakmir()
{
    if ($_SESSION['roles'] == "ketua_takmir") {
        return false;
    }
    return true;
}

function isSessionExpired()
{
    if (isset($_SESSION['login_time']) && isset($_SESSION['session_expiry'])) {
        $current_time = time();
        $session_lifetime = $current_time - $_SESSION['login_time'];
        if ($session_lifetime > $_SESSION['session_expiry']) {
            session_unset();
            session_destroy();
            return true;
        }
    }
    return false;
}

function login($username, $password)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT id_user, nama, username, password, usertype FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['roles'] = $user['usertype'];
        $_SESSION['login_time'] = time(); // Store the current time
        $_SESSION['session_expiry'] = 3600; // Set session expiry time in seconds (e.g., 1 hour)
        return true;
    }



    return false;
}

function logout()
{
    session_unset();
    session_destroy();
}
