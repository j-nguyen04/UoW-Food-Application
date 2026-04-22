<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\FeedbackModel;

class FeedbackController extends Controller
{
    // Accept customer feedback and show both personal and community submissions.
    public function show(): void
    {
        if (!isset($_SESSION['client_id'], $_SESSION['client_name'])) {
            $this->redirect('login.php');
        }

        $feedbackModel = new FeedbackModel($this->pdo);
        $successMessage = '';
        $errorMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCsrf();
                $feedbackModel->create(
                    (int) $_SESSION['client_id'],
                    (int) ($_POST['rating'] ?? 0),
                    (string) ($_POST['message'] ?? '')
                );
                $successMessage = 'Thank you. Your feedback has been submitted.';
            } catch (\Throwable $exception) {
                $errorMessage = $exception->getMessage();
            }
        }

        $userFeedback = $feedbackModel->forUser((int) $_SESSION['client_id']);
        $recentFeedback = $feedbackModel->recent(6);

        $this->render('feedback/show', compact('successMessage', 'errorMessage', 'userFeedback', 'recentFeedback'));
    }
}
