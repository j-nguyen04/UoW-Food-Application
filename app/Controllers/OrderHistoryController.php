<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\OrderModel;

class OrderHistoryController extends Controller
{
    // Customer-facing order history and status tracking screen.
    public function index(): void
    {
        if (!isset($_SESSION['client_id'], $_SESSION['client_name'])) {
            $this->redirect('login.php');
        }

        $orderModel = new OrderModel($this->pdo);
        // History is scoped to the logged-in customer only.
        $orders = $orderModel->history((int) $_SESSION['client_id']);

        $this->render('orders/history', ['orders' => $orders]);
    }
}
