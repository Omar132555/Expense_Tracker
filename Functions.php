<?php
require 'Connection.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

// ============== In Case using Headers ==============

/*$autheader = $_SERVER['HTTP_AUTHORIZATION'];
if (preg_match('/Bearer\s(\S+)/', $autheader, $matches)) {
    $jwt = $matches[1];
} else {
    echo "Authorization header not found!";
    http_response_code(401);
    exit;
}*/

try {
$jwt = isset($_COOKIE['jwt-token']) ? $_COOKIE['jwt-token'] : null;
if($jwt == null) {
    echo "Unauthorized!";
    http_response_code(401);
    exit;
}
$decoded = JWT::decode($jwt, new Key('bgCptJDTIGLaGA5fdSWnsXcVx9e4AZezqhJ7GpGNcjU=', 'HS256'));
$ID = $decoded -> data -> ID;
function Add_expense($expense_name, $expense_category, $expense_amount, $expense_date, $user_id) {
    global $connection;
    global $ID;
    $stmt = $connection->prepare("INSERT INTO expenses (expense_name, expense_category, expense_amount, expense_date, ID)
    VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisi", $expense_name, $expense_category, $expense_amount, $expense_date, $ID);
    if($stmt->execute()){
        echo "Expense added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
function Get_expenses($user_id, $start_date = null, $end_date = null, $expense_category = null) {
    global $connection;
    global $ID;
        $query = "SELECT * FROM expenses where ID = $ID";
        if($expense_category)
        {
            $query .= " AND expense_category  = '$expense_category' ";
        }
        if($start_date && $end_date)
        {
            $query .= " AND expense_date BETWEEN '$start_date' AND '$end_date'";
        }
        $stmt = $connection->prepare($query);
        if($stmt->execute()){
            $result = $stmt->get_result();
            http_response_code(200);
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    echo "Expense Name: " . $row['expense_name'] . " - Category: " . $row['expense_category'] . " - Amount: " . $row['expense_amount'] . " - Date: " . $row['expense_date'] . "\n";
                }
            } else {
                echo "No expenses found!";
                http_response_code(404);
            }
        }
        $stmt->close();    
}
function Delete_expense($expense_id) {
    global $connection;
    global $ID;
    $stmt = $connection->prepare("DELETE FROM expenses WHERE expense_id = ? and ID = $ID");
    $stmt->bind_param("i", $expense_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($stmt->affected_rows > 0){
        echo "Expense deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
function Update_expense($expense_id, $expense_name = null, $expense_category = null, $expense_amount = null, $expense_date = null) {
    global $connection;
    global $ID;
    $types = "";
    $param = [];
    $query = "";
    $comma = ", ";
    $comma_counter = 0;

    if($expense_name)
    {
        if($comma_counter > 0)
        {
            $query .= $comma;
        }
        $query .= "expense_name = ?";
        $param[] = $expense_name;
        $types .= "s";
        $comma_counter++;
    }
    if($expense_amount)
    {
        if($comma_counter > 0)
        {
            $query .= $comma;
        }
        $query .= "expense_amount = ?";
        $param[] = $expense_amount;
        $types .= "i";
        $comma_counter++;
    }
    if($expense_category)
    {
        if($comma_counter > 0)
        {
            $query .= $comma;
        }
        $query .= "expense_category = ?";
        $param[] = $expense_category;
        $types .= "s";
        $comma_counter++;
    }
    if($expense_date)
    {
        if($comma_counter > 0)
        {
            $query .= $comma;
        }
        $query .= "expense_date = ?";
        $param[] = $expense_date;
        $types .= "s";
        $comma_counter++;
    }
    try{
    $mainquery = "UPDATE expenses SET $query WHERE expense_id = ? and ID = ?";
    $stmt = $connection->prepare($mainquery);
    $types .= "ii"; // Add the type for expense_id
    $param[] = $expense_id; // Add the expense_id to the parameters
    $param[] = $ID; // Add the expense_id to the parameters
    $stmt->bind_param($types, ...$param);
    if($stmt->execute()){
        echo "Expense updated successfully!. $mainquery";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    }
    catch (Exception $e) {
        echo "Error: " . $e->getMessage() .$mainquery. " ".$comma_counter;
        http_response_code(500);
        exit;
    }
}
}

catch (ExpiredException $e) {
    // Handle expired token
    echo "Error: Your Login Timeout! Please login again.";
    http_response_code(401);
    exit;
}