<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Izifiso Team</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #0f172a; 
            --accent: #2563eb;
            --bg-light: #f8fafc;
            --border: #e2e8f0;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #fff;
        }

        .split-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .brand-side {
            flex: 1;
            background: var(--primary);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
            position: relative;
        }

        .brand-side::after {
            content: "";
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 300px;
            height: 300px;
            background: rgba(37, 99, 235, 0.1);
            border-radius: 50%;
        }

        .form-side {
            flex: 1.5;
            overflow-y: auto;
            background-color: #ffffff;
            padding: 60px 10%;
            display: flex;
            flex-direction: column;
        }

        .form-header { margin-bottom: 40px; }
        .form-header h2 { font-weight: 700; font-size: 2rem; color: var(--primary); }

        label {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: #64748b;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 1.5px solid var(--border);
            padding: 12px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.05);
        }

        /* --- Password Toggle CSS --- */
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-container input {
            padding-right: 45px; /* Space for the eye */
        }

        .toggle-password-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            height: 100%;
            z-index: 10;
        }

        .toggle-password-btn:hover {
            color: var(--accent);
        }

        .btn-register {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 14px;
            font-weight: 600;
            border-radius: 8px;
            margin-top: 20px;
        }

        .alert-custom {
            border-radius: 12px;
            border: none;
            background-color: #fff1f2;
            color: #be123c;
        }

        .field-error {
            font-size: .875rem;
            color: #b91c1c;
            margin-top: .35rem;
        }

        @media (max-width: 992px) {
            .brand-side { display: none; }
            .form-side { padding: 40px 20px; }
        }
    </style>
</head>
<body>

<div class="split-container">
    <div class="brand-side">
        <h1 class="display-4 fw-bold"> 
            <img src="/assets/image.jpg" width="70" height="70" class="me-2 rounded-circle" alt="Izifiso Logo"> 
            <span class="text-warning"> Izifiso</span> Team
        </h1>
        <p class="lead opacity-75">Connect with the team, manage your projects, and streamline your workflow in one central hub.</p>
        <div class="mt-auto">
            <small class="opacity-50">© 2026 Izifiso Team Portal. All rights reserved.</small>
        </div>
    </div>

    <div class="form-side">
        <div class="form-header">
            <h2>Create Account</h2>
            <p class="text-muted">Enter your professional details to get started.</p>
        </div>

        <?php
            // Get validation errors (set by controller with ->withInput()->with('errors', ...))
            $errors = session()->getFlashdata('errors') ?? [];
            // Also show generic flash message if needed
            $flashSuccess = session()->getFlashdata('success') ?? null;
            $flashError = session()->getFlashdata('error') ?? null;
        ?>

        <?php if ($flashSuccess): ?>
            <div class="alert alert-success p-3 mb-4"><?= esc($flashSuccess) ?></div>
        <?php endif; ?>

        <?php if ($flashError): ?>
            <div class="alert alert-danger p-3 mb-4"><?= esc($flashError) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors) && is_array($errors)): ?>
            <div class="alert alert-custom p-3 mb-4">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- NOTE: controller uses redirect()->back()->withInput() so old() will work -->
        <form action="<?= base_url('auth/process_register') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row g-4"> 
                <div class="col-12">
                    <label>Full Name</label>
                    <input type="text" name="employee_name" class="form-control" placeholder="Enter Full Name" required
                        value="<?= esc(old('employee_name')) ?>">
                    <?php if (!empty($errors['employee_name'])): ?>
                        <div class="field-error"><?= esc($errors['employee_name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="jane@izifiso.com" required
                        value="<?= esc(old('email')) ?>">
                    <?php if (!empty($errors['email'])): ?>
                        <div class="field-error"><?= esc($errors['email']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label>Password</label>
                    <div class="password-container">
                        <!-- Do NOT repopulate password for security -->
                        <input type="password" id="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                        <button type="button" id="togglePassword" class="toggle-password-btn">
                            <i class="bi bi-eye-slash" id="eyeIcon"></i>
                        </button>
                    </div>
                    <?php if (!empty($errors['password'])): ?>
                        <div class="field-error"><?= esc($errors['password']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label>Department</label>
                    <select name="department_id" class="form-select" required>
                        <option value="" disabled <?= old('department_id') ? '' : 'selected' ?>>Select Department</option>
                        <?php foreach($departments as $d): ?>
                            <option value="<?= esc($d['id']) ?>"
                                <?= (string)old('department_id') === (string)$d['id'] ? 'selected' : '' ?>>
                                <?= esc($d['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['department_id'])): ?>
                        <div class="field-error"><?= esc($errors['department_id']) ?></div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($isSuperManager)): ?>
                <div class="col-md-6">
                    <label>System Role</label>
                    <select name="system_role" class="form-select">
                        <option value="Member" <?= old('system_role') === 'Member' ? 'selected' : '' ?>>Member</option>
                        <option value="Intern" <?= old('system_role') === 'Intern' ? 'selected' : '' ?>>Intern</option>
                        <option value="Manager" <?= old('system_role') === 'Manager' ? 'selected' : '' ?>>Manager</option>
                        <option value="Super Manager" <?= old('system_role') === 'Super Manager' ? 'selected' : '' ?>>Super Manager</option>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-md-6">
                    <label>Phone Number</label>
                    <input type="text" name="phone_no" class="form-control" placeholder="+91 000 000 0000"
                        value="<?= esc(old('phone_no')) ?>">
                    <?php if (!empty($errors['phone_no'])): ?>
                        <div class="field-error"><?= esc($errors['phone_no']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label>Designation</label>
                    <input type="text" name="designation" class="form-control" placeholder="Lead Developer"
                        value="<?= esc(old('designation')) ?>">
                    <?php if (!empty($errors['designation'])): ?>
                        <div class="field-error"><?= esc($errors['designation']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label>Work Location</label>
                    <input type="text" name="location" class="form-control" placeholder="HQ / Remote"
                        value="<?= esc(old('location')) ?>">
                    <?php if (!empty($errors['location'])): ?>
                        <div class="field-error"><?= esc($errors['location']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

              <button type="submit" class="btn btn-warning w-100 fw-bold text-dark mt-4">
                Create Team Account
            </button>


            <p class="text-center mt-4 text-muted">
                Already part of the team? <a href="<?= base_url('auth/login') ?>" class="text-warning font-weight-bold">Log in here</a>
            </p>
        </form>
    </div>
</div>

<script>
    // Password Show/Hide Script
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    });
</script>

</body>
</html>

<div>
    <a href="">git new</a>
</div>