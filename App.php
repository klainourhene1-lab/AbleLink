<?php
class App {
    public function __construct() {
        spl_autoload_register(function($c) {
            if (file_exists("app/Core/$c.php")) {
                require "app/Core/$c.php";
                return;
            } elseif (file_exists("app/Controllers/$c.php")) {
                require "app/Controllers/$c.php";
                return;
            } elseif (file_exists("app/Models/$c.php")) {
                require "app/Models/$c.php";
                return;
            } elseif (file_exists("app/Repositories/$c.php")) {
                require "app/Repositories/$c.php";
                return;
            } elseif (file_exists("app/Models/" . strtolower($c) . ".php")) {
                require "app/Models/" . strtolower($c) . ".php";
                return;
            }
        });

        $url = isset($_GET['url']) ? $_GET['url'] : '';
        $url = rtrim($url, '/');
        $p = explode('/', $url);

        if ($p[0] === 'admin') {
            $ac = new AdminController();
            $action = isset($p[1]) ? $p[1] : 'index';

            if ($action === 'login') {
                $ac->login();
            } elseif ($action === 'logout') {
                $ac->logout();
            } elseif ($action === 'stories') {
                if (isset($p[2]) && $p[2] === 'delete') {
                    $id = isset($p[3]) ? (int)$p[3] : 0;
                    $ac->deleteStory($id);
                } elseif (isset($p[2]) && $p[2] === 'status') {
                    $id = isset($p[3]) ? (int)$p[3] : 0;
                    $status = isset($p[4]) ? $p[4] : 'pending';
                    $ac->updateStoryStatus($id, $status);
                } else {
                    $ac->stories();
                }
            } elseif ($action === 'comments') {
                if (isset($p[2]) && $p[2] === 'delete') {
                    $id = isset($p[3]) ? (int)$p[3] : 0;
                    $ac->deleteComment($id);
                } else {
                    $ac->comments();
                }
            } else {
                $ac->index();
            }
        } elseif ($p[0] === 'stories' || $url === '') {
            $sc = new StoryController();
            $action = isset($p[1]) ? $p[1] : 'index';

            if ($action === 'create') {
                if (!empty($_POST)) {
                    $sc->store();
                } else {
                    $sc->create();
                }
            } elseif ($action === 'edit') {
                $id = isset($p[2]) ? (int)$p[2] : 0;
                if (!empty($_POST)) {
                    $sc->update($id);
                } else {
                    $sc->edit($id);
                }
            } elseif ($action === 'show') {
                $id = isset($p[2]) ? (int)$p[2] : 0;
                $sc->show($id);
            } elseif ($action === 'like') {
                $id = isset($p[2]) ? (int)$p[2] : 0;
                $sc->like($id);
            } elseif ($action === 'comment') {
                $id = isset($p[2]) ? (int)$p[2] : 0;
                $sc->addComment($id);
            } elseif ($action === 'delete') {
                $id = isset($p[2]) ? (int)$p[2] : 0;
                $sc->delete($id);
            } else {
                $sc->index();
            }
        }
    }
}
