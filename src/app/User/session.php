<?php

namespace App\User;

use App\Operation\Database;

Class Session
{
    public function write($sessionID, $username)
    {
        $database = new Database;
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            $query = $database->query("INSERT INTO sessions (session_id, user) VALUES ('$sessionID', '$username')");
        }
    }
}