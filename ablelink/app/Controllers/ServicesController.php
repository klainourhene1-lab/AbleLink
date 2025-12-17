<?php
namespace App\Controllers;

use App\Core\Controller;

class ServicesController extends Controller {
    public function index(): void {
        $this->render('general/services');
    }
}

