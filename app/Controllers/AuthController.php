<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Services\AuthService;
use App\Services\SecurityService;

class AuthController extends Controller
{
    // Handle customer login and establish the customer session on success.
    public function login(): void
    {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCsrf();
                $authService = new AuthService($this->pdo);
                $email = $authService->normalizeEmail((string) ($_POST['email'] ?? ''));
                $password = (string) ($_POST['password'] ?? '');
                $userModel = new UserModel($this->pdo);
                $user = $userModel->findByEmail($email);

                // A session is only created after the email exists and the password matches.
                // Legacy plain-text passwords are transparently upgraded inside AuthService.
                if ($user && $authService->verifyUserPassword($user, $password)) {
                    SecurityService::regenerateSession();
                    $_SESSION['client_id'] = $user['user_id'];
                    $_SESSION['client_name'] = $user['last_name'] . ' ' . $user['first_name'];
                    $this->redirect('index.php');
                }

                $error = 'Invalid email or password. Please try again.';
            } catch (\Throwable $exception) {
                $error = $exception->getMessage();
            }
        }

        $this->render('auth/login', ['error' => $error]);
    }

    // Handle customer registration, then log the new customer in immediately.
    public function signup(): void
    {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCsrf();
                $userModel = new UserModel($this->pdo);
                // New customers are signed in immediately after registration to keep
                // checkout and profile flows seamless.
                $userId = $userModel->create($_POST);
                $profile = $userModel->findProfile($userId);
                SecurityService::regenerateSession();
                $_SESSION['client_id'] = $userId;
                $_SESSION['client_name'] = $profile['last_name'] . ' ' . $profile['first_name'];
                $this->redirect('index.php');
            } catch (\Throwable $exception) {
                $error = $exception->getMessage();
            }
        }

        $this->render('auth/signup', ['error' => $error]);
    }
}
