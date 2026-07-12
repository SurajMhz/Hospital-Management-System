<?php
session_start();

// Check if patient is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: ../Pages/login.php");
    exit();
}

// Include database connection
include '../DataBaseConnection/db.php';

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

</head>
<link rel="stylesheet" href="Patient.css">

<body>

    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Patient</h2>
            <div class="sidebar-menu">
                <a href="Dashboard.php" class="active">Dashboard</a>
                <a href="./book_appointment.php">Book Appointment</a>
                <a href="./ViewPrescription.php">View Prescription</a>
                <button type="submit" class="logout-btn" onclick="logout-btn">Logout</button>
            </div>
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