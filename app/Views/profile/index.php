<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>



<div class="container">
    <div class="row g-4">
         <div>
            <a href="<?= base_url('team-builder') ?>" class="btn btn-outline-secondary">Back to Team Builder</a>
        </div>

        <!-- ================= LEFT PROFILE CARD ================= -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 text-center">

                <!-- HEADER -->
                <div class="card-body pt-4">
                    <img src="<?= base_url('uploads/profile/' . ($user['profile_image'] ?? 'default.png')) ?>"
                         class="rounded-circle border border-3 border-warning mb-3"
                         style="width:150px;height:150px;object-fit:cover;"
                         alt="Profile Image">

                    <h5 class="fw-bold mb-1"><?= esc($user['employee_name']) ?></h5>

                    <span class="badge bg-warning text-dark px-3 py-1">
                        <?= esc($user['designation'] ?? 'Employee') ?>
                    </span>

                    <p class="text-muted small mt-2 mb-0">
                        <?= esc($user['email'] ?? '') ?>
                    </p>
                </div>

                <hr class="my-0">

                <!-- UPLOAD IMAGE -->
                <div class="card-body">
                    <form action="<?= base_url('profile/upload-image') ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <input type="file" name="profile_image" class="form-control form-control-sm">
                        </div>
                        <button class="btn btn-outline-warning btn-sm w-100 fw-semibold">
                            <i class="bi bi-upload"></i> Update Profile Picture
                        </button>
                    </form>
                </div>

            </div>
        </div>

        <!-- ================= RIGHT CONTENT ================= -->
        <div class="col-lg-8">

            <!-- ================= EDIT PROFILE ================= -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white fw-semibold">
                    <i class="bi bi-person-lines-fill me-1"></i> Personal Information
                </div>

                <div class="card-body">
                    <form action="<?= base_url('profile/update') ?>" method="post">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="employee_name"
                                       class="form-control"
                                       value="<?= esc($user['employee_name']) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="text" name="phone_no"
                                       class="form-control"
                                       value="<?= esc($user['phone_no']) ?>">
                            </div>

                        </div>

                        <div class="mt-4 text-end">
                            <button class="btn btn-warning fw-bold px-4">
                                <i class="bi bi-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ================= CHANGE PASSWORD ================= -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark fw-semibold">
                    <i class="bi bi-shield-lock me-1"></i> Security Settings
                </div>

                <div class="card-body">
                    <form action="<?= base_url('profile/change-password') ?>" method="post">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Old Password</label>
                                <input type="password" name="old_password"
                                       class="form-control"
                                       placeholder="••••••••">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">New Password</label>
                                <input type="password" name="new_password"
                                       class="form-control"
                                       placeholder="••••••••">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Confirm Password</label>
                                <input type="password" name="confirm_password"
                                       class="form-control"
                                       placeholder="••••••••">
                            </div>

                        </div>

                        <div class="mt-4 text-end">
                            <button class="btn btn-dark fw-bold px-4">
                                <i class="bi bi-key"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
