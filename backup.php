<?php

require_once("config.php");

function getDirContents($dir, &$results = array()){
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}

if(!empty(CURRENT_DIR) && !empty(REMOTE_DIR)){
    $data = [];
    $list = getDirContents(CURRENT_DIR);

    foreach ($list as $item){
        array_push($data, str_replace(CURRENT_DIR, "", $item));
    }

    natsort($data);

    foreach ($data as $key => $item){

        if(is_dir(CURRENT_DIR.$item)){
            if(!file_exists(REMOTE_DIR.$item)){
                mkdir(REMOTE_DIR.$item);
                echo "Le repertoire " . REMOTE_DIR.$item . " a été créé ! " . PHP_EOL;
            }
            unset($data[$key]);
        }
    }

    foreach ($data as $item){
        if(!is_dir(CURRENT_DIR.$item)) {

            $path = CURRENT_DIR.$item;
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            if(!in_array($ext, $excludeExt)) {
                if (file_exists(REMOTE_DIR . $item)) {
                    $shaCurrent = sha1_file(CURRENT_DIR . $item);
                    $shaRemote = sha1_file(REMOTE_DIR . $item);

                    if ($shaCurrent != $shaRemote) {
                        copy(CURRENT_DIR . $item, REMOTE_DIR . $item);
                        echo "Fichier : $item copier" . PHP_EOL;
                    }
                } else {
                    copy(CURRENT_DIR . $item, REMOTE_DIR . $item);
                    echo "Fichier : $item copier" . PHP_EOL;
                }
            }
        }
    }
} else {
    echo "ERREUR : Configuration invalide";
}