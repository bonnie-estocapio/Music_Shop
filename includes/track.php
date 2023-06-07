<?php

require_once 'functions.php';
require_once 'download.php';

class Track
{
    public function list($trackID)
    {
        $functions = new Functions;
        $query = $functions->query("SELECT id, title, artist, year, album, genre,price FROM tracks WHERE id=$trackID");
        $data = mysqli_fetch_assoc($query);
        return $data;
    }

    public function search($search, $category)
    {
        $functions = new Functions;
        $query = $functions->query("SELECT id FROM tracks WHERE {$category}='{$search}'");
        if($query === null) {
            echo "Search result not Found";
        } else {
            while($row = mysqli_fetch_assoc($query)) {
                $this->showTrack($row['id']);
            }
        }
    }

    public function owned()
    {
        $functions = new Functions;
        foreach($_SESSION as $data => $value) {
            if($value == 2 && substr($data, 0, 8)== "product_") {
                    $id = substr($data, 8, strlen($data)-8);
                    $this->showTrack($id);
            }
        }
    }

    public function all()
    {
        $count = 1;
        $trackTotal = $this->trackTotal();                    
        while($count <= $trackTotal) {
            $this->showTrack($count);
            $count++;
        }
    }
    
    public function display($trackID)
    {
        $download = new Download;
        $download->downloadTrack();
        $data = $this->list($trackID);
        $track = <<<DELIMETER
            <div class="col-sm-4 col-lg-4 col-md-4">
                <div class="thumbnail">
                    <img src="images/{$data['album']}.jpg" alt="Album">
                    <div class="caption">
                        <h4 class="pull-right">$ {$data['price']}</h4>
                        <h4>{$data['title']}</h4>
                        <p>{$data['artist']}</p>
                        <p>{$data['album']}</p>
                        <p>({$data['genre']}) {$data['year']}</p>
                    </div>
        DELIMETER;

        echo $track;

        if ($_SESSION['product_'.$trackID] == 0) {
            $button = <<<DELIMETER
                        <a class="btn btn-primary" href="cart.php?add={$trackID}">Add to Cart</a>
                    </div>
                </div>
            DELIMETER;
            echo $button;
        } elseif ($_SESSION['product_'.$trackID] == 1) {
            $button = <<<DELIMETER
                        <a class="btn btn-primary" href="cart.php?remove={$trackID}">Remove from Cart</a>
                    </div>
                </div>
            DELIMETER;
            echo $button;
        } elseif ($_SESSION['product_'.$trackID] == 2) {
            $button = <<<DELIMETER
                        <h5> In Library </h5>
                        <a class="btn btn-primary" href="{$_SERVER['REQUEST_URI']}?path=tracks/{$data['title']}.mp3">Download</a>
                    </div>
                </div>
            DELIMETER;
            echo $button;
        }
    }

    public function count()
    {
        $functions = new Functions;
        $count = 0;
        $query = $functions->query("SELECT id FROM tracks");
        while($row = mysqli_fetch_assoc($query))
        {
            $count++;
        }
        return $count;
    }
}