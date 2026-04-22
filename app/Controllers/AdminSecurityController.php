<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\AdminModel;

class AdminSecurityController extends Controller
{
    // Security tools page for hashing legacy admin passwords and resetting admin credentials.
    public function index(): void
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            $this->redirect('login-admin.php');
        }

        $adminModel = new AdminModel($this->pdo);
        $successMessage = '';
        $errorMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCsrf();

                if (isset($_POST['hash_legacy_passwords'])) {
                    // Upgrade any remaining plain-text admin passwords in one controlled admin action.
                    $updatedCount = $adminModel->hashLegacyPasswords();
                    $successMessage = $updatedCount > 0
                        ? "Hashed {$updatedCount} legacy admin password(s)."
                        : 'No legacy admin passwords were found.';
                } elseif (isset($_POST['reset_admin_password'])) {
                    $adminId = (int) ($_POST['admin_id'] ?? 0);
                    $newPassword = (string) ($_POST['new_password'] ?? '');
                    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

                    $adminModel->resetPassword($adminId, $newPassword, $confirmPassword);
                    $successMessage = 'The selected admin password has been reset successfully.';
                }
            } catch (\Throwable $exception) {
                $errorMessage = $exception->getMessage();
            }
        }

        $admins = $adminModel->all();

        $this->render('admin/security', compact('admins', 'successMessage', 'errorMessage'));
    }
}
