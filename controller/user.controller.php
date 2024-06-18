<?php

function getUserList()
{ //function parameters, two variables.
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM user");
    $stmt->execute();
    $user = $stmt->fetchAll();

    return $user;
}


function addUser($data)
{
    global $pdo;

    $query = "INSERT INTO user (nama, username, password, usertype) VALUES (?, ?, ?, ?)";

    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['nama'], $data['username'], $hashed_password,  $data['usertype']]);
        return  "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}

function deleteUser($id)
{
    global $pdo;

    //delete donasi
    $query = "DELETE FROM user
       WHERE id_user = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}
