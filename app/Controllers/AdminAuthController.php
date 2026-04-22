<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\AdminModel;
use App\Services\SecurityService;

class AdminAuthController extends Controller
{
    // Admin login mirrors customer login but writes to the admin session namespace.
    public function login(): void
    {
        if (!empty($_SESSION['admin_logged_in'])) {
            $this->redirect('admin.php');
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCsrf();
                $username = (string) ($_POST['username'] ?? '');
                $password = (string) ($_POST['password'] ?? '');

                $adminModel = new AdminModel($this->pdo);
                $admin = $adminModel->verifyCredentials($username, $password);

                if ($admin) {
                    // Admin login regenerates the session id for the same fixation protection
                    // used by customer accounts.
                    SecurityService::regenerateSession();
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $this->redirect('admin.php');
                }

                $error = 'Invalid username or password.';
            } catch (\Throwable $exception) {
                $error = $exception->getMessage();
            }
        }

        $this->render('admin/login', ['error' => $error]);
    }
}
