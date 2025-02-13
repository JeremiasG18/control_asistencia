<?php

class Views{


    public static function render($template, array $data = []){

        extract($data);

        include __DIR__ . "/../../src/views/$template";
    }

}

?>