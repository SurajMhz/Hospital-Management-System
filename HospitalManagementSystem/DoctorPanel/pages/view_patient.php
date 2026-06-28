<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Pages/login.php");
    exit;
}

$doctor_id = $_SESSION['user_id'];

// GET PATIENT BY ID
if (!isset($_GET['id'])) {
    header("Location: records.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $id, $doctor_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

// IF NOT FOUND
if (!$patient) {
    header("Location: records.php");
    exit;
}

// Fetch prescription linked to this appointment
$presc_stmt = $conn->prepare("SELECT * FROM prescriptions WHERE appointment_id = ? AND doctor_id = ?");
$presc_stmt->bind_param("ii", $id, $doctor_id);
$presc_stmt->execute();
$prescription = $presc_stmt->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Patient</title>
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
                <h3 style="margin:0;">Patient Details</h3>
                <p style="margin:0; font-size:13px; color:#888;">
                    <a href="records.php" style="color:#0B7C75; text-decoration:none;">← Back to Records</a>
                </p>
            </div>
        </div>

        <!-- PATIENT CARD -->
        <div style="background:white; border-radius:10px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.1); max-width:600px;">

            <!-- AVATAR + NAME -->
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; padding-bottom:16px; border-bottom:1px solid #eee;">
                <div style="width:55px; height:55px; border-radius:50%; background:#0B7C75; color:white; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:bold;">
                    <?= strtoupper(substr($patient['patient_name'], 0, 2)) ?>
                </div>
                <div>
                    <div style="font-size:18px; font-weight:bold; color:#111;"><?= htmlspecialchars($patient['patient_name']) ?></div>
                    <div style="font-size:13px; color:#888;"><?= htmlspecialchars($patient['age']) ?> years · <?= htmlspecialchars($patient['gender']) ?></div>
                </div>
            </div>

            <!-- DETAILS -->
            <table style="width:100%; border-collapse:collapse; font-size:14px; table-layout:fixed; word-wrap:break-word;">
                <tr style="border-bottom:1px solid #f0f0f0;">
                    <td style="padding:10px 0; color:#888; width:140px;">Phone</td>
                    <td style="padding:10px 0; color:#111;"><?= htmlspecialchars($patient['phone']) ?></td>
                </tr>
                <tr style="border-bottom:1px solid #f0f0f0;">
                    <td style="padding:10px 0; color:#888;">Appointment Date</td>
                    <td style="padding:10px 0; color:#111;"><?= htmlspecialchars($patient['date']) ?></td>
                </tr>
                <tr style="border-bottom:1px solid #f0f0f0;">
                    <td style="padding:10px 0; color:#888;">Department</td>
                    <td style="padding:10px 0; color:#111;"><?= htmlspecialchars($patient['department']) ?></td>
                </tr>
                <tr style="border-bottom:1px solid #f0f0f0;">
                    <td style="padding:10px 0; color:#888;">Reason</td>
                    <td style="padding:10px 0; color:#111;"><?= htmlspecialchars($patient['reason']) ?></td>
                </tr>
                <tr style="border-bottom:1px solid #f0f0f0;">
                    <td style="padding:10px 0; color:#888;">Status</td>
                    <td style="padding:10px 0;">
                        <?php
                        if($patient['status'] === 'Scheduled') {
                            echo '<span style="background:#fef3c7; color:#92400e; font-size:12px; padding:3px 10px; border-radius:5px;">Scheduled</span>';
                        } elseif($patient['status'] === 'Completed') {
                            echo '<span style="background:#d1fae5; color:#065f46; font-size:12px; padding:3px 10px; border-radius:5px;">Completed</span>';
                        } else {
                            echo '<span style="background:#fee2e2; color:#dc2626; font-size:12px; padding:3px 10px; border-radius:5px;">Cancelled</span>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding:10px 0; color:#888;">Source</td>
                    <td style="padding:10px 0;">
                        <?php if($patient['source'] === 'Patient Form'): ?>
                            <span style="background:#dbeafe; color:#1e40af; font-size:12px; padding:3px 10px; border-radius:5px;">Patient Form</span>
                        <?php else: ?>
                            <span style="background:#f3f4f6; color:#374151; font-size:12px; padding:3px 10px; border-radius:5px;">Manual</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <!-- PRESCRIPTION SECTION -->
        <div style="margin-top:24px; padding-top:20px; border-top:2px solid #f0f0f0;">
        <h4 style="margin-bottom:14px; color:#0B7C75;">Prescription</h4>

            <?php if ($prescription): ?>
            <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 0; color:#888; width:140px;">Diagnosis</td>
                <td><?= htmlspecialchars($prescription['diagnosis']) ?></td>
            </tr>
            <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 0; color:#888;">Medicine</td>
                <td style="white-space:pre-wrap; word-break:break-word; overflow-wrap:anywhere;"><?= htmlspecialchars($prescription['medicine']) ?></td>
            </tr>
            <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 0; color:#888;">Dosage</td>
                <td><?= htmlspecialchars($prescription['dosage']) ?></td>
            </tr>
            <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 0; color:#888;">Duration</td>
                <td><?= htmlspecialchars($prescription['duration']) ?></td>
            </tr>
            <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 0; color:#888;">Notes</td>
                <td><?= htmlspecialchars($prescription['notes'] ?: '—') ?></td>
            </tr>
            <tr>
                <td style="padding:10px 0; color:#888;">Last Updated</td>
                <td style="font-size:12px; color:#888;"><?= $prescription['updated_at'] ?></td>
            </tr>
             </table>

            <a href="prescription.php?apt_id=<?= $patient['id'] ?>"
           style="display:inline-block; margin-top:16px; background:#0B7C75; color:white; padding:8px 16px; border-radius:6px; font-size:13px; text-decoration:none;">
             Edit Prescription
            </a>

         <?php else: ?>
        <p style="color:#888; font-size:14px;">No prescription added yet.</p>
        <a href="prescription.php?apt_id=<?= $patient['id'] ?>"
           style="display:inline-block; margin-top:8px; background:#0B7C75; color:white; padding:8px 16px; border-radius:6px; font-size:13px; text-decoration:none;">
            + Add Prescription
        </a>
        <?php endif; ?>
    </div>

        </div>

    </div>
</div>

</body>
</html>