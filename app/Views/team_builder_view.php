<?php
$deptId = session()->get('department_id');
$role   = session()->get('system_role');

$isSuperManager = ($role === 'Super Manager');
$isManager      = ($role === 'Manager');
$isViewer       = in_array($role, ['Member', 'Intern']);

// Only Super Manager can see Register page
$canRegister = $isSuperManager;

// Managers and Super Managers can do all actions (except restricted)
$canEdit = $isSuperManager || $isManager;
$fieldDepts = ['1','2','3'];
$canAccessDestinations = in_array((string)$deptId, $fieldDepts);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Izifiso Team Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
    :root {
        --brand-yellow: #f5b301;
        --brand-yellow-soft: #fff8e1;
        --brand-dark: #111827;
        --border-soft: #e5e7eb;
    }

    body {
        font-family: 'Inter', system-ui, sans-serif;
        background: linear-gradient(180deg, #f8f9fb, #ffffff);
        color: #1f2933;
    }

    /* Navbar */
    .navbar {
        background: linear-gradient(90deg, #111827, #1f2933) !important;
    }

    .navbar-brand {
        font-weight: 800;
        letter-spacing: .6px;
    }

    /* Headings */
    h2 span {
        color: var(--brand-yellow);
    }

    /* Cards */
    .card {
        border-radius: 14px;
        border: 1px solid var(--border-soft);
    }

    .card-header {
        font-weight: 600;
        letter-spacing: .3px;
    }

    .card-header.bg-dark {
        background: linear-gradient(90deg, #111827, #1f2933) !important;
    }

    .card-header.bg-warning {
        background: linear-gradient(90deg, #f5b301, #ffc107) !important;
        color: #111827;
    }

    /* Forms */
    .form-label {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #6c757d;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 1px solid var(--border-soft);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--brand-yellow);
        box-shadow: 0 0 0 .15rem rgba(245, 179, 1, .25);
    }

    /* Buttons */
    .btn-warning {
        background: linear-gradient(90deg, #f5b301, #ffc107);
        border: none;
        color: #111827;
    }

    .btn-warning:hover {
        background: linear-gradient(90deg, #e0a800, #ffca2c);
        transform: translateY(-1px);
    }

    .btn-outline-warning {
        border-color: var(--brand-yellow);
        color: var(--brand-yellow);
    }

    .btn-outline-warning:hover {
        background: var(--brand-yellow);
        color: #111827;
    }

    /* Table */
    .table-hover tbody tr:hover {
        background: var(--brand-yellow-soft);
        transition: .2s;
    }

    /* Role Badges */
    .role-badge {
        font-size: .75rem;
        padding: 6px 10px;
        border-radius: 20px;
        margin: 2px;
        font-weight: 600;
        background: #e3f2fd;
        border: 1px solid #bee3f8;
    }

    /* Edit Slot Animation */
    #editSlot {
        overflow: hidden;
        transition: all .45s ease;
        opacity: 0;
        max-height: 0;
        transform: translateY(-15px);
    }

    #editSlot.active {
        opacity: 1;
        max-height: 600px;
        transform: translateY(0);
        margin-bottom: 1.5rem !important;
    }

    /* Loader */
    .loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(255, 255, 255, .85);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(4px);
        visibility: hidden;
        opacity: 0;
        transition: .3s;
    }

    body.loading .loading-overlay {
        visibility: visible;
        opacity: 1;
    }

    /* Role Checklist */
    .role-checklist-container {
        max-height: 180px;
        overflow-y: auto;
        background: #fff;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid var(--border-soft);
    }

    /* Floating Alert */
    #globalAlert {
        border-left: 5px solid var(--brand-yellow);
        border-radius: 12px;
    }
    </style>

</head>

<body>

    <div class="loading-overlay">
        <div class="spinner-border text-warning" style="width: 3rem; height: 3rem;" role="status"></div>
    </div>



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

                <a class="btn btn-outline-warning btn-sm" href="<?= base_url('auth/logout') ?>">Logout</a>


          <?php if ($canRegister): ?>
          <a href="<?= base_url('auth/register') ?>"
          class="btn btn-warning btn-sm ms-2">
         <i class="bi bi-person-plus-fill"></i> Register User
          </a>
          <?php endif; ?>


            </div>


        </div>


    </nav>



    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h2 class="h3 mb-0">Resource <span class="text-warning">Manager</span></h2>
            <div class="col-md-4">
                <?php if ($isSuperManager): ?>
                <!-- Super Manager can select any department -->
                <select id="managerDeptSelect" class="form-select border-primary shadow-sm"
                    onchange="loadDepartmentData(this.value)">
                    <option value="">-- Select Department --</option>
                    <?php foreach($departments as $dept): ?>
                    <option value="<?= $dept['id'] ?>"><?= esc($dept['department_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php else: ?>
                <!-- Manager/Viewer: no department select -->
                <input type="hidden" id="managerDeptSelect" value="<?= esc($deptId) ?>">
                <div class="fw-semibold">
                    <?= esc(array_values(array_filter($departments, fn($d)=> $d['id']==$deptId))[0]['department_name'] ?? 'Department') ?>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <div id="managerContent">

            <div id="globalAlert" class="alert alert-dismissible fade show shadow-sm"
                style="display:none; position: fixed; top: 20px; right: 20px; z-index: 1050; min-width: 300px;">
                <span id="alertIcon" class="me-2"></span>
                <strong id="alertTitle"></strong> <span id="alertMsg"></span>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
            </div>


            
   <div id="zoneActionButtons">
                <a href="<?= base_url('team-builder/destinations') ?>" class="btn btn-outline-warning btn-sm me-2">
                    <i class="bi bi-geo-alt-fill"></i> Add & View Destination
                </a>

                <a href="<?= base_url('zones') ?>" class="btn btn-outline-warning btn-sm me-2">
                    <i class="bi bi-geo-alt-fill"></i> View Zones Assigned Destination
                </a>
            </div>

            <br>
            

            <?php if (!$isViewer): ?>
            <div class="card shadow-sm mb-4 border-0 bg-light">
                <div class="card-body">
                    <label class="form-label fw-bold text-muted small text-uppercase">Quick Add: New Job Role</label>
                    <div class="input-group">
                        <input type="text" id="newRoleNameInput" class="form-control" placeholder="e.g. Senior Guide">
                        <button class="btn btn-dark" type="button" id="btnAddRole" onclick="addNewRoleToDB()">
                            <i class="bi bi-plus-lg"></i> Add Role
                        </button>
                    </div>
                </div>
            </div>

            <div id="zoneQuickAddContainer" class="card shadow-sm mb-4 border-0 bg-light">
                <div class="card-body">
                    <label class="form-label fw-bold text-muted small text-uppercase">Quick Add: New
                        Zone/Location</label>
                    <div class="input-group">
                        <input type="text" id="newZoneNameInput" class="form-control"
                            placeholder="e.g. Sundarbans East">
                        <button class="btn btn-primary" type="button" id="btnAddZone" onclick="addNewZoneToDB()"
                            style="background: #111827; border:none;">
                            <i class="bi bi-geo-fill"></i> Add Zone
                        </button>
                    </div>
                </div>
            </div>

         
            <br>

            <div class="card shadow mb-4 border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Assign Team Member</span>
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div class="card-body bg-white">
                    <form id="assignForm">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Select Employee</label>
                                <select name="employee_id" id="empSelect" class="form-select bg-light"
                                    required></select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Assign Job Role(s)</label>
                                <div id="jobChecklist" class="role-checklist-container border-warning custom-scrollbar">
                                </div>
                            </div>


                            <!-- CHANGED: zone SELECT -> zone CHECKLIST (multi-zone) -->
                            <div class="col-md-4" id="zoneContainer">
                                <label class="form-label fw-bold small">Assign Zone(s)</label>
                                <div id="zoneChecklist" class="role-checklist-container">
                                    <?php foreach($zones as $zone): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="zone_ids[]"
                                            value="<?= $zone['id'] ?>" id="zone_<?= $zone['id'] ?>">
                                        <label class="form-check-label" for="zone_<?= $zone['id'] ?>">
                                            <?= $zone['zone_name'] ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <small class="text-muted d-block mt-1">Hold Ctrl/Cmd to select multiple / use
                                    checkboxes</small>
                            </div>
                        </div>
                        <input type="hidden" name="department_id" id="hiddenDeptId">
                        <button type="submit" id="btnAssign"
                            class="btn btn-warning w-100 fw-bold mt-4 shadow-sm text-dark">
                            Confirm Assignment <i class="bi bi-arrow-right-circle ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div id="editSlot" class="card shadow mb-4 border-warning">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Assignment</span>
                    <button type="button" class="btn-close" onclick="closeEditSlot()"></button>
                </div>
                <div class="card-body bg-white">
                    <div class="alert alert-light border mb-3">
                        <div class="row text-muted small">
                            <div class="col-md-4">Editing: <strong class="text-dark" id="prevEmpName"></strong></div>
                            <div class="col-md-4">Current Roles: <strong class="text-dark" id="prevRoleName"></strong>
                            </div>
                            <div class="col-md-4" id="prevZoneArea">Current Zone: <strong class="text-dark"
                                    id="prevZoneName"></strong></div>
                        </div>
                    </div>
                    <form id="editAssignmentForm">
                        <input type="hidden" name="employee_id" id="editEmployeeId">
                        <input type="hidden" name="map_id" id="editMapId">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Update Employee</label>
                                <select id="editEmpSelect" class="form-select border-warning" disabled></select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Select Roles (Multi-Select)</label>
                                <div id="editRoleChecklist"
                                    class="role-checklist-container border-warning custom-scrollbar"></div>
                            </div>

                            <!-- CHANGED: edit zone SELECT -> edit zone CHECKLIST -->
                            <div class="col-md-4" id="editZoneContainer">
                                <label class="form-label fw-bold">Update Zone(s)</label>
                                <div id="editZoneChecklist" class="role-checklist-container border-warning">
                                    <?php foreach($zones as $zone): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="zone_ids[]"
                                            value="<?= $zone['id'] ?>" id="edit_zone_<?= $zone['id'] ?>">
                                        <label class="form-check-label" for="edit_zone_<?= $zone['id'] ?>">
                                            <?= $zone['zone_name'] ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                            <button type="button" class="btn btn-light border me-2"
                                onclick="closeEditSlot()">Cancel</button>
                            <button type="submit" id="btnSaveEdit" class="btn btn-warning fw-bold">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="bi bi-people-fill me-2"></i> Current Team Structure
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-dark text-uppercase small">
                                <tr>
                                    <th class="ps-3">Zone / Location</th>
                                    <th>Job Role(s)</th>
                                    <th>Employee Name</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="teamTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ZONE DESTINATIONS MODAL -->
    <div class="modal fade" id="zoneDestModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-geo-alt-fill me-2"></i>
                        Zone Destinations
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- SEARCH BOX -->
                    <input type="text" id="zoneDestSearch" class="form-control mb-3"
                        placeholder="🔍 Search destination..." onkeyup="filterZoneDestinations()">

                    <!-- DESTINATION LIST -->
                    <ul class="list-group" id="zoneDestList">
                        <li class="list-group-item text-muted text-center">
                            Loading...
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

   <script>
    const IS_VIEWER = <?= $isViewer ? 'true' : 'false' ?>;
    const IS_SUPER = <?= $isSuperManager ? 'true' : 'false' ?>;
    const IS_MANAGER = <?= $isManager ? 'true' : 'false' ?>;
    const SESSION_DEPT = "<?= esc($deptId) ?>";

    if (IS_VIEWER) {
        window.addNewRoleToDB = () => showAlert('danger', 'Unauthorized');
        window.addNewZoneToDB = () => showAlert('danger', 'Unauthorized');
        window.removeAssignment = () => showAlert('danger', 'Unauthorized');
        window.openEditSlot = () => showAlert('danger', 'Unauthorized');
    }

    // make PHP $zones available as fallback if backend doesn't return zones per-department
    let currentZoneDestinations = [];
    let initialZones = <?= json_encode($zones ?? []) ?>;
    let currentDeptRoles = [];
    let roleMap = {};
    let zoneMap = {};

    // --- UTILITY: Show/Hide Spinner on Buttons ---
    function setBtnLoading(btnId, isLoading, defaultText = '') {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        if (isLoading) {
            btn.dataset.originalText = btn.innerHTML;
            btn.innerHTML =
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;
            btn.disabled = true;
        } else {
            btn.innerHTML = defaultText || btn.dataset.originalText || btn.innerHTML;
            btn.disabled = false;
        }
    }

    // --- UTILITY: Alerts ---
    function showAlert(type, message) {
        const alertBox = document.getElementById('globalAlert');
        const title = document.getElementById('alertTitle');
        const msg = document.getElementById('alertMsg');
        const icon = document.getElementById('alertIcon');

        if (!alertBox) {
            // fallback: simple alert if global alert missing
            alert(message);
            return;
        }

        alertBox.className = `alert alert-${type} alert-dismissible fade show shadow-sm`;
        title.innerText = type === 'success' ? 'Success!' : 'Error!';
        msg.innerText = message;
        icon.className = type === 'success' ? 'bi bi-check-circle-fill' : 'bi bi-exclamation-triangle-fill';

        alertBox.style.display = 'block';
        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 4000);
    }

    // helper to build zone checklist HTML from a zones array
    function renderZoneChecklist(containerId, zones) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';
        zones.forEach(zone => {
            const id = containerId + '_z_' + zone.id;
            container.insertAdjacentHTML('beforeend', `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="zone_ids[]" value="${zone.id}" id="${id}">
                    <label class="form-check-label" for="${id}">${zone.zone_name}</label>
                </div>
            `);
        });
    }

    // --- LOAD DATA ---
    function loadDepartmentData(deptId) {
        if (!deptId) {
            const managerContent = document.getElementById('managerContent');
            if (managerContent) managerContent.style.display = 'block';
            return Promise.resolve();
        }

        document.body.classList.add('loading');
        const managerContent = document.getElementById('managerContent');

        return fetch('<?= base_url("team-builder/get-data/") ?>' + deptId)
            .then(response => response.json())
            .then(data => {

                // Viewer / unauthorized → show container but hide edit/add controls
                if (!data || Object.keys(data).length === 0) {
                    if (managerContent) managerContent.style.display = 'block';
                    const hideIds = ['zoneContainer', 'zoneQuickAddContainer', 'zoneActionButtons', 'jobChecklist'];
                    hideIds.forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.style.display = 'none';
                    });
                    document.body.classList.remove('loading');
                    return;
                }

                // Hidden deptId for forms
                const hiddenDeptInput = document.getElementById('hiddenDeptId');
                if (hiddenDeptInput) hiddenDeptInput.value = deptId;

                const isOffice = (deptId == "4" || deptId == "5");

                // Show/hide zone related buttons
                const zoneButtons = document.getElementById('zoneActionButtons');
                if (zoneButtons) zoneButtons.style.display = isOffice ? 'none' : 'block';

                const zoneContainer = document.getElementById('zoneContainer');
                const editZoneContainer = document.getElementById('editZoneContainer');
                if (zoneContainer) zoneContainer.style.display = isOffice ? 'none' : 'block';
                if (editZoneContainer) editZoneContainer.style.display = isOffice ? 'none' : 'block';

                const zoneQuickAdd = document.getElementById('zoneQuickAddContainer');
                if (zoneQuickAdd) zoneQuickAdd.style.display = isOffice ? 'none' : 'block';

                // Employee dropdown, roles, zones, table — same as before
                currentDeptRoles = data.job_types || [];
                roleMap = {};
                currentDeptRoles.forEach(r => roleMap[r.id] = r.job_type_name);

                zoneMap = {};
                (initialZones || []).forEach(z => zoneMap[z.id] = z.zone_name);

                populateEmployeeDropdown(data.employees || [], data.assigned_emp_ids || []);
                renderJobChecklist('jobChecklist', data.job_types || []);
                const zonesToUse = (data.zones && data.zones.length) ? data.zones : initialZones;
                renderZoneChecklist('zoneChecklist', zonesToUse);
                renderZoneChecklist('editZoneChecklist', zonesToUse);

                // Team table
                const tbody = document.getElementById('teamTableBody');
                if (!tbody) {
                    if (managerContent) managerContent.style.display = 'block';
                    document.body.classList.remove('loading');
                    return;
                }
                tbody.innerHTML = '';
                if (data.current_team && data.current_team.length > 0) {
                    data.current_team.forEach(row => {
                        let zoneContent = row.zone_ids ? row.zone_ids.split(',').map(id => `
                        <span class="badge bg-primary me-1 mb-1 zone-badge"
                              style="cursor:pointer"
                              onclick="openZoneDestinations(${id.trim()})">
                            <i class="bi bi-geo-alt"></i> ${zoneMap[id.trim()] ?? 'Zone'}
                        </span>`).join('') : '<span class="badge bg-secondary">Office Based</span>';

                        let rolesHtml = (row.job_roles || '').split(',').map(role =>
                            `<span class="badge bg-info text-dark role-badge border border-info">${role.trim()}</span>`
                        ).join('');

                        let actions = '';
                        if (!IS_VIEWER) {
                            actions = `
                        <button class="btn btn-sm btn-outline-primary me-1"
                            onclick="openEditSlot('${row.map_ids}', '${row.employee_id}', '${row.job_type_ids}', '${row.zone_ids}', this)">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger"
                            onclick="removeAssignment('${row.map_ids}', this)">
                            <i class="bi bi-trash"></i>
                        </button>`;
                        }

                        tbody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td class="ps-3">${zoneContent}</td>
                            <td>${rolesHtml}</td>
                            <td class="fw-bold text-dark">${row.employee_name}</td>
                            <td class="text-end pe-3">${actions}</td>
                        </tr>`);
                    });
                } else {
                    tbody.innerHTML =
                        '<tr><td colspan="4" class="text-center py-4 text-muted">No team members assigned yet.</td></tr>';
                }

                if (managerContent) managerContent.style.display = 'block';
                document.body.classList.remove('loading');
            })
            .catch(err => {
                console.error(err);
                const managerContent = document.getElementById('managerContent');
                if (managerContent) managerContent.style.display = 'block';
                document.body.classList.remove('loading');
            });
    }

    // --- ADD ROLE ---
    function addNewRoleToDB() {
        const roleNameInput = document.getElementById('newRoleNameInput');
        const roleName = roleNameInput ? roleNameInput.value.trim() : '';
        const deptSelect = document.getElementById('managerDeptSelect');
        const deptId = deptSelect ? deptSelect.value : null;

        if (!deptId) {
            showAlert('warning', "Please select a department first!");
            return;
        }
        if (!roleName) {
            showAlert('warning', "Please enter a role name.");
            return;
        }

        setBtnLoading('btnAddRole', true);

        fetch('<?= base_url("team-builder/save-role-only") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    job_type_name: roleName,
                    department_id: deptId
                })
            })
            .then(response => response.json())
            .then(data => {
                setBtnLoading('btnAddRole', false);
                if (data.status === 'success') {
                    if (roleNameInput) roleNameInput.value = '';
                    showAlert('success', "New role added!");
                    if (deptId) loadDepartmentData(deptId);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(() => {
                setBtnLoading('btnAddRole', false);
                showAlert('danger', 'Server error');
            });
    }

    // --- ASSIGN MEMBER (SAFE: only attach if form exists) ---
    (function() {
        const assignForm = document.getElementById('assignForm');
        if (!assignForm) return;

        assignForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const deptSelect = document.getElementById('managerDeptSelect');
            const deptId = deptSelect ? deptSelect.value : null;

            // --- STRICT RULE: Sales(1), Product(2), Execution(3) MUST have at least one zone ---
            const isFieldDept = (deptId == "1" || deptId == "2" || deptId == "3");
            const checkedZones = document.querySelectorAll('#zoneChecklist input[type="checkbox"]:checked');

            if (isFieldDept && checkedZones.length === 0) {
                showAlert('danger', "Zone selection is mandatory for Sales, Product, or Execution teams.");
                return;
            }

            // Gather all checked job roles
            const selectedJobRoles = Array.from(
                document.querySelectorAll('#jobChecklist input[type="checkbox"]:checked')
            ).map(cb => cb.value);

            if (selectedJobRoles.length === 0) {
                showAlert('danger', 'Please select at least one job role.');
                return;
            }

            const formData = new FormData(assignForm);
            // Remove old single job_type_id (if exists)
            formData.delete('job_type_id');
            selectedJobRoles.forEach(id => formData.append('job_type_ids[]', id));

            setBtnLoading('btnAssign', true);

            fetch('<?= base_url("team-builder/assign") ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setBtnLoading('btnAssign', false);
                    if (data.status === 'success') {
                        assignForm.reset();
                        showAlert('success', data.message);
                        if (deptId) loadDepartmentData(deptId);
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(err => {
                    setBtnLoading('btnAssign', false);
                    console.error(err);
                    showAlert('danger', 'Server error');
                });
        });
    })();

    // --- UPDATE ASSIGNMENT (SAFE: only attach if form exists) ---
    (function() {
        const editForm = document.getElementById('editAssignmentForm');
        if (!editForm) return;

        editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const deptSelect = document.getElementById('managerDeptSelect');
            const deptId = deptSelect ? deptSelect.value : null;
            const isFieldDept = (deptId == "1" || deptId == "2" || deptId == "3");
            const checkedEditZones = document.querySelectorAll('#editZoneChecklist input[type="checkbox"]:checked');

            if (isFieldDept && checkedEditZones.length === 0) {
                showAlert('danger', "A specific Zone is required for this department. It cannot be Office Based.");
                return;
            }

            const formData = new FormData(editForm);
            setBtnLoading('btnSaveEdit', true);

            fetch('<?= base_url("team-builder/update-assignment") ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setBtnLoading('btnSaveEdit', false);
                    if (data.status === 'success') {
                        closeEditSlot();
                        showAlert('success', data.message);
                        if (deptId) loadDepartmentData(deptId);
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(err => {
                    setBtnLoading('btnSaveEdit', false);
                    console.error(err);
                    showAlert('danger', "Server Error occurred.");
                });
        });
    })();

    // --- REMOVE MEMBER ---
    function removeAssignment(mapIds, btnElement) {
        if (!confirm('Are you sure you want to remove this member?')) return;

        if (!btnElement) {
            showAlert('danger', 'Action element missing');
            return;
        }

        // Visual feedback on the specific delete button
        const originalContent = btnElement.innerHTML;
        btnElement.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        btnElement.disabled = true;

        const deptSelect = document.getElementById('managerDeptSelect');
        const deptId = deptSelect ? deptSelect.value : null;

        let formData = new FormData();
        formData.append('map_ids', mapIds);

        fetch('<?= base_url("team-builder/remove") ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // <--- This now works because PHP returns JSON
            .then(data => {
                if (data.status === 'success') {
                    showAlert('success', data.message); // Uses the Toast alert now
                    if (deptId) loadDepartmentData(deptId);
                } else {
                    btnElement.innerHTML = originalContent;
                    btnElement.disabled = false;
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btnElement.innerHTML = originalContent;
                btnElement.disabled = false;
                showAlert('danger', 'System Error');
            });
    }

    // --- UI HELPERS ---
    function openEditSlot(mapIds, empId, roleIds, zoneIds, btn) {

        const slot = document.getElementById('editSlot');
        if (!slot) return;

        const deptSelect = document.getElementById('managerDeptSelect');
        const deptId = deptSelect ? deptSelect.value : null;

        const isOffice = (deptId == "4" || deptId == "5");

        // IDS → ARRAY
        const roleIdArr = roleIds ? roleIds.split(',').map(id => id.trim()) : [];
        const zoneIdArr = zoneIds ? zoneIds.split(',').map(id => id.trim()) : [];

        // DISPLAY TEXT WITH COMMAS 
        const roleNames = roleIdArr.map(id => roleMap[id]).filter(Boolean);
        const prevRole = document.getElementById('prevRoleName');
        if (prevRole) prevRole.innerText = roleNames.join(', ');

        if (!isOffice) {
            const zoneNames = zoneIdArr.map(id => zoneMap[id]).filter(Boolean);
            const prevZone = document.getElementById('prevZoneName');
            if (prevZone) prevZone.innerText = zoneNames.join(', ');
        }

        // EMPLOYEE
        const prevEmp = document.getElementById('prevEmpName');
        if (prevEmp && btn && btn.closest) {
            prevEmp.innerText = btn.closest('tr') ? btn.closest('tr').cells[2].innerText : '';
        }

        const editMapId = document.getElementById('editMapId');
        const editEmployeeId = document.getElementById('editEmployeeId');
        if (editMapId) editMapId.value = mapIds;
        if (editEmployeeId) editEmployeeId.value = empId;

        const editEmpSelect = document.getElementById('editEmpSelect');
        const empSelect = document.getElementById('empSelect');
        if (editEmpSelect && empSelect) {
            editEmpSelect.innerHTML = empSelect.innerHTML;
            editEmpSelect.value = empId;
            editEmpSelect.disabled = true;
        }

        // ROLE CHECKLIST PREFILL
        let roleHtml = '';
        currentDeptRoles.forEach(role => {
            const checked = roleIdArr.includes(role.id.toString()) ? 'checked' : '';
            roleHtml += `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="job_type_ids[]"
                        value="${role.id}" ${checked}>
                    <label class="form-check-label">${role.job_type_name}</label>
                </div>`;
        });
        const editRoleChecklist = document.getElementById('editRoleChecklist');
        if (editRoleChecklist) editRoleChecklist.innerHTML = roleHtml;

        // ZONE CHECKLIST PREFILL
        document.querySelectorAll('#editZoneChecklist input[type="checkbox"]').forEach(cb => {
            cb.checked = zoneIdArr.includes(cb.value);
        });

        // SHOW SLOT
        const prevZoneArea = document.getElementById('prevZoneArea');
        if (prevZoneArea) prevZoneArea.style.display = isOffice ? 'none' : 'block';
        if (editZoneContainer) document.getElementById('editZoneContainer').style.display = isOffice ? 'none' : 'block';

        slot.classList.add('active');
        slot.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    function closeEditSlot() {
        const slot = document.getElementById('editSlot');
        if (slot) slot.classList.remove('active');
    }

    // --- ADD ZONE ---
    function addNewZoneToDB() {
        const zoneNameInput = document.getElementById('newZoneNameInput');
        const zoneName = zoneNameInput ? zoneNameInput.value.trim() : '';
        const deptSelect = document.getElementById('managerDeptSelect');
        const deptId = deptSelect ? deptSelect.value : null;

        if (!zoneName) {
            showAlert('warning', "Please enter a zone name.");
            return;
        }

        setBtnLoading('btnAddZone', true);

        fetch('<?= base_url("team-builder/save-zone-only") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    zone_name: zoneName
                })
            })
            .then(res => res.json())
            .then(data => {
                setBtnLoading('btnAddZone', false);

                if (data.status === 'success') {
                    if (zoneNameInput) zoneNameInput.value = '';
                    showAlert('success', "New zone added successfully!");

                    //  ADD NEW ZONE TO LOCAL CACHE (NO REFRESH)
                    if (data.zone) {
                        initialZones.push(data.zone);

                        // Update both checklists immediately
                        renderZoneChecklist('zoneChecklist', initialZones);
                        renderZoneChecklist('editZoneChecklist', initialZones);
                    }

                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(() => {
                setBtnLoading('btnAddZone', false);
                showAlert('danger', 'Server error');
            });
    }

    // --- POPULATE EMPLOYEE DROPDOWN WITH STYLES ---
    function populateEmployeeDropdown(employees, assignedEmpIds) {
        const empSelect = document.getElementById('empSelect');
        if (!empSelect) return;
        empSelect.innerHTML = '<option value="">-- Select Employee --</option>';

        employees.forEach(emp => {
            const option = document.createElement('option');
            option.value = emp.id;
            option.textContent = emp.employee_name;

            if (assignedEmpIds.includes(emp.id.toString())) {
                // already assigned → disabled & off-white
                option.disabled = true;
                option.style.backgroundColor = '#f1f1f1';
                option.style.color = '#999';
                option.style.fontWeight = 'normal';
            } else {
                // not assigned → bold
                option.style.fontWeight = '600';
            }

            empSelect.appendChild(option);
        });
    }

    // helper function (similar to your zone checklist)
    function renderJobChecklist(containerId, roles, selectedRoleIds = []) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = '';
        roles.forEach(role => {
            const isChecked = selectedRoleIds.includes(role.id.toString()) ? 'checked' : '';
            container.insertAdjacentHTML('beforeend', `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="job_type_ids[]" value="${role.id}" id="job_${role.id}" ${isChecked}>
                    <label class="form-check-label" for="job_${role.id}">${role.job_type_name}</label>
                </div>
            `);
        });
    }

    // --------------------------------------------------
    // SHOW DESTINATIONS FOR A ZONE (POPUP)
    // --------------------------------------------------
    function showZoneDestinations(zoneId, zoneName) {
        const titleEl = document.getElementById('zoneDestTitle');
        if (titleEl) titleEl.innerText = 'Destinations in ' + zoneName;

        const list = document.getElementById('zoneDestList');
        if (!list) return;
        list.innerHTML = `
        <li class="list-group-item text-muted text-center">
            Loading...
        </li>`;

        fetch(`<?= base_url('team-builder/zone') ?>/${zoneId}/destinations`)
            .then(res => res.json())
            .then(res => {

                list.innerHTML = '';

                if (!res.data || res.data.length === 0) {
                    list.innerHTML = `
                    <li class="list-group-item text-muted text-center">
                        No destinations assigned
                    </li>`;
                    return;
                }

                res.data.forEach(dest => {
                    list.innerHTML += `
                    <li class="list-group-item">
                        <i class="bi bi-geo-alt-fill text-warning me-2"></i>
                        ${dest.destination_name}
                    </li>`;
                });
            });

        new bootstrap.Modal(
            document.getElementById('zoneDestModal')
        ).show();
    }

    function openZoneDestinations(zoneId) {

        const list = document.getElementById('zoneDestList');
        const searchInput = document.getElementById('zoneDestSearch');
        if (!list || !searchInput) return;

        // reset search
        searchInput.value = '';

        list.innerHTML = '<li class="list-group-item text-muted text-center">Loading...</li>';

        fetch(`<?= base_url('team-builder/zone') ?>/${zoneId}/destinations`)
            .then(res => res.json())
            .then(resp => {

                if (!resp || resp.status !== 'success' || !resp.data || resp.data.length === 0) {
                    currentZoneDestinations = [];
                    list.innerHTML = `
                        <li class="list-group-item text-muted text-center">
                            No destinations assigned
                        </li>`;
                    return;
                }

                //  STORE DATA FOR SEARCH
                currentZoneDestinations = resp.data;

                renderZoneDestinations(currentZoneDestinations);
            });

        new bootstrap.Modal(
            document.getElementById('zoneDestModal')
        ).show();
    }

    function renderZoneDestinations(destinations) {

        const list = document.getElementById('zoneDestList');
        if (!list) return;
        list.innerHTML = '';

        destinations.forEach(dest => {
            list.innerHTML += `
                <li class="list-group-item">
                    <i class="bi bi-geo-alt-fill text-warning me-2"></i>
                    ${dest.destination_name}
                </li>`;
        });
    }

    function filterZoneDestinations() {

        const input = document.getElementById('zoneDestSearch');
        if (!input) return;

        const query = input.value.toLowerCase();

        const filtered = currentZoneDestinations.filter(dest =>
            dest.destination_name.toLowerCase().includes(query)
        );

        if (filtered.length === 0) {
            const list = document.getElementById('zoneDestList');
            if (list) list.innerHTML = `
                <li class="list-group-item text-muted text-center">
                    No matching destinations
                </li>`;
            return;
        }

        renderZoneDestinations(filtered);
    }

    // Trigger department load for users without dropdown (Managers, Members, Interns)
    document.addEventListener('DOMContentLoaded', function() {
        const deptInput = document.getElementById('managerDeptSelect');
        const managerContent = document.getElementById('managerContent');

        if (deptInput && deptInput.value) {
            const deptId = deptInput.value;

            // Call loadDepartmentData but handle errors
            loadDepartmentData(deptId).catch(err => {
                console.error("Failed to load department data:", err);
                if (managerContent) managerContent.style.display = 'block'; // show container anyway
            });
        } else {
            // No deptId → show container but hide role/zone cards
            if (managerContent) managerContent.style.display = 'block';
        }
    });
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
