<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'IZIFISO TEAM') ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .navbar-brand {
            font-weight: 800;
            letter-spacing: .6px;
        }
    </style>
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('team-builder') ?>">
            <img src="/assets/image.jpg" width="35" height="35"
                 class="me-2 rounded-circle border border-2 border-warning" alt="Logo">
            <span class="text-warning">IZIFISO</span> TEAM
        </a>

        <div class="d-flex align-items-center">
            <span class="text-white me-3 small d-none d-md-block">
                Signed in as <strong><?= session()->get('user_name') ?></strong>
            </span>

            <a class="btn btn-outline-warning btn-sm me-2" href="<?= base_url('profile') ?>">
                <i class="bi bi-person-circle"></i> Profile
            </a>

            <a class="btn btn-outline-warning btn-sm" href="<?= base_url('auth/logout') ?>">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <?= $this->renderSection('content') ?>
</div>

<!-- BOOTSTRAP JS (MANDATORY FOR MODALS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
