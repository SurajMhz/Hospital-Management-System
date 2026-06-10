<?php
session_start();

$errors = [];
$formData = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitise all incoming fields
    $formData = [
        'first_name' => htmlspecialchars(trim($_POST['first_name'] ?? '')),
        'last_name' => htmlspecialchars(trim($_POST['last_name'] ?? '')),
        'email' => htmlspecialchars(trim($_POST['email'] ?? '')),
        'phone' => htmlspecialchars(trim($_POST['phone'] ?? '')),
        'role' => htmlspecialchars(trim($_POST['role'] ?? '')),
        'password' => $_POST['password'] ?? '',
        'confirm_pwd' => $_POST['confirm_pwd'] ?? '',
    ];

    // Validate name fields — letters, spaces, and hyphens only
    if (empty($formData['first_name'])) {
        $errors['first_name'] = 'First name is required.';
    } elseif (!preg_match('/^[A-Za-z\s\-]+$/', $formData['first_name'])) {
        $errors['first_name'] = 'First name may only contain letters, spaces, or hyphens.';
    }

    if (empty($formData['last_name'])) {
        $errors['last_name'] = 'Last name is required.';
    } elseif (!preg_match('/^[A-Za-z\s\-]+$/', $formData['last_name'])) {
        $errors['last_name'] = 'Last name may only contain letters, spaces, or hyphens.';
    }

    // Validate email format
    if (empty($formData['email'])) {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    // Phone is optional — only validate if provided
    if (!empty($formData['phone']) && !preg_match('/^\+?[0-9\s\-\(\)]{7,20}$/', $formData['phone'])) {
        $errors['phone'] = 'Enter a valid phone number.';
    }

    // Role must be one of the two allowed values
    if (empty($formData['role']) || !in_array($formData['role'], ['doctor', 'patient'])) {
        $errors['role'] = 'Please select your role.';
    }

    // Password rules: min 8 chars, at least one uppercase and one digit
    if (empty($formData['password'])) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($formData['password']) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    } elseif
    (!preg_match('/[A-Z]/', $formData['password'])) {
        $errors['password'] = 'Password must include at least one uppercase letter.';
    } elseif (
        !preg_match(
            '/[0-9]/',
            $formData['password']
        )
    ) {
        $errors['password'] = 'Password must include at least one number.';
    }
    if
    (empty($formData['confirm_pwd'])) {
        $errors['confirm_pwd'] = 'Please confirm your password.';
    } elseif
    ($formData['password'] !== $formData['confirm_pwd']) {
        $errors['confirm_pwd'] = 'Passwords do not match.';
    }
    // If all fields are valid, redirect to login with a success flash message
    if (empty($errors)) { // TODO: hash password and insert into DB before redirecting
        // $hash=password_hash($formData['password'], PASSWORD_BCRYPT);
        $_SESSION['flash_registered'] = 'Account created! You can now sign in.';
        header('Location: login.php');
        exit;
    }
}
// Returns the previously submitted value so the form is sticky on errors 
function old(string $field): string
{
    global
    $formData;
    return $formData[$field] ?? '';
} // Returns an error CSS class for a field if it failed validation
function errorClass(string $field): string
{
    global $errors;
    return isset($errors[$field]) ? 'field-input--error'
        : '';
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Account — CityMed HMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../css./register.css" />
</head>

<body>

    <div class="page-wrapper">

        <header class="page-header">
            <div class="logo-wrap">
                <div class="logo-cross" aria-hidden="true">+</div>
                <span class="logo-text">CityMed HMS</span>
            </div>
            <a href="login.php" class="header-back-link">&#8592; Back to sign in</a>
        </header>

        <main class="register-card">

            <div class="card-intro">
                <h1 class="card-title">Create your account</h1>
                <p class="card-sub">Join CityMed HMS to manage appointments, patient records, and medical history —
                    all in one place.</p>
            </div>

            <!-- Show error count banner if validation failed -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert--error" role="alert">
                    Please fix the <?= count($errors) ?> error<?= count($errors) > 1 ? 's' : '' ?> highlighted below.
                </div>
            <?php endif; ?>

            <form method="POST" action="" novalidate class="auth-form">

                <!-- Section 1: Basic personal details -->
                <fieldset class="form-section">
                    <legend class="section-legend">
                        <span class="legend-number">1</span>
                        Personal information
                    </legend>

                    <div class="field-row">
                        <div class="field-group">
                            <label for="first_name" class="field-label">First name <span
                                    class="required">*</span></label>
                            <input type="text" id="first_name" name="first_name"
                                class="field-input <?= errorClass('first_name') ?>" placeholder="Jane"
                                value="<?= old('first_name') ?>" autocomplete="given-name" required />
                            <?php if (isset($errors['first_name'])): ?>
                                <span class="field-error" role="alert"><?= $errors['first_name'] ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="field-group">
                            <label for="last_name" class="field-label">Last name <span class="required">*</span></label>
                            <input type="text" id="last_name" name="last_name"
                                class="field-input <?= errorClass('last_name') ?>" placeholder="Smith"
                                value="<?= old('last_name') ?>" autocomplete="family-name" required />
                            <?php if (isset($errors['last_name'])): ?>
                                <span class="field-error" role="alert"><?= $errors['last_name'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="email" class="field-label">Email address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="field-input <?= errorClass('email') ?>"
                            placeholder="you@example.com" value="<?= old('email') ?>" autocomplete="email" required />
                        <?php if (isset($errors['email'])): ?>
                            <span class="field-error" role="alert"><?= $errors['email'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="field-group">
                        <label for="phone" class="field-label">
                            Phone number <span class="optional-tag">optional</span>
                        </label>
                        <input type="tel" id="phone" name="phone" class="field-input <?= errorClass('phone') ?>"
                            placeholder="+1 (555) 000-0000" value="<?= old('phone') ?>" autocomplete="tel" />
                        <?php if (isset($errors['phone'])): ?>
                            <span class="field-error" role="alert"><?= $errors['phone'] ?></span>
                        <?php endif; ?>
                    </div>
                </fieldset>

                <!-- Section 2: Role selection — Doctor or Patient -->
                <fieldset class="form-section">
                    <legend class="section-legend">
                        <span class="legend-number">2</span>
                        Your role
                    </legend>

                    <div class="field-group">
                        <label class="field-label">I am registering as <span class="required">*</span></label>
                        <div class="role-cards">

                            <label class="role-card <?= old('role') === 'doctor' ? 'role-card--selected' : '' ?>">
                                <input type="radio" name="role" value="doctor" class="role-radio"
                                    <?= old('role') === 'doctor' ? 'checked' : '' ?> />
                                <span class="role-card-title">Doctor</span>
                                <span class="role-card-desc">Manage appointments, patients &amp; medical
                                    records</span>
                            </label>

                            <label class="role-card <?= old('role') === 'patient' ? 'role-card--selected' : '' ?>">
                                <input type="radio" name="role" value="patient" class="role-radio"
                                    <?= old('role') === 'patient' ? 'checked' : '' ?> />
                                <span class="role-card-title">Patient</span>
                                <span class="role-card-desc">Book appointments &amp; view your health records</span>
                            </label>

                        </div>
                        <?php if (isset($errors['role'])): ?>
                            <span class="field-error" role="alert"><?= $errors['role'] ?></span>
                        <?php endif; ?>
                    </div>
                </fieldset>

                <!-- Section 3: Account password -->
                <fieldset class="form-section">
                    <legend class="section-legend">
                        <span class="legend-number">3</span>
                        Set your password
                    </legend>

                    <div class="field-group">
                        <label for="password" class="field-label">Password <span class="required">*</span></label>
                        <div class="input-wrap">
                            <input type="password" id="password" name="password"
                                class="field-input <?= errorClass('password') ?>" placeholder="••••••••"
                                autocomplete="new-password" required />
                            <button type="button" class="pwd-toggle" aria-label="Show password"
                                onclick="togglePassword(this)">&#128065;</button>
                        </div>
                        <span class="field-hint">Min 8 characters, one uppercase letter, one number.</span>
                        <?php if (isset($errors['password'])): ?>
                            <span class="field-error" role="alert"><?= $errors['password'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="field-group">
                        <label for="confirm_pwd" class="field-label">Confirm password <span
                                class="required">*</span></label>
                        <div class="input-wrap">
                            <input type="password" id="confirm_pwd" name="confirm_pwd"
                                class="field-input <?= errorClass('confirm_pwd') ?>" placeholder="••••••••"
                                autocomplete="new-password" required />
                            <button type="button" class="pwd-toggle" aria-label="Show password"
                                onclick="togglePassword(this)">&#128065;</button>
                        </div>
                        <?php if (isset($errors['confirm_pwd'])): ?>
                            <span class="field-error" role="alert"><?= $errors['confirm_pwd'] ?></span>
                        <?php endif; ?>
                    </div>
                </fieldset>

                <button type="submit" class="btn-primary">Create Account &rarr;</button>

                <p class="switch-link">
                    Already have an account? <a href="login.php">Sign in here</a>
                </p>

            </form>

        </main>

        <footer class="page-footer">
            <p>&copy; <?= date('Y') ?> CityMed Hospital Management System. All rights reserved.</p>
        </footer>

    </div>

    <script>
        // Toggle a password field between masked and visible
        function togglePassword(btn) {
            const input = btn.closest('.input-wrap').querySelector('input');
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.setAttribute('aria-label', input.type === 'password' ? 'Show password' : 'Hide password');
        }

        // Highlight the selected role card when the radio changes
        document.querySelectorAll('.role-radio').forEach(radio => {
            radio.addEventListener('change', () => {
                document.querySelectorAll('.role-card').forEach(c => c.classList.remove('role-card--selected'));
                if (radio.checked) radio.closest('.role-card').classList.add('role-card--selected');
            });
        });
    </script>

</body>

</html>