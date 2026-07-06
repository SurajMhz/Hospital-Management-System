<?php
session_start();

// Require DB
include '../DataBaseConnection/db.php';

// Require login
if (!isset($_SESSION['patient_id'])) {
    header('Location: login.php');
    exit();
}

$patient_name = isset($_SESSION['patient_name']) ? $_SESSION['patient_name'] : '';
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
    $doctor_id = (int)$_POST['doctor_id'];
    $patient_name_post = trim($_POST['patient_name']);
    $age = isset($_POST['age']) ? (int)$_POST['age'] : null;
    $gender = trim($_POST['gender']);
    $phone = trim($_POST['phone']);
    $date = trim($_POST['date']);
    $department = trim($_POST['department']);
    $reason = trim($_POST['reason']);

    $source = 'Patient Form';

    $stmt = $conn->prepare("INSERT INTO appointments (doctor_id, patient_name, age, gender, phone, date, department, reason, source) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('isissssss', $doctor_id, $patient_name_post, $age, $gender, $phone, $date, $department, $reason, $source);
        if ($stmt->execute()) {
            $stmt->close();
            header('Location: ../../Patient/Dashboard.php');
            exit();
        } else {
            $message = 'Failed to submit appointment.';
        }
    } else {
        $message = 'Database error.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Book Appointment</title>
    <style>
        body{font-family:Segoe UI, Tahoma, Geneva, Verdana, sans-serif; background:#f5f5f5; padding:30px}
        .form-card{max-width:640px;margin:0 auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.06)}
        .form-card h2{margin-bottom:12px}
        .row{display:flex;gap:10px}
        input,select,textarea{width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;margin-bottom:10px}
        button{background:#1a8080;color:#fff;border:none;padding:10px 14px;border-radius:6px;cursor:pointer}
        .msg{color:#c62828;margin-bottom:10px}
    </style>
</head>
<body>
    <div class="form-card">
        <h2>Book an Appointment</h2>
        <?php if ($message): ?><div class="msg"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
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

            <label>Department</label>
            <input type="text" name="department" placeholder="e.g. Cardiology" required>

            <label>Reason</label>
            <textarea name="reason" rows="3" placeholder="Short reason" required></textarea>

            <div style="display:flex;gap:10px;align-items:center;justify-content:flex-end">
                <a href="login.php" style="text-decoration:none;color:#666">Cancel</a>
                <button type="submit">Send Request</button>
            </div>
        </form>
    </div>
</body>
</html>
