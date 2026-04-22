<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CartModel;

class CartController extends Controller
{
    // All cart mutations are POST-only to avoid accidental or cross-site state changes.
    public function handle(): void
    {
        $cartModel = new CartModel();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php');
        }

        try {
            $this->validateCsrf();
        } catch (\Throwable $exception) {
            $this->redirect('index.php');
        }

        if (isset($_POST['remove'])) {
            $cartModel->remove((int) $_POST['remove']);
            $this->redirect('index.php');
        }

        if (isset($_POST['update'], $_POST['id'])) {
            $cartModel->updateQuantity((int) $_POST['id'], (string) $_POST['update']);
            $this->redirect('index.php');
        }

        $this->redirect('index.php');
    }
}
