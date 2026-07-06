<?php
session_start();

// Check if patient is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: ../../Patient/Dashboard.php");
    exit();
}

// Include database connection
include '../HospitalManagementSystem/DataBaseConnection/db.php';

// Get patient name
$patient_id = $_SESSION['patient_id'];
$patient_name = isset($_SESSION['patient_name']) ? $_SESSION['patient_name'] : 'Patient';

// Fetch upcoming appointments
$upcoming_query = "SELECT appointment_id, appointment_date, appointment_time, doctor_name, department, reason, status 
                   FROM appointments 
                   WHERE patient_id = '$patient_id' 
                   AND appointment_date >= CURDATE() 
                   ORDER BY appointment_date ASC, appointment_time ASC";
$upcoming_result = mysqli_query($conn, $upcoming_query);
$upcoming_appointments = mysqli_fetch_all($upcoming_result, MYSQLI_ASSOC);

// Fetch past appointments
$past_query = "SELECT appointment_id, appointment_date, appointment_time, doctor_name, department, reason, status 
               FROM appointments 
               WHERE patient_id = '$patient_id' 
               AND appointment_date < CURDATE() 
               ORDER BY appointment_date DESC, appointment_time DESC";
$past_result = mysqli_query($conn, $past_query);
$past_appointments = mysqli_fetch_all($past_result, MYSQLI_ASSOC);

// Get current date
$current_date = date('l, d F Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            background-color: #1a8080;
            color: white;
            width: 250px;
            padding: 30px 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            font-size: 24px;
            margin-bottom: 40px;
            font-weight: 600;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sidebar-menu a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 12px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar-menu a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.3);
            font-weight: 600;
        }

        .logout-btn {
            margin-top: auto;
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 12px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            text-align: left;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #c9302c;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .header h1 {
            font-size: 28px;
            color: #333;
        }

        .header p {
            color: #999;
            font-size: 14px;
            margin-top: 5px;
        }

        .header-right {
            text-align: right;
        }

        .profile-circle {
            width: 50px;
            height: 50px;
            background-color: #1a8080;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }

        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            text-align: center;
            border-left: 5px solid #1a8080;
        }

        .stat-card h3 {
            color: #999;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 36px;
            color: #1a8080;
            font-weight: bold;
        }

        /* Appointments Section */
        .section-title {
            font-size: 22px;
            color: #333;
            margin-bottom: 20px;
            margin-top: 40px;
            font-weight: 600;
        }

        .appointments-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .table-header {
            background-color: #1a8080;
            color: white;
            padding: 20px;
            display: grid;
            grid-template-columns: 1.5fr 1.5fr 1fr 1.5fr 1fr 1fr;
            gap: 15px;
            font-weight: 600;
        }

        .table-row {
            display: grid;
            grid-template-columns: 1.5fr 1.5fr 1fr 1.5fr 1fr 1fr;
            gap: 15px;
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }

        .table-row:hover {
            background-color: #f9f9f9;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        .status-scheduled {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .status-completed {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .status-cancelled {
            background-color: #ffebee;
            color: #c62828;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }

        .date-time {
            color: #666;
        }

        .doctor-dept {
            color: #666;
        }

        .reason {
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 200px;
                padding: 20px 15px;
            }

            .main-content {
                padding: 20px;
            }

            .table-header,
            .table-row {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 20px;
            }

            .sidebar-menu {
                flex-direction: row;
                gap: 10px;
                flex-wrap: wrap;
            }

            .sidebar-menu a {
                flex: 1;
                min-width: 100px;
            }

            .main-content {
                padding: 15px;
            }

            .header {
                flex-direction: column;
                gap: 15px;
                text-align: left;
            }

            .header-right {
                text-align: left;
            }

            .table-header,
            .table-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .table-header {
                display: none;
            }

            .table-row {
                background-color: #f9f9f9;
                margin-bottom: 15px;
                border: 1px solid #e0e0e0;
            }

            .table-row::before {
                content: attr(data-label);
                font-weight: 600;
                color: #1a8080;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Patient</h2>
            <div class="sidebar-menu">
                <a href="Dashboard.php" class="active">Dashboard</a>
                <a href="../HospitalManagementSystem/Pages/book_appointment.php">Book Appointment</a>
            </div>
            <form method="POST" action="logout.php" style="margin-top: 40px;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div>
                    <h1>Welcome, <?php echo htmlspecialchars($patient_name); ?></h1>
                    <p><?php echo $current_date; ?></p>
                </div>
                <div class="header-right">
                    <div class="profile-circle"><?php echo strtoupper(substr($patient_name, 0, 1)); ?></div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Appointments</h3>
                    <div class="number"><?php echo count($upcoming_appointments) + count($past_appointments); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Upcoming</h3>
                    <div class="number"><?php echo count($upcoming_appointments); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Past</h3>
                    <div class="number"><?php echo count($past_appointments); ?></div>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <h2 class="section-title">Upcoming Appointments</h2>
            <div class="appointments-container">
                <?php if (!empty($upcoming_appointments)): ?>
                    <div class="table-header">
                        <div>Date & Time</div>
                        <div>Doctor</div>
                        <div>Department</div>
                        <div>Reason</div>
                        <div>Status</div>
                    </div>
                    <?php foreach ($upcoming_appointments as $appointment): ?>
                        <div class="table-row">
                            <div class="date-time">
                                <?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?><br>
                                <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?>
                            </div>
                            <div class="doctor-dept"><?php echo htmlspecialchars($appointment['doctor_name']); ?></div>
                            <div><?php echo htmlspecialchars($appointment['department']); ?></div>
                            <div class="reason"><?php echo htmlspecialchars($appointment['reason']); ?></div>
                            <div>
                                <span class="status-badge status-scheduled">
                                    <?php echo htmlspecialchars($appointment['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data">No upcoming appointments</div>
                <?php endif; ?>
            </div>

            <!-- Past Appointments -->
            <h2 class="section-title">Past Appointments</h2>
            <div class="appointments-container">
                <?php if (!empty($past_appointments)): ?>
                    <div class="table-header">
                        <div>Date & Time</div>
                        <div>Doctor</div>
                        <div>Department</div>
                        <div>Reason</div>
                        <div>Status</div>
                    </div>
                    <?php foreach ($past_appointments as $appointment): ?>
                        <div class="table-row">
                            <div class="date-time">
                                <?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?><br>
                                <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?>
                            </div>
                            <div class="doctor-dept"><?php echo htmlspecialchars($appointment['doctor_name']); ?></div>
                            <div><?php echo htmlspecialchars($appointment['department']); ?></div>
                            <div class="reason"><?php echo htmlspecialchars($appointment['reason']); ?></div>
                            <div>
                                <span class="status-badge status-completed">
                                    <?php echo htmlspecialchars($appointment['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data">No past appointments</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
