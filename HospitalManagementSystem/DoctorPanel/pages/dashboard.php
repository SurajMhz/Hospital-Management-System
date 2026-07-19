<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Pages/login.php");
    exit;
}

$doctor_id = $_SESSION['user_id'];
$doctor_name = $_SESSION['user_name'];

// ADD APPOINTMENT
if (isset($_POST['add'])) {

    $patient_id = (int) $_POST['patient_id']; // FIXED
    $phone = trim($_POST['phone']);
    $date = trim($_POST['date']);
    $department = trim($_POST['department']);
    $reason = trim($_POST['reason']);
    $source = 'Manual';

    $stmt = $conn->prepare("
    INSERT INTO appointments
    (patient_id, doctor_id, doctor_name, appointment_date, department, reason, source, phone)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

    $stmt->bind_param(
        "iissssss",
        $patient_id,
        $doctor_id,
        $doctor_name,
        $date,
        $department,
        $reason,
        $source,
        $phone
    );
    $stmt->execute();
}

// DELETE APPOINTMENT
if (isset($_POST['delete'])) {

    $id = (int) $_POST['delete'];

    // Remove related prescription first
    $stmt = $conn->prepare("
        DELETE FROM prescriptions 
        WHERE appointment_id = ? AND doctor_id = ?
    ");
    $stmt->bind_param("ii", $id, $doctor_id);
    $stmt->execute();


    // Then remove appointment
    $stmt = $conn->prepare("
        DELETE FROM appointments 
        WHERE appointment_id = ? AND doctor_id = ?
    ");
    $stmt->bind_param("ii", $id, $doctor_id);
    $stmt->execute();
}

// CANCEL APPOINTMENT
if (isset($_POST['cancel'])) {
    $id = (int) $_POST['cancel'];
    $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE appointment_id = ? AND doctor_id = ?");
    $stmt->bind_param("ii", $id, $doctor_id);
    $stmt->execute();
}

// CHANGE STATUS
if (isset($_POST['status'])) {
    $id = (int) $_POST['status'];

    $stmt = $conn->prepare("SELECT status FROM appointments WHERE appointment_id = ? AND doctor_id = ?");
    $stmt->bind_param("ii", $id, $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        $current = $data['status'];

        $new_status =
            $current === 'Scheduled' ? 'Completed' :
            ($current === 'Completed' ? 'Cancelled' : 'Scheduled');

        $stmt2 = $conn->prepare("
        UPDATE appointments 
        SET status=? 
        WHERE appointment_id=? AND doctor_id=?
    ");

        $stmt2->bind_param("sii", $new_status, $id, $doctor_id);
        $stmt2->execute();
    }

    $new_status = $current === 'Scheduled' ? 'Completed' : ($current === 'Completed' ? 'Cancelled' : 'Scheduled');

    $stmt2 = $conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ? AND doctor_id = ?");
    $stmt2->bind_param("sii", $new_status, $id, $doctor_id);
    $stmt2->execute();
}

// GET ALL APPOINTMENTS
$appointments = $conn->query("
    SELECT 
        a.*,
        u.fullname,
        u.age,
        u.gender
    FROM appointments a
    JOIN users u ON u.id = a.patient_id
    WHERE a.doctor_id = $doctor_id
    ORDER BY a.created_at DESC
");
// STATS
$total = $conn->query("SELECT COUNT(*) as c FROM appointments WHERE doctor_id=$doctor_id")->fetch_assoc()['c'];
$scheduled = $conn->query("SELECT COUNT(*) as c FROM appointments WHERE doctor_id=$doctor_id AND status='Scheduled'")->fetch_assoc()['c'];
$completed = $conn->query("SELECT COUNT(*) as c FROM appointments WHERE doctor_id=$doctor_id AND status='Completed'")->fetch_assoc()['c'];
$cancelled = $conn->query("SELECT COUNT(*) as c FROM appointments WHERE doctor_id=$doctor_id AND status='Cancelled'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>

    <div class="container">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2>DoctorPanel</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="records.php">Records</a>
            <a href="prescription.php">Prescription</a>
            <a href="profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>

        <!-- MAIN -->
        <div class="main">

            <div class="topbar" style="display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <h3 style="margin:0;">Welcome, <?= htmlspecialchars($doctor_name) ?></h3>
                    <p style="margin:0; font-size:13px; color:#888;"><?= date('l, d F Y') ?></p>
                </div>
                <div
                    style="width:38px; height:38px; border-radius:50%; background:#0B7C75; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:14px;">
                    <?= strtoupper(substr($doctor_name, 0, 2)) ?>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="cards">
                <div class="card">
                    <h3>Total</h3>
                    <p><?= $total ?></p>
                </div>
                <div class="card">
                    <h3>Scheduled</h3>
                    <p><?= $scheduled ?></p>
                </div>
                <div class="card">
                    <h3>Completed</h3>
                    <p><?= $completed ?></p>
                </div>
                <div class="card">
                    <h3>Cancelled</h3>
                    <p><?= $cancelled ?></p>
                </div>
            </div>

            <!-- ADD APPOINTMENT FORM -->
            <div class="controls">
                <p
                    style="font-size:11px; color:#888; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:10px;">
                    Add appointment manually</p>
                <form method="POST">
                    <select name="patient_id" required>
                        <option value="" disabled selected>Select Patient</option>

                        <?php
                        $patients = $conn->query("SELECT id, fullname FROM users WHERE role='patient'");
                        while ($p = $patients->fetch_assoc()):
                            ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['fullname']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <input type="tel" name="phone" placeholder="Phone (98XXXXXXXX)" required>
                    <input type="date" name="date" required>
                    <select name="department" required>
                        <option value="" disabled selected>Department</option>
                        <option>Cardiology</option>
                        <option>Neurology</option>
                        <option>Orthopedics</option>
                        <option>Pediatrics</option>
                        <option>Emergency care</option>
                    </select>
                    <input type="text" name="reason" placeholder="Reason for visit" required>
                    <button type="submit" name="add">+ Add Appointment</button>
                </form>
            </div>

            <!-- TABLE -->
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Phone</th>
                            <th>Date</th>
                            <th>Department</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Source</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $appointments->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['fullname']) ?></strong><br>
                                    <small style="color:#888;">
                                        <?= htmlspecialchars($row['age'] ?? 'N/A') ?> ·
                                        <?= htmlspecialchars($row['gender'] ?? 'N/A') ?>
                                    </small>
                                </td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                                <td><?= htmlspecialchars($row['department']) ?></td>
                                <td><?= htmlspecialchars($row['reason']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td>
                                    <?php if ($row['source'] === 'Patient Form'): ?>
                                        <span
                                            style="background:#dbeafe; color:#1e40af; font-size:11px; padding:3px 9px; border-radius:5px;">Patient
                                            Form</span>
                                    <?php else: ?>
                                        <span
                                            style="background:#f3f4f6; color:#374151; font-size:11px; padding:3px 9px; border-radius:5px;">Manual</span>
                                    <?php endif; ?>
                                </td>
                                <td style="white-space:nowrap;">
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="status" value="<?= $row['appointment_id'] ?>">
                                        <button type="submit" class="btn-action btn-change">Change</button>
                                    </form>
                                    <?php if ($row['status'] !== 'Cancelled'): ?>
                                        <form method="POST" style="display:inline">
                                            <input type="hidden" name="cancel" value="<?= $row['appointment_id'] ?>">
                                            <button type="submit" class="btn-action btn-cancel">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="delete" value="<?= $row['appointment_id'] ?>">
                                        <button type="submit" class="btn-action btn-delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>



    <script>
        if (sessionStorage.getItem('scrollY')) {
            window.scrollTo(0, +sessionStorage.getItem('scrollY'));
            sessionStorage.removeItem('scrollY');
        }

        document.querySelectorAll('form[method="POST"]').forEach(form => {
            if (form.querySelector('[name="add"]')) return;
            form.addEventListener('submit', function () {
                sessionStorage.setItem('scrollY', window.scrollY);
            });
        });
    </script>

</body>

</html>