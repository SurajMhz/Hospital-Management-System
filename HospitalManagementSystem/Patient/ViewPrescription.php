<?php
session_start();

// Include database connection
include '../DataBaseConnection/db.php';

// Get patient name
$patient_id = $_SESSION['patient_id'];
$patient_name = isset($_SESSION['patient_name']) ? $_SESSION['patient_name'] : 'Patient';
$current_date = date('l, d F Y');
$saved_msg = '';

// Get all prescriptions for the logged-in patient
$stmt = $conn->prepare("
SELECT
    p.*,
    a.appointment_date,
    a.appointment_time,
    a.department,
    a.doctor_name
FROM prescriptions p
JOIN appointments a
    ON p.appointment_id = a.appointment_id
WHERE a.patient_id = ?
ORDER BY p.created_at DESC
");

$stmt->bind_param("i", $patient_id);
$stmt->execute();
$prescriptions = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<link rel="stylesheet" href="./Patient.css">

<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Patient</h2>
            <div class="sidebar-menu">
                <a href="Dashboard.php">Dashboard</a>
                <a href="./book_appointment.php">Book Appointment</a>
                <a href="./ViewPrescription.php" class="active">View Prescription</a>
                <button type="submit" class="logout-btn" onclick="logout.php">Logout</button>
            </div>
        </div>

        <!-- main -->
        <div class="main-content">
            <div class="header">
                <div>
                    <h1><?php echo htmlspecialchars($patient_name); ?></h1>
                    <p><?php echo $current_date; ?></p>
                </div>
                <div class="header-right">
                    <div class="profile-circle"><?php echo strtoupper(substr($patient_name, 0, 1)); ?></div>
                </div>
            </div>
            <!-- Prescription -->
            <h2 class="section-title">Prescription</h2>
            <div class="appointments-container">
                <h2 class="section-title">My Prescriptions</h2>
                <?php if ($prescriptions->num_rows > 0): ?>

                    <?php while ($row = $prescriptions->fetch_assoc()): ?>

                        <div class="prescription-card">

                            <div class="prescription-header">
                                <div class="doctor-info">
                                    <div class="doctor-avatar">
                                        <?= strtoupper(substr($row['doctor_name'], 0, 2)) ?>
                                    </div>

                                    <div>
                                        <h3><?= htmlspecialchars($row['doctor_name']) ?></h3>
                                        <p><?= htmlspecialchars($row['department']) ?></p>
                                    </div>
                                </div>

                                <div class="appointment-date">
                                    <strong>
                                        <?= date('d M Y', strtotime($row['appointment_date'])) ?>
                                    </strong>

                                    <?php if (!empty($row['appointment_time'])): ?>
                                        <br>
                                        <?= date('h:i A', strtotime($row['appointment_time'])) ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="prescription-body">

                                <div class="detail">
                                    <span class="label">Diagnosis</span>
                                    <span class="value">
                                        <?= nl2br(htmlspecialchars($row['diagnosis'])) ?>
                                    </span>
                                </div>

                                <div class="detail">
                                    <span class="label">Medicine</span>
                                    <span class="value">
                                        <?= nl2br(htmlspecialchars($row['medicine'])) ?>
                                    </span>
                                </div>

                                <div class="detail-grid">

                                    <div class="detail">
                                        <span class="label">Dosage</span>
                                        <span class="value">
                                            <?= htmlspecialchars($row['dosage']) ?>
                                        </span>
                                    </div>

                                    <div class="detail">
                                        <span class="label">Duration</span>
                                        <span class="value">
                                            <?= htmlspecialchars($row['duration']) ?>
                                        </span>
                                    </div>

                                </div>

                                <?php if (!empty($row['notes'])): ?>

                                    <div class="detail">
                                        <span class="label">Doctor Notes</span>
                                        <span class="value">
                                            <?= nl2br(htmlspecialchars($row['notes'])) ?>
                                        </span>
                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="empty-prescription">
                        <h3>No prescriptions found</h3>
                        <p>Your doctor hasn't added any prescriptions yet.</p>
                    </div>

                <?php endif; ?>

            </div>


        </div>
    </div>

</body>

</html>