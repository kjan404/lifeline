<?php
include 'db_donor_and_requests.php'; // Include database connection
error_reporting(E_ALL); ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $blood_type = $_POST['blood_type'];
    $contact_number = $_POST['contact_number'];
    $hospital_location = $_POST['hospital_location'];
    $reason = $_POST['reason'];
    $urgency_level = $_POST['urgency_level'];
    $request_date = $_POST['request_date'];

    // Insert data into database
    $stmt = $conn->prepare ("INSERT INTO recepient_list (full_name, age, gender, blood_type, contact_number, hospital_location, reason, urgency_level, request_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssssss", $full_name, $age, $gender, $blood_type, $contact_number, $hospital_location, $reason, $urgency_level, $request_date);

    if ($stmt->execute()) {
        echo "Success";
        header("Location: home.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
