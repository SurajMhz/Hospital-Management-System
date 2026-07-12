<?php
session_start();

// Require DB
include '../DataBaseConnection/db.php';

// Require login
if (!isset($_SESSION['patient_id'])) {
    header('Location: login.php');
    exit();
}

$patient_id = $_SESSION['patient_id'];
$patient_name = $_SESSION['patient_name'] ?? '';
$patient_phone = isset($_SESSION['patient_phone']) ? $_SESSION['patient_phone'] : '';

// Fetch doctors
$doctors_result = $conn->query("SELECT id, fullname FROM users WHERE role='doctor'");
$doctors = [];
if ($doctors_result) {
    while ($d = $doctors_result->fetch_assoc()) {
        $doctors[] = $d;
    }
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $doctor_id = (int) $_POST['doctor_id'];

    // Get doctor's name
    $stmtDoctor = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
    $stmtDoctor->bind_param("i", $doctor_id);
    $stmtDoctor->execute();

    $doctor = $stmtDoctor->get_result()->fetch_assoc();

    if (!$doctor) {
        $message = "Doctor not found.";
    } else {

        $doctor_name = $doctor['fullname'];

        $age = !empty($_POST['age']) ? (int) $_POST['age'] : NULL;
        $gender = trim($_POST['gender']);
        $phone = trim($_POST['phone']);
        $date = $_POST['date'];
        $time = $_POST['time'];
        $department = trim($_POST['department']);
        $reason = trim($_POST['reason']);

        $source = "Patient Form";

        $stmt = $conn->prepare("
                INSERT INTO appointments
                (
                    patient_id,
                    doctor_id,
                    doctor_name,
                    appointment_date,
                    appointment_time,
                    department,
                    reason,
                    source,
                    age,
                    gender,
                    phone
                )
                VALUES
                (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
            ");

        if ($stmt) {

            $stmt->bind_param(
                "iissssssiss",
                $patient_id,
                $doctor_id,
                $doctor_name,
                $date,
                $time,
                $department,
                $reason,
                $source,
                $age,
                $gender,
                $phone
            );

            if ($stmt->execute()) {

                header("Location: ../Patient/Dashboard.php");
                exit();

            } else {

                $message = "Failed to book appointment: " . $stmt->error;

            }

        } else {

            $message = "Database error: " . $conn->error;

        }

    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Book Appointment</title>
</head>
<link rel="stylesheet" href="Patient.css">

<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Patient</h2>
            <div class="sidebar-menu">
                <a href="Dashboard.php">Dashboard</a>
                <a href="./book_appointment.php" class="active">Book Appointment</a>
                <a href="./ViewPrescription.php">View Prescription</a>
                <button type="submit" class="logout-btn" onclick="logout-btn">Logout</button>
            </div>
        </div>
        <div class="form-card">
            <h2>Book an Appointment</h2>
            <?php if ($message): ?>
                <div class="msg"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
            <form method="POST">
                <label>Choose Doctor</label>
                <select name="doctor_id" required>
                    <option value="" disabled selected>Select doctor</option>
                    <?php foreach ($doctors as $d): ?>
                        <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['fullname']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Your Full Name</label>
                <input type="text" name="patient_name" value="<?php echo htmlspecialchars($patient_name); ?>" required>

                <div class="row">
                    <div style="flex:1">
                        <label>Age</label>
                        <input type="number" name="age" min="0" max="120">
                    </div>
                    <div style="flex:1">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="" disabled selected>Select</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                </div>

                <label>Phone</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($patient_phone); ?>" required>

                <label>Date</label>
                <input type="date" name="date" required>

                <label>Time</label>
                <input type="time" name="time" required>

                <label>Department</label>
                <input type="text" name="department" placeholder="e.g. Cardiology" required>

                <label>Reason</label>
                <textarea name="reason" rows="3" placeholder="Short reason" required></textarea>

                <div style="display:flex;gap:10px;align-items:center;justify-content:flex-end">
                    <a href="./Dashboard.php" style="text-decoration:none;color:#666">Cancel</a>
                    <button type="submit">Send Request</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>