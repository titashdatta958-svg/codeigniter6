<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Izifiso Team Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
    :root {
        --primary: #2563eb;
        /* Corporate Blue */
        --primary-hover: #1d4ed8;
        --bg-body: #f8fafc;
        /* Light Slate Gray */
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-body);
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    .login-container {
        background: #ffffff;
        width: 100%;
        max-width: 440px;
        padding: 2.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid var(--border-color);
    }

    .header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .header h2 {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.025em;
    }

    .header p {
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    input {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background-color: #ffffff;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    /* --- Password Toggle CSS --- */
    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-wrapper input {
        padding-right: 3rem; /* Space for the icon */
    }

    .toggle-btn {
        position: absolute;
        right: 10px;
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 5px;
        width: auto; /* Reset global button width */
        margin-top: 0; /* Reset global button margin */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toggle-btn:hover {
        background: none; /* Keep transparent */
        color: var(--primary);
    }

    /* Error/Success Alert Box */
    .alert-box {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
        display: none;
        /* Hidden by default */
        align-items: center;
    }

    .alert-error {
        background-color: #fef2f2;
        border: 1px solid #fee2e2;
        color: #b91c1c;
    }

    .alert-success {
        background-color: #f0fdf4;
        border: 1px solid #dcfce7;
        color: #15803d;
    }

    button {
        width: 100%;
        background-color: var(--primary);
        color: white;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s ease;
        margin-top: 0.5rem;
    }

    button:hover {
        background-color: var(--primary-hover);
    }

    button:disabled {
        background-color: var(--text-muted);
        cursor: not-allowed;
    }

    .footer {
        margin-top: 2rem;
        text-align: center;
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .footer a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }

    .footer a:hover {
        text-decoration: underline;
    }

    .flex-between {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="header">
            <img src="/assets/image.jpg" width="40" height="40" class="me-2 rounded-circle" alt="Izifiso Logo">
            <h2>Izifiso Team</h2>
            <p>Welcome back! Please enter your details.</p>
        </div>

        <div id="authAlert" class="alert-box"></div>

        <form id="loginForm">
            

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <div class="flex-between">
                    <label for="password">Password</label>
                    <a href="#" class="text-warning font-weight-bold"
                        style="text-decoration: none; font-size: 0.8rem;">Forgot password?</a>
                </div>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <button type="button" id="togglePassword" class="toggle-btn">
                        <i class="bi bi-eye-slash" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" id="btnLogin" class="btn btn-warning">Sign in to Account</button>
        </form>

        <div class="footer">
            Only Super Manager can <a class="text-warning font-weight-bold">Sign up</a> for Everyone.
        </div>
    </div>

    <script>
    // --- New Script for Password Show/Hide ---
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        // Toggle the type attribute
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        
        // Toggle the eye icon
        eyeIcon.classList.toggle('bi-eye');
        eyeIcon.classList.toggle('bi-eye-slash');
    });

    // --- Your original Submit Script ---
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = document.getElementById('btnLogin');
        const alertBox = document.getElementById('authAlert');
        const formData = new FormData(this);

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Checking...';
        alertBox.style.display = 'none';

        fetch('<?= base_url("auth/process_login") ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alertBox.className = "alert-box alert-success";
                    alertBox.innerHTML = "<strong>Success!</strong> Redirecting to portal...";
                    alertBox.style.display = 'flex';

                    setTimeout(() => {
                        window.location.href = '<?= base_url("team-builder") ?>';
                    }, 1000);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = 'Sign in to Account';
                    alertBox.className = "alert-box alert-error";
                    let errorMsg = '';

                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            errorMsg += `<div>• ${data.errors[key]}</div>`;
                        });
                    } else {
                        errorMsg = data.message || "Invalid Email or Password";
                    }

                    alertBox.innerHTML = errorMsg;
                    alertBox.style.display = 'block';
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'Sign in to Account';
                alertBox.className = "alert-box alert-error";
                alertBox.innerHTML = "Connection error. Please try again.";
                alertBox.style.display = 'flex';
            });
    });
    </script>

</body>

</html>