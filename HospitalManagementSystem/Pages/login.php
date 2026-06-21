<?php
session_start();
include '../DataBaseConnection/db.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error_message = 'Both email and password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['fullname'];

                header("Location: dashboard.php");
                exit;

            } else {
                $error_message = 'Incorrect password.';
            }

        } else {
            $error_message = 'User not found.';
        }
    }
}

if (!empty($_SESSION['flash_registered'])) {
    $success_message = $_SESSION['flash_registered'];
    unset($_SESSION['flash_registered']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Staff Login — CityMed HMS</title>

    <!-- Google Fonts: Playfair Display for headings, Inter for body -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet" />

    <!-- Page-specific stylesheet -->
    <link rel="stylesheet" href="../css/login.css" />
</head>

<body>
    <div class="split-wrapper">
        <aside class="brand-panel" aria-hidden="true">

            <!-- Hospital logo mark -->
            <div class="logo-wrap">
                <!-- Inline SVG cross icon — no external image dependency -->
                <svg class="logo-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <rect width="48" height="48" rx="12" fill="white" fill-opacity="0.15" />
                    <rect x="20" y="10" width="8" height="28" rx="3" fill="white" />
                    <rect x="10" y="20" width="28" height="8" rx="3" fill="white" />
                </svg>
                <span class="logo-text">CityMed</span>
            </div>

            <!-- Tagline -->
            <h1 class="brand-headline">Precision care,<br />seamlessly managed.</h1>
            <p class="brand-sub">The hospital management platform for doctors, staff, and patients — all in one place.
            </p>

            <!-- Decorative circles — pure CSS -->
            <div class="deco-circle deco-circle--lg" aria-hidden="true"></div>
            <div class="deco-circle deco-circle--sm" aria-hidden="true"></div>
        </aside>

        <!-- --------------------------------------------------------
             RIGHT PANEL — Login form
        -------------------------------------------------------- -->
        <main class="form-panel">

            <div class="form-card">

                <!-- Card header -->
                <header class="card-header">
                    <h2 class="card-title">Welcome back</h2>
                    <p class="card-sub">Sign in to access the HMS dashboard</p>
                </header>

                <!-- ------------------------------------------------
                     Feedback banners (PHP-generated)
                ------------------------------------------------ -->
                <?php if (!empty($error_message)): ?>
                    <!-- Error banner — shown when validation or auth fails -->
                    <div class="alert alert--error" role="alert">
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <!-- Success banner — shown after register or successful login -->
                    <div class="alert alert--success" role="status">
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <?= $success_message ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="" novalidate class="auth-form">

                    <!-- Email field -->
                    <div class="field-group">
                        <label for="email" class="field-label">Email address</label>
                        <input type="email" id="email" name="email" class="field-input" placeholder="you@example.com"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autocomplete="email" required />
                    </div>

                    <!-- Password field -->

                    <div class="field-group">
                        <div class="label-row">
                            <label for="password" class="field-label">Password</label>
                        </div>
                        <div class="input-wrap">
                            <input type="password" id="password" name="password" class="field-input"
                                placeholder="••••••••" autocomplete="current-password" required />
                            <!-- Toggle password visibility -->
                            <button type="button" class="pwd-toggle" aria-label="Show password"
                                onclick="togglePassword(this)">&#128065;</button>
                        </div>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" class="btn-primary">
                        Sign in to HMS
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                </form>

                <!-- Link to the registration page -->
                <p class="switch-link">
                    New staff member?
                    <a href="register.php">Request an account</a>
                </p>


            </div>

        </main>
    </div>

    <script>
        /**
         * togglePassword()
         * Switches the adjacent password input between text and password type,
         * giving the user a way to verify what they have typed.
         *
         * @param {HTMLButtonElement} btn - The eye icon button that was clicked.
         */
        function togglePassword(btn) {
            const input = btn.closest('.input-wrap').querySelector('input');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        }
    </script>

</body>

</html>