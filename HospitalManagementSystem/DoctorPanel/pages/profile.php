<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Pages/login.php");
    exit;
}

$doctor_id = $_SESSION['user_id'];

// GET DOCTOR INFO FROM USERS TABLE
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$doctor = $stmt->get_result()->fetch_assoc();

// GET DOCTOR PROFILE
$stmt2 = $conn->prepare("SELECT * FROM doctor_profile WHERE user_id = ?");
$stmt2->bind_param("i", $doctor_id);
$stmt2->execute();
$profile = $stmt2->get_result()->fetch_assoc();

// UPDATE SPECIALIZATION
if (isset($_POST['update_profile'])) {
    $specialization = trim($_POST['specialization']);

    if ($profile) {
        $stmt3 = $conn->prepare("UPDATE doctor_profile SET specialization = ? WHERE user_id = ?");
        $stmt3->bind_param("si", $specialization, $doctor_id);
        $stmt3->execute();
    } else {
        $stmt3 = $conn->prepare("INSERT INTO doctor_profile (user_id, specialization) VALUES (?, ?)");
        $stmt3->bind_param("is", $doctor_id, $specialization);
        $stmt3->execute();
    }

    header("Location: profile.php");
    exit;
}

// CHANGE PASSWORD
$password_error = '';
$password_success = '';

if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (password_verify($current, $doctor['password'])) {
        if ($new === $confirm) {
            if (strlen($new) >= 8) {
                $hashed = password_hash($new, PASSWORD_BCRYPT);
                $stmt4 = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt4->bind_param("si", $hashed, $doctor_id);
                $stmt4->execute();
                $password_success = 'Password changed successfully!';
            } else {
                $password_error = 'New password must be at least 8 characters.';
            }
        } else {
            $password_error = 'New passwords do not match.';
        }
    } else {
        $password_error = 'Current password is incorrect.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Profile</title>
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

        <div class="topbar">
            <h3>Doctor Profile</h3>
        </div>

        <!-- DOCTOR INFO -->
        <div class="card" style="padding:20px; margin-bottom:20px;">
            <h3>Personal Information</h3><br>
            <p><strong>Name:</strong> <?= htmlspecialchars($doctor['fullname']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($doctor['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($doctor['phone']) ?></p>
            <p><strong>Specialization:</strong> <?= htmlspecialchars($profile['specialization'] ?? 'Not set') ?></p>
        </div>

        <!-- UPDATE SPECIALIZATION -->
        <div class="card" style="padding:20px; margin-bottom:20px;">
            <h3>Update Specialization</h3><br>
            <div id="spec-display" style="display:flex; align-items:center; justify-content:center; gap:12px;">
                <span id="spec-text"><?= htmlspecialchars($profile['specialization'] ?? 'Not set') ?></span>
                <button type="button" onclick="toggleSpec()" class="btn-action btn-change">Edit</button>
            </div>
            <form method="POST" id="spec-form" style="display:none; margin-top:10px; text-align:center;">
                <input type="text" name="specialization" placeholder="e.g Cardiologist" value="<?= htmlspecialchars($profile['specialization'] ?? '') ?>" required>
                <button type="submit" name="update_profile">Update</button>
                <button type="button" onclick="toggleSpec()" style="margin-left:6px;">Cancel</button>
            </form>
        </div>

        <!-- CHANGE PASSWORD -->
        <div class="card" style="padding:20px;">
            <h3>Change Password</h3><br>
            <?php if ($password_error): ?>
                <p style="color:red;"><?= $password_error ?></p>
            <?php endif; ?>
            <?php if ($password_success): ?>
                <p style="color:green;"><?= $password_success ?></p>
            <?php endif; ?>
            <button type="button" onclick="togglePwd()" id="pwd-toggle-btn" class="btn-action btn-change">Change Password</button>
            <form method="POST" id="pwd-form" style="display:none; margin-top:14px;">
                <input type="password" name="current_password" placeholder="Current Password" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                <button type="submit" name="change_password">Update Password</button>
                <button type="button" onclick="togglePwd()" style="margin-left:6px;">Cancel</button>
            </form>
        </div>

    </div>
</div>

<script>
function toggleSpec() {
    const display = document.getElementById('spec-display');
    const form    = document.getElementById('spec-form');
    const hidden  = form.style.display === 'none';
    form.style.display    = hidden ? 'block' : 'none';
    display.style.display = hidden ? 'none'  : 'flex';
}

function togglePwd() {
    const form = document.getElementById('pwd-form');
    const btn  = document.getElementById('pwd-toggle-btn');
    const hidden = form.style.display === 'none';
    form.style.display = hidden ? 'block' : 'none';
    btn.style.display  = hidden ? 'none'  : 'inline-block';
}
</script>

</body>
</html>