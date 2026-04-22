<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;

class ProfileController extends Controller
{
    // Show and update the signed-in customer's profile details.
    public function show(): void
    {
        if (!isset($_SESSION['client_id'], $_SESSION['client_name'])) {
            $this->redirect('login.php');
        }

        $userModel = new UserModel($this->pdo);
        $profile = $userModel->findProfile((int) $_SESSION['client_id']);

        if ($profile === null) {
            session_unset();
            session_destroy();
            $this->redirect('login.php');
        }

        $successMessage = '';
        $errorMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCsrf();
                $userModel->updateProfile((int) $_SESSION['client_id'], $_POST);
                $profile = $userModel->findProfile((int) $_SESSION['client_id']);
                // Keep the session display name aligned with the database after edits.
                $_SESSION['client_name'] = $profile['last_name'] . ' ' . $profile['first_name'];
                $successMessage = 'Your profile details were updated successfully.';
            } catch (\Throwable $exception) {
                $errorMessage = $exception->getMessage();
                // Repopulate the form with the attempted values so validation errors
                // do not force the customer to retype everything.
                $profile = [
                    'last_name' => trim((string) ($_POST['last_name'] ?? $profile['last_name'])),
                    'first_name' => trim((string) ($_POST['first_name'] ?? $profile['first_name'])),
                    'email' => trim((string) ($_POST['email'] ?? $profile['email'] ?? '')),
                    'phone' => trim((string) ($_POST['phone'] ?? $profile['phone'])),
                ] + $profile;
            }
        }

        $this->render('profile/show', compact('profile', 'successMessage', 'errorMessage'));
    }
}
