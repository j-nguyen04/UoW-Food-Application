<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\AdminDashboardModel;

class AdminController extends Controller
{
    // Show dashboard metrics and process order-status updates from the admin table.
    public function dashboard(): void
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            $this->redirect('login-admin.php');
        }

        $model = new AdminDashboardModel($this->pdo);
        $errorMessage = '';

        if (isset($_POST['updateStatus'])) {
            try {
                $this->validateCsrf();
                // Business rule: admins can update order statuses in bulk from the dashboard screen.
                $model->updateOrderStatuses($_POST['status'] ?? []);
                $this->redirect('admin.php');
            } catch (\Throwable $exception) {
                $errorMessage = $exception->getMessage();
            }
        }

        $today = date('Y-m-d');
        $overview = $model->overview($today);

        $this->render('admin/dashboard', [
            'today' => $today,
            'totalOrders' => $overview['totalOrders'],
            'totalClients' => $overview['totalClients'],
            'canceledOrders' => $overview['canceledOrders'],
            'totalDishes' => $overview['totalDishes'],
            'orders' => $overview['orders'],
            'topDishes' => $overview['topDishes'],
            'errorMessage' => $errorMessage,
        ]);
    }
}
