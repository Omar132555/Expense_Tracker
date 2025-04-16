<?php
require_once 'Connection.php';
require 'vendor/autoload.php';
require_once 'Functions.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$case = isset($_POST['case']) ? $_POST['case'] : null;
if($case != null)
{
    $expense_name = isset($_POST['expense_name']) ? $_POST['expense_name']: null;
    $expense_date = isset($_POST['expense_date']) ? $_POST['expense_date']: null;
    $expense_category = isset($_POST['expense_category']) ? $_POST['expense_category']: null;
    $expense_amount = isset($_POST['expense_amount']) ? $_POST['expense_amount']: null;
    $user_id = isset($_POST['user_id']) ? $_POST['user_id']: null;
    $expense_id = isset($_POST['expense_id']) ? $_POST['expense_id']: null;
    $start_date = isset($_POST['start_date']) ? $_POST['start_date']: null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date']: null;
switch ($case) {
    case "Add_expense":
        Add_expense($expense_name, $expense_category, $expense_amount, $expense_date, $user_id);
        break;
    case "Delete_expense":
        Delete_expense($expense_id);
        break;
    case "Update_expense":
        Update_expense($expense_id, $expense_name, $expense_category, $expense_amount, $expense_date);
        break;
    case "Get_expenses":
        Get_expenses($user_id, $start_date, $end_date, $expense_category);
        break;
    case "Log_out":
        setcookie('jwt-token', '', time() - 3600, "/"); // Clear the JWT Token
        echo "Logged out successfully!";
        break;
}
}
?>