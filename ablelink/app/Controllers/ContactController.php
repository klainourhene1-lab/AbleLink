<?php
namespace App\Controllers;

use App\Core\Controller;

class ContactController extends Controller {
    public function index(): void {
        $message_sent = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';
            if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
                $message_sent = true;
            }
        }
        
        $this->render('general/contact', ['message_sent' => $message_sent]);
    }
}

