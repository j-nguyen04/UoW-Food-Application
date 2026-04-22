<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\FeedbackModel;
use App\Models\MenuModel;

class HomeController extends Controller
{
    // Build the homepage menu with filters and add-to-cart handling.
    public function index(): void
    {
        $menuModel = new MenuModel($this->pdo);
        $feedbackModel = new FeedbackModel($this->pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_order'])) {
            try {
                $this->validateCsrf();
                // Adding to cart is intentionally kept on the homepage to minimize
                // friction while browsing the menu.
                $menuModel->addToCart((int) $_POST['add_to_order']);
            } catch (\Throwable $exception) {
            }
            $this->redirect('index.php');
        }

        $filters = [
            'cuisine_type' => $_GET['cuisine_type'] ?? 'ALL',
            'category' => $_GET['category'] ?? 'ALL',
            'dietary' => $_GET['dietary'] ?? 'ALL',
        ];

        $this->render('home/index', [
            'filters' => $filters,
            'dietaryOptions' => $menuModel->dietaryOptions(),
            'dishesByCuisine' => $menuModel->dishes($filters),
            'recentFeedback' => $feedbackModel->recent(3),
            'menuModel' => $menuModel,
        ]);
    }
}
