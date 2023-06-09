<?php

namespace App\User;

use App\Operation\Database;
use App\Track\Track;

class UserData
{
    public function change($before, $after): void
    {
        $userdata = new UserData;
        $track = new Track;

        foreach ($_SESSION as $data => $value) {
            if ($value === $before && substr($data, 0, 8) === "product_") {
                $id = substr($data, 8);
                $query = $track->getQuery($id);
                while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $_SESSION['product_'.$id] = $after;
                }
            }
        }
        $userdata->write($_SESSION['user']);
    }

    public function write($username): void
    {
        $database = new Database;
        $track = new Track;
        $dataArray = [];

        foreach ($_SESSION as $data => $value) {
            if ($value != 0 && substr($data, 0, 8) === "product_") {
                $id = substr($data, 8);
                $query = $track->getQuery($id);
                while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $dataArray['product_' . $id] = $_SESSION['product_' . $id];
                }
            }
        }

        $dataString = http_build_query($dataArray);
        $query = $database->conn->prepare("UPDATE users SET data = :dataString WHERE username = :username");
        $query->bindParam(':dataString', $dataString);
        $query->bindParam(':username', $username);
        $query->execute();

        if (!$query) {
            echo "failed to input data";
        }
    }

    public function read(): void
    {
        $database = new Database;

        $newArray = [];
        $activeUser = $_SESSION['user'];

        $query = $database->conn->prepare("SELECT data FROM users WHERE username = :activeUser");
        $query->bindParam(':activeUser', $activeUser);
        $query->execute();
        $data = $query->fetch(\PDO::FETCH_ASSOC);
        
        $data['data'] = trim($data['data'], '{()}');
        parse_str($data['data'], $newArray);
        
        foreach ($newArray as $data => $value) {
            if ($value != 0 && substr($data, 0, 8) === "product_") {
                $id = substr($data, 8);
                $_SESSION['product_'.$id] = $newArray['product_'.$id];
            }
        }
    }
}
