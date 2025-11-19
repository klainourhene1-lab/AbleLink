<?php
class Controller {
    protected function view($view, $data = array(), $layout = 'main') {
        extract($data);
        $viewPath = "app/Views/$view";
        if (!file_exists($viewPath)) {
            die("Vue introuvable: $viewPath");
        }
        $view = $viewPath;
        $layoutPath = "app/Views/layouts/$layout.php";
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            require "app/Views/layouts/main.php";
        }
    }
    protected function redirect($url) {
        header("Location: /ablelink/$url"); 
        exit;
    }
}