<?php

namespace app;

class Auth
{
    /**
     * It's only a validation example!
     * You should search user (on your database) by authorization token
     */
    public function getUserByToken($token)
    {
        /*
        if ($token != 'usertokensecret') {
            // The throwable class must implement UnauthorizedExceptionInterface
            throw new UnauthorizedException('Invalid Token');
        }
        */
        if (session_status() == PHP_SESSION_NONE) session_start();
        $tk= $_SESSION[$token];
        if (!is_numeric($tk)) throw new UnauthorizedException('Invalid Token');
        $sql = "SELECT id,nom FROM usuari where id=".tk.";";
        try{
            // Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();
            $stmt = $db->query($sql);
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e){
            throw new UnauthorizedException('Invalid Token');
        }

        /*$user = [
            'name' => 'Dyorg',
            'id' => 1,
            'permisssion' => 'admin'
        ];*/

        return $user;
    }

}