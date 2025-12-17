<?php
namespace App\Core;

class Controller {
    protected function render(string $view, array $params = []): void {
        extract($params);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        include __DIR__ . '/../Views/layout.php';
    }
}
