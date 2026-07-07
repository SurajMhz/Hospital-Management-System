<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Pages/login.php");
    exit;
}

$doctor_id = $_SESSION['user_id'];
$doctor_name = $_SESSION['user_name'];

// DELETE
if (isset($_POST['delete'])) {
    $id = (int) $_POST['delete'];
    $stmt = $conn->prepare("DELETE FROM appointments WHERE appointment_id = ? AND doctor_id = ?");
    $stmt->bind_param("ii", $id, $doctor_id);
    $stmt->execute();
    header("Location: records.php");
    exit;
}

// GET ALL APPOINTMENTS
$records = $conn->query("
SELECT
    a.*,
    u.fullname AS patient_name
FROM appointments a
JOIN users u
ON a.patient_id = u.id
WHERE a.doctor_id = $doctor_id
ORDER BY a.appointment_date DESC,
         a.appointment_time DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Patient Records</title>
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
                    <h3 style="margin:0;">Patient Records</h3>
                    <p style="margin:0; font-size:13px; color:#888;">Welcome back, Dr.
                        <?= htmlspecialchars($doctor_name) ?>
                    </p>
                </div>
                <div
                    style="width:38px; height:38px; border-radius:50%; background:#0B7C75; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:14px;">
                    <?= strtoupper(substr($doctor_name, 0, 2)) ?>
                </div>
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
                        <?php while ($row = $records->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['patient_name']) ?></strong><br>
                                    <small style="color:#888;"><?= htmlspecialchars($row['age']) ?> ·
                                        <?= htmlspecialchars($row['gender']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td>
                                    <?= htmlspecialchars($row['appointment_date']) ?><br>
                                    <small><?= date('h:i A', strtotime($row['appointment_time'])) ?></small>
                                </td>
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
                                <td>
                                    <?php if ($row['status'] === 'Cancelled'): ?>
                                        <form method="POST" style="display:inline"
                                            onsubmit="return confirm('Delete this record?')">
                                            <input type="hidden" name="delete" value="<?= $row['appointment_id'] ?>">
                                            <button type="submit" class="btn-action btn-delete">Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <a href="view_patient.php?id=<?= $row['appointment_id'] ?>"
                                            class="btn-action btn-delete">View</a>
                                        <form method="POST" style="display:inline"
                                            onsubmit="return confirm('Delete this record?')">
                                            <input type="hidden" name="delete" value="<?= $row['appointment_id'] ?>">
                                            <button type="submit" class="btn-action btn-delete">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>

</html>