<?php
include 'db_donor_and_requests.php'; // Database connection

// Handle GET request to return blood stock as JSON
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT blood_type, units_available FROM blood_stock";
    $result = $conn->query($sql);

    $stock = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stock[$row['blood_type']] = (int)$row['units_available'];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($stock);
    exit;
}

// Handle POST request to store new donor and update stock
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $blood_type = $_POST['blood_type'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $health_condition = $_POST['health_condition'];
    $last_donation_date = $_POST['last_donation_date'];

    $sql = "INSERT INTO donors_list (full_name, age, gender, blood_type, contact_number, address, health_condition, last_donation_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssss", $full_name, $age, $gender, $blood_type, $contact_number, $address, $health_condition, $last_donation_date);

    if ($stmt->execute()) {
        $updateStockSql = "UPDATE blood_stock SET units_available = units_available + 1 WHERE blood_type = ?";
        $updateStmt = $conn->prepare($updateStockSql);
        $updateStmt->bind_param("s", $blood_type);
        $updateStmt->execute();

        if ($updateStmt->affected_rows == 0) {
            $insertStockSql = "INSERT INTO blood_stock (blood_type, units_available) VALUES (?, 1)";
            $insertStmt = $conn->prepare($insertStockSql);
            $insertStmt->bind_param("s", $blood_type);
            $insertStmt->execute();
            $insertStmt->close();
        }

        $updateStmt->close();
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>