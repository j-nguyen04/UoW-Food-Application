<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;

class AdminUserController extends Controller
{
    // List users and handle admin-triggered deletions.
    public function index(): void
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            $this->redirect('login-admin.php');
        }

        $userModel = new UserModel($this->pdo);
        $errorMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
            try {
                $this->validateCsrf();
                // Deletion is guarded in the model so foreign-key-linked users fail gracefully
                // instead of exposing a raw database exception to the admin UI.
                $userModel->delete((int) $_POST['delete']);
                $this->redirect('client-admin.php');
            } catch (\Throwable $exception) {
                $errorMessage = $exception->getMessage();
            }
        }

        $users = $userModel->all();
        $this->render('admin/users', compact('users', 'errorMessage'));
    }
}
