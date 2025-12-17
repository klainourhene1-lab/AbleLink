<?php
spl_autoload_register(function($class){
    $prefix = 'App\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
    $file = __DIR__ . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\SuccessStoriesController;
use App\Controllers\AdminController;
use App\Controllers\EventsController;
use App\Controllers\ServicesController;
use App\Controllers\ContactController;
use App\Controllers\AboutController;
use App\Controllers\LoginController;
use App\Controllers\AdminLoginController;
use App\Controllers\AuthController;

$router = new Router();

// Page d'accueil
$router->get('index', [HomeController::class, 'index']);
$router->get('', [HomeController::class, 'index']);
$router->get('index.php', [HomeController::class, 'index']);

// Pages générales
$router->get('about', [AboutController::class, 'index']);
$router->get('services', [ServicesController::class, 'index']);
$router->get('contact', [ContactController::class, 'index']);
$router->post('contact', [ContactController::class, 'index']);

// Authentication
$router->get('auth/login', [AuthController::class, 'showLogin']);
$router->post('auth/do-login', [AuthController::class, 'login']);
$router->get('auth/register', [AuthController::class, 'showRegister']);
$router->post('auth/do-register', [AuthController::class, 'register']);
$router->get('auth/logout', [AuthController::class, 'logout']);

// Stats
$router->get('stats', function() {
    $viewFile = __DIR__ . '/app/Views/stats.php';
    include __DIR__ . '/app/Views/layout.php';
});

// Login
$router->get('login', [LoginController::class, 'index']);
$router->post('login', [LoginController::class, 'index']);

// Success Stories
$router->get('success-stories', [SuccessStoriesController::class, 'index']);
$router->get('success-stories/create', [SuccessStoriesController::class, 'create']);
$router->post('success-stories/store', [SuccessStoriesController::class, 'store']);
$router->get('success-stories/edit', [SuccessStoriesController::class, 'edit']);
$router->post('success-stories/update', [SuccessStoriesController::class, 'update']);
$router->get('success-stories/delete', [SuccessStoriesController::class, 'delete']);
$router->get('success-stories/like', [SuccessStoriesController::class, 'like']);
$router->get('success-stories/share', [SuccessStoriesController::class, 'share']);
$router->get('success-stories/comments', [SuccessStoriesController::class, 'comments']);
$router->post('success-stories/comment-store', [SuccessStoriesController::class, 'commentStore']);
$router->get('success-stories/comment-delete', [SuccessStoriesController::class, 'commentDelete']);
$router->get('success-stories/comment-like', [SuccessStoriesController::class, 'commentLike']);
$router->get('success-stories/comment-report', [SuccessStoriesController::class, 'commentReport']);
$router->get('historique', [SuccessStoriesController::class, 'history']);

// Events
$router->get('events', [EventsController::class, 'index']);
$router->post('events/evaluate', [EventsController::class, 'evaluate']);
$router->post('events/register', [EventsController::class, 'register']);

// Admin
$router->get('admin', [AdminController::class, 'index']);
$router->get('admin/all-stories', [AdminController::class, 'allStories']);
$router->get('admin/approve', [AdminController::class, 'approve']);
$router->get('admin/reject', [AdminController::class, 'reject']);
$router->post('admin/bulk', [AdminController::class, 'bulk']);
$router->get('admin/reported', [AdminController::class, 'reported']);
$router->get('admin/comments', [AdminController::class, 'comments']);
$router->get('admin/reported/unreport', [AdminController::class, 'unreport']);
$router->get('admin/reported/delete', [AdminController::class, 'deleteComment']);
$router->get('admin/story', [AdminController::class, 'story']);
$router->get('admin/export-stories', [AdminController::class, 'exportStories']);
$router->get('admin/stats', [AdminController::class, 'stats']);

// Admin Login
$router->get('admin_login', [AdminLoginController::class, 'index']);
$router->get('admin-login', [AdminLoginController::class, 'index']);
$router->post('admin_login', [AdminLoginController::class, 'index']);
$router->post('admin-login', [AdminLoginController::class, 'index']);

// Logout
$router->get('logout', [\App\Controllers\LogoutController::class, 'index']);

$router->dispatch();
