<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\FeedbackModel;

class AdminFeedbackController extends Controller
{
    // Read-only feedback overview for admins.
    public function index(): void
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            $this->redirect('login-admin.php');
        }

        $feedbackModel = new FeedbackModel($this->pdo);
        // Admins receive the full joined feedback list so they can see both the
        // submission and the customer account it belongs to.
        $feedbackEntries = $feedbackModel->all();

        $this->render('admin/feedback', ['feedbackEntries' => $feedbackEntries]);
    }
}
