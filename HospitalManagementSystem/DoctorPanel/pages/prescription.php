<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Pages/login.php");
    exit;
}

$doctor_id = $_SESSION['user_id'];
$doctor_name = $_SESSION['user_name'];
$saved_msg = '';

// SAVE / UPDATE PRESCRIPTION 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {

    $apt_id = (int) $_POST['apt_id'];
    $pat_name = trim($_POST['patient_name']);
    $diagnosis = trim($_POST['diagnosis']);
    $medicine = trim($_POST['medicine']);
    $dosage = trim($_POST['dosage']);
    $duration = trim($_POST['duration']);
    $notes = trim($_POST['notes']);

    // Check if a prescription already exists for this appointment
    $chk = $conn->prepare("SELECT id FROM prescriptions WHERE appointment_id = ? AND doctor_id = ?");
    $chk->bind_param("ii", $apt_id, $doctor_id);
    $chk->execute();
    $exists = $chk->get_result()->fetch_assoc();

    if ($exists) {
        // UPDATE — prescription already exists, just overwrite fields
        $stmt = $conn->prepare("UPDATE prescriptions 
                                SET diagnosis=?, medicine=?, dosage=?, duration=?, notes=?
                                WHERE appointment_id=? AND doctor_id=?");
        $stmt->bind_param("sssssii", $diagnosis, $medicine, $dosage, $duration, $notes, $apt_id, $doctor_id);
    } else {
        // INSERT — first time prescribing for this appointment
        $stmt = $conn->prepare("INSERT INTO prescriptions 
                               (doctor_id, appointment_id, patient_name, diagnosis, medicine, dosage, duration, notes)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissssss", $doctor_id, $apt_id, $pat_name, $diagnosis, $medicine, $dosage, $duration, $notes);
    }

    $stmt->execute();
    header("Location: prescription.php?apt_id=$apt_id&saved=1");
    exit;
}

//  LOAD APPOINTMENTS FOR DROPDOWN
$apts = $conn->query("
SELECT
    a.appointment_id,
    a.appointment_date,
    a.appointment_time,
    a.department,
    u.fullname AS patient_name
FROM appointments a
JOIN users u ON a.patient_id = u.id
WHERE a.doctor_id = $doctor_id
ORDER BY a.appointment_date DESC
");

//  IF AN APPOINTMENT IS SELECTED, LOAD IT 
$selected_apt_id = isset($_GET['apt_id']) ? (int) $_GET['apt_id'] : 0;
$existing = null;   // existing prescription (if any)
$apt_info = null;   // selected appointment details

if ($selected_apt_id) {

    // Get appointment info (patient name, date, etc.)
    $s = $conn->prepare("SELECT a.*, u.fullname AS patient_name
FROM appointments a
JOIN users u ON a.patient_id = u.id
WHERE a.appointment_id = ? AND a.doctor_id = ?");
    $s->bind_param("ii", $selected_apt_id, $doctor_id);
    $s->execute();
    $apt_info = $s->get_result()->fetch_assoc();

    // Get existing prescription for this appointment if it exists
    $p = $conn->prepare("SELECT * FROM prescriptions WHERE appointment_id = ? AND doctor_id = ?");
    $p->bind_param("ii", $selected_apt_id, $doctor_id);
    $p->execute();
    $existing = $p->get_result()->fetch_assoc();
}

$saved_msg = isset($_GET['saved']) ? 'Prescription saved successfully!' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Prescription</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        .presc-card {
            background: white;
            border-radius: 10px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            max-width: 650px;
        }

        .presc-card label {
            display: block;
            font-size: 13px;
            color: #555;
            margin-bottom: 4px;
            margin-top: 14px;
            font-weight: 600;
        }

        .presc-card input,
        .presc-card textarea,
        .presc-card select {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
        }

        .presc-card textarea {
            resize: vertical;
        }

        .btn-save {
            margin-top: 18px;
            background: #0B7C75;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-save:hover {
            background: #096b65;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .badge-existing {
            background: #fef3c7;
            color: #92400e;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 5px;
            margin-left: 8px;
        }

        .apt-select-wrap {
            background: white;
            border-radius: 10px;
            padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            max-width: 650px;
            margin-bottom: 20px;
        }

        .apt-select-wrap label {
            font-size: 13px;
            font-weight: 600;
            color: #555;
            display: block;
            margin-bottom: 6px;
        }

        .apt-select-wrap select {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .hint {
            font-size: 12px;
            color: #888;
            margin-top: 4px;
        }
    </style>
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

            <div class="topbar">
                <h3 style="margin:0;">Prescription Module</h3>
                <p style="margin:0; font-size:13px; color:#888;">Select a patient then fill or update their prescription
                </p>
            </div>

            <?php if ($saved_msg): ?>
                <div
                    style="background:#d1fae5; color:#065f46; padding:12px 18px; border-radius:8px; margin-bottom:16px; font-size:14px;">
                    Prescription saved successfully!
                </div>
            <?php endif; ?>

            <!-- STEP 1: SELECT PATIENT FROM APPOINTMENTS -->
            <div class="apt-select-wrap">
                <label>Step 1 — Select a Patient (from your appointments)</label>
                <select id="aptDropdown" onchange="goToPatient(this.value)">
                    <option value="">-- Choose patient --</option>
                    <?php
                    // Reset result pointer so we can loop again
                    $apts->data_seek(0);
                    while ($row = $apts->fetch_assoc()):
                        ?>
                        <option value="<?= $row['appointment_id'] ?>" <?= $selected_apt_id == $row['appointment_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['patient_name']) ?>
                            —
                            <?= htmlspecialchars($row['appointment_date']) ?>
                            <?= date('h:i A', strtotime($row['appointment_time'])) ?>
                            (<?= htmlspecialchars($row['department']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
                <p class="hint">Choosing a patient loads their info and any existing prescription below.</p>
            </div>

            <!-- PRESCRIPTION FORM — only shows after a patient is selected -->
            <?php if ($apt_info): ?>
                <?php if ($apt_info['status'] === 'Cancelled'): ?>
                    <div
                        style="background:#fee2e2; color:#dc2626; padding:16px 20px; border-radius:8px; max-width:650px; font-size:14px;">
                        This appointment was <strong>cancelled</strong>. Prescriptions cannot be added or edited for cancelled
                        appointments.
                    </div>
                <?php else: ?>
                    <div class="presc-card">

                        <div
                            style="display:flex; align-items:center; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid #eee;">
                            <div
                                style="width:44px; height:44px; border-radius:50%; background:#0B7C75; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:15px; margin-right:12px;">
                                <?= strtoupper(substr($apt_info['patient_name'], 0, 2)) ?>
                            </div>
                            <div>
                                <div style="font-weight:bold; font-size:16px;">
                                    <?= htmlspecialchars($apt_info['patient_name']) ?>
                                </div>
                                <div style="font-size:12px; color:#888;">
                                    <?= htmlspecialchars($apt_info['age']) ?> yrs ·
                                    <?= htmlspecialchars($apt_info['gender']) ?> ·
                                    <?= htmlspecialchars($apt_info['department']) ?>
                                </div>
                            </div>
                            <?php if ($existing): ?>
                                <span class="badge-existing">✏️ Existing Prescription — Editing</span>
                            <?php endif; ?>
                        </div>

                        <!-- THE FORM -->
                        <form method="POST" action="prescription.php">
                            <!-- Hidden fields carry the appointment ID and patient name -->
                            <input type="hidden" name="apt_id" value="<?= $apt_info['appointment_id'] ?>">
                            <input type="hidden" name="patient_name" value="<?= htmlspecialchars($apt_info['patient_name']) ?>">

                            <label>Diagnosis</label>
                            <input type="text" name="diagnosis" placeholder="e.g. Hypertension, Viral Fever"
                                value="<?= htmlspecialchars($existing['diagnosis'] ?? '') ?>" required>

                            <label>
                                Medicine
                                <?php if ($existing): ?>
                                    <span style="font-weight:400; color:#888;">(previous medicines shown — add new ones
                                        below)</span>
                                <?php endif; ?>
                            </label>
                            <textarea name="medicine" rows="5" placeholder="e.g. Paracetamol 500mg&#10;Amoxicillin 250mg"
                                required><?= htmlspecialchars($existing['medicine'] ?? '') ?></textarea>

                            <label>Dosage</label>
                            <input type="text" name="dosage" placeholder="e.g. 1-0-1, 1-1-1"
                                value="<?= htmlspecialchars($existing['dosage'] ?? '') ?>" required>

                            <label>Duration</label>
                            <input type="text" name="duration" placeholder="e.g. 5 days, 2 weeks"
                                value="<?= htmlspecialchars($existing['duration'] ?? '') ?>" required>

                            <label>Doctor Notes <span style="font-weight:400; color:#888;">(optional)</span></label>
                            <textarea name="notes" rows="3"
                                placeholder="Any extra instructions, follow-up date, etc."><?= htmlspecialchars($existing['notes'] ?? '') ?></textarea>

                            <button type="submit" name="save" class="btn-save">
                                <?= $existing ? '💾 Update Prescription' : '+ Save Prescription' ?>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>

    <script>
        // When doctor picks a patient from dropdown, reload the page with ?apt_id=X
        function goToPatient(id) {
            if (id) {
                window.location.href = 'prescription.php?apt_id=' + id;
            }
        }
    </script>

</body>

</html>