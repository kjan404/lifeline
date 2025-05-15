<?php
session_start();
include "db_connect.php"; // Your DB connection

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Fetch Blood Stock
$bloodStock = [];
$bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
$stockSql = "SELECT blood_type, units_available FROM blood_stock";
$stockResult = $conn->query($stockSql);
while ($row = $stockResult->fetch_assoc()) {
    $bloodStock[$row['blood_type']] = $row['units_available'];
}

// Fetch Users
$userRows = [];
$userSql = "SELECT full_name, age, gender, blood_type, contact_number, address, role FROM users";
$userResult = $conn->query($userSql);
while ($row = $userResult->fetch_assoc()) {
    $userRows[] = $row;
}

// Fetch Donors
$donorRows = [];
$donorSql = "SELECT full_name, age, gender, blood_type, contact_number, address, date_of_donation FROM donors";
$donorResult = $conn->query($donorSql);
while ($row = $donorResult->fetch_assoc()) {
    $donorRows[] = $row;
}

// Fetch Requests
$requestRows = [];
$requestSql = "SELECT full_name, age, gender, blood_type, contact_number, hospital_location, urgency_level FROM blood_requests";
$requestResult = $conn->query($requestSql);
while ($row = $requestResult->fetch_assoc()) {
    $requestRows[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root {
      --bg-color: #ffffff;
      --text-color: #000000;
      --card-bg: #f9f9f9;
    }

    [data-theme="dark"] {
      --bg-color: #121212;
      --text-color: #ffffff;
      --card-bg: #1e1e1e;
    }

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      display: flex;
      background-color: var(--bg-color);
      color: var(--text-color);
      transition: background-color 0.3s, color 0.3s;
    }

    /* === Sidebar === */
    .sidebar {
      width: 220px;
      background-color: #c62828;
      padding: 20px;
      height: 160vh;
      color: white;
      animation: slideIn 0.5s ease-out;
      will-change: transform, opacity;
    }

    .sidebar h2 {
      text-align: center;
    }

    .sidebar button {
      background: none;
      border: none;
      color: white;
      padding: 10px;
      width: 100%;
      text-align: left;
      cursor: pointer;
      font-size: 16px;
      margin: 10px 0;
    }

    .sidebar button:hover {
      background-color: #b71c1c;
    }

    .sidebar button.active {
      background-color:rgb(255, 0, 0);
      font-weight: bold;
      border-left: 4px solid white;
    }


    /* === Main Content === */
    .main {
      flex-grow: 1;
      padding: 20px;
    }

    .section {
      display: none;
    }

    .blood-card {
      background-color: var(--card-bg);
      padding: 15px;
      margin: 10px 0;
      border-radius: 8px;
      display: flex;
      justify-content: space-between;
      animation: fadeInUp 0.5s ease-out;
      will-change: transform, opacity;
    }

    .blood-card input {
      width: 50px;
      text-align: center;
    }

    canvas {
      max-width: 100%;
    }

    table {
      width: 100%;
      overflow-x: auto;
      display: block;
      border-collapse: collapse;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ddd;
    }


    @keyframes slideIn {
      from { transform: translateX(-30px); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Admin</h2>
    <button onclick="showSection('stock')" aria-label="View stock overview">
      <i data-lucide="bar-chart-3"></i> Stock Overview
    </button>
    <button onclick="showSection('users')" aria-label="View users">
  <i data-lucide="user"></i> User List
</button>
    <button onclick="showSection('donors')" aria-label="View donors">
      <i data-lucide="users"></i> Donors
    </button>
    <button onclick="showSection('requests')" aria-label="View requests">
      <i data-lucide="file-text"></i> Requests
    </button>
    <button onclick="showSection('messages')" aria-label="View messages">
      <i data-lucide="mail"></i> Messages
    </button>
    <button id="themeToggleBtn" onclick="toggleTheme()" aria-label="Toggle dark or light mode">
      <i data-lucide="sun-moon"></i> Dark Mode
    </button>
    <button onclick="logout()" aria-label="Logout">
      <i data-lucide="log-out"></i> Logout
    </button>
  </div>

  <div class="main">
    <div id="stock" class="section">
  <h1>Blood Stock Overview</h1>
  <?php foreach ($bloodTypes as $type): ?>
    <div class="blood-card">
      <span><?= $type ?></span>
      <input type="number" value="<?= $bloodStock[$type] ?? 0 ?>" data-blood-type="<?= $type ?>" onchange="updateChart()">
    </div>
  <?php endforeach; ?>
  <canvas id="bloodStockChart"></canvas>
</div>
    
    <div id="users" class="section">
  <h1>User List</h1>
  <table>
    <thead>
      <tr>
        <th>Full Name</th><th>Age</th><th>Gender</th><th>Blood Type</th><th>Contact Number</th><th>Address</th><th>User Roles</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($userRows as $user): ?>
        <tr>
          <td><?= htmlspecialchars($user['full_name']) ?></td>
          <td><?= htmlspecialchars($user['age']) ?></td>
          <td><?= htmlspecialchars($user['gender']) ?></td>
          <td><?= htmlspecialchars($user['blood_type']) ?></td>
          <td><?= htmlspecialchars($user['contact_number']) ?></td>
          <td><?= htmlspecialchars($user['address']) ?></td>
          <td><?= htmlspecialchars($user['role']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
 
    <div id="donors" class="section">
      <h1>Donors</h1>
      <table>
        <thead>
          <tr><th>Full Name</th><th>Age</th><th>Gender</th><th>Blood Type</th><th>Contact Number</th><th>Address</th><th>Date of Donation</th></tr>
        </thead>
        <tbody>
  <?php foreach ($donorRows as $donor): ?>
    <tr>
      <td><?= htmlspecialchars($donor['full_name']) ?></td>
      <td><?= htmlspecialchars($donor['age']) ?></td>
      <td><?= htmlspecialchars($donor['gender']) ?></td>
      <td><?= htmlspecialchars($donor['blood_type']) ?></td>
      <td><?= htmlspecialchars($donor['contact_number']) ?></td>
      <td><?= htmlspecialchars($donor['address']) ?></td>
      <td><?= htmlspecialchars($donor['date_of_donation']) ?></td>
    </tr>
  <?php endforeach; ?>
</tbody>

      </table>
    </div>

    <div id="requests" class="section">
      <h1>Requests</h1>
      <table>
        <thead>
          <tr><th>Full Name</th><th>Age</th><th>Gender</th><th>Required Blood Type</th><th>Contact Number</th><th>Hospital/Location</th><th>Urgency Level</th></tr>
        </thead>
       <tbody>
  <?php foreach ($requestRows as $request): ?>
    <tr>
      <td><?= htmlspecialchars($request['full_name']) ?></td>
      <td><?= htmlspecialchars($request['age']) ?></td>
      <td><?= htmlspecialchars($request['gender']) ?></td>
      <td><?= htmlspecialchars($request['blood_type']) ?></td>
      <td><?= htmlspecialchars($request['contact_number']) ?></td>
      <td><?= htmlspecialchars($request['hospital_location']) ?></td>
      <td><?= htmlspecialchars($request['urgency_level']) ?></td>
    </tr>
  <?php endforeach; ?>
</tbody>

      </table>
    </div>
  </div>

  <script>
    let bloodChart;

    document.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();
      showSection('stock');
      initChart();
    });

    function showSection(id) {
  // Hide all sections
  document.querySelectorAll('.section').forEach(section => {
    section.style.display = 'none';
  });

  // Show selected section
  document.getElementById(id).style.display = 'block';

  // Remove active class from all buttons
  document.querySelectorAll('.sidebar button').forEach(btn => {
    btn.classList.remove('active');
  });

  // Add active class to the clicked button
  const buttons = document.querySelectorAll('.sidebar button');
  buttons.forEach(button => {
    if (button.getAttribute('onclick')?.includes(`showSection('${id}'`)) {
      button.classList.add('active');
    }
  });
}


    function logout() {
  window.location.href = 'admin_logout.php';
}


    function toggleTheme() {
      const body = document.body;
      const isDark = body.getAttribute('data-theme') === 'dark';
      body.setAttribute('data-theme', isDark ? 'light' : 'dark');

      document.getElementById('themeToggleBtn').innerHTML = 
  `<i data-lucide="sun-moon"></i> ${isDark ? 'Dark Mode' : 'Light Mode'}`;

      
      lucide.createIcons();
      updateChartColors();
    }

    function updateChart() {
      const inputs = document.querySelectorAll('.blood-card input');
      const labels = [], data = [];

      inputs.forEach(input => {
        labels.push(input.getAttribute('data-blood-type'));
        data.push(parseInt(input.value) || 0);
      });

      bloodChart.data.labels = labels;
      bloodChart.data.datasets[0].data = data;
      bloodChart.update();
    }

    function updateChartColors() {
      const isDark = document.body.getAttribute('data-theme') === 'dark';
      bloodChart.options.plugins.title.color = isDark ? '#fff' : '#000';
      bloodChart.options.scales.y.ticks.color = isDark ? '#eee' : '#333';
      bloodChart.options.scales.x.ticks.color = isDark ? '#eee' : '#333';
      bloodChart.update();
    }

    function initChart() {
  const ctx = document.getElementById('bloodStockChart').getContext('2d');
  bloodChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
      datasets: [{
        label: 'Units Available',
        data: <?= json_encode(array_map(fn($bt) => (int)($bloodStock[$bt] ?? 0), $bloodTypes)) ?>,
        backgroundColor: '#d32f2f'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        title: {
          display: true,
          text: 'Blood Stock Levels',
          colcolor: getComputedStyle(document.body).getPropertyValue('--text-color')
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: document.body.getAttribute('data-theme') === 'dark' ? '#eee' : '#333'
          }
        },
        x: {
          ticks: {
            color: document.body.getAttribute('data-theme') === 'dark' ? '#eee' : '#333'
          }
        }
      }
    }
  });
}
  </script>
</body>
</html>
