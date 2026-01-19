<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Zones & Destinations</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fa;
        }
        .zone-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,.08);
        }
        .badge-zone {
            font-size: 14px;
            padding: 6px 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>

<div class="container mt-4">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Zone Destination Mapping</h3>
        <a href="<?= base_url('team-builder') ?>" class="btn btn-outline-secondary">
            Back to Team Builder
        </a>
    </div>

    <!-- ZONES TABLE -->
    <div class="zone-card p-3">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-dark">
            <tr>
                <th>Zone Name</th>
                <th class="text-center">Destinations</th>
                <?php if (empty($isViewer)): ?>
                <th class="text-center">Action</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>

            <?php if (!empty($zones)): ?>
                <?php foreach ($zones as $zone): ?>
                    <tr>
                        <!-- Zone Name -->
                        <td class="fw-semibold">
                            <?= esc($zone['zone_name']) ?>
                        </td>

                        <!-- Destinations -->
                        <td class="text-center">
                            <?php if (!empty($zone['destination_count']) && $zone['destination_count'] > 0): ?>
                                <span class="badge bg-primary badge-zone"
                                      onclick="showDestinations('<?= esc(addslashes($zone['destination_names'])) ?>')">
                                    <?= $zone['destination_count'] ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted small">No destinations assigned</span>
                            <?php endif; ?>
                        </td>

                        <!-- Assign / Edit (only for Manager / Super Manager) -->
                        <?php if (empty($isViewer)): ?>
                        <td class="text-center">
                            <!-- 📍 ASSIGN DESTINATIONS -->
                            <button class="btn btn-sm btn-warning mb-1"
                                onclick="openAssignModal(<?= $zone['id'] ?>)">
                                Assign Destinations
                            </button>

                            <!-- ✏ EDIT ZONE NAME -->
                            <button class="btn btn-sm btn-outline-primary"
                                onclick="openEditZoneModal(
                                    <?= $zone['id'] ?>,
                                    '<?= esc(addslashes($zone['zone_name'])) ?>'
                                )">
                                Edit Zone
                            </button>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= !empty($isViewer) ? 2 : 3 ?>" class="text-center text-muted">
                        No zones found
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<!-- ASSIGN DESTINATIONS MODAL -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Assign Destinations to Zone</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- 🔍 Search -->
                <input type="text"
                       id="destinationSearch"
                       class="form-control form-control-sm mb-2"
                       placeholder="Search destination...">

                <!-- Hidden Zone ID -->
                <input type="hidden" id="zoneId">

                <!-- Destination checklist -->
                <div id="destinationChecklist"
                     class="border rounded p-2"
                     style="max-height:300px; overflow-y:auto;">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="saveZoneDestinations()">Save</button>
            </div>

        </div>
    </div>
</div>



<!-- EDIT ZONE MODAL -->
<div class="modal fade" id="editZoneModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Zone Name</h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="editZoneId">

                <label class="form-label">Zone Name</label>
                <input type="text"
                       id="editZoneName"
                       class="form-control"
                       placeholder="Enter zone name">
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary"
                        onclick="updateZoneName()">Update</button>
            </div>

        </div>
    </div>
</div>




<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Safe initialization: only create modals if elements exist
    let assignModal = null;
    const assignEl = document.getElementById('assignModal');
    if (assignEl) {
        assignModal = new bootstrap.Modal(assignEl);
    }

    let editZoneModal = null;
    const editZoneEl = document.getElementById('editZoneModal');
    if (editZoneEl) {
        editZoneModal = new bootstrap.Modal(editZoneEl);
    }

    // Helper to render destinations inside the modal; assignedIds should be an array
    function renderDestinations(destinations = [], assignedIds = []) {
        const box = document.getElementById('destinationChecklist');
        if (!box) return;

        box.innerHTML = '';

        // Normalize assigned ids to string set to avoid type mismatches
        const assignedSet = new Set((assignedIds || []).map(String));

        (destinations || []).forEach(d => {
            const checked = assignedSet.has(String(d.id)) ? 'checked' : '';
            box.insertAdjacentHTML('beforeend', `
                <div class="form-check destination-item">
                    <input class="form-check-input"
                           type="checkbox"
                           value="${d.id}"
                           ${checked}>
                    <label class="form-check-label">
                        ${escapeHtml(d.destination_name)}
                    </label>
                </div>`);
        });
    }

    // OPEN MODAL WITH AUTO-CHECKED DESTINATIONS
    function openAssignModal(zoneId) {
        // If assign modal not initialized (e.g., viewer-only), simply return
        if (!assignModal) return;

        const zoneInput = document.getElementById('zoneId');
        if (zoneInput) zoneInput.value = zoneId;

        const searchInput = document.getElementById('destinationSearch');
        if (searchInput) searchInput.value = '';

        // fetch all destinations, then fetch assigned ids for this zone
        fetch("<?= base_url('zones/get-destinations') ?>")
            .then(res => res.ok ? res.json() : Promise.reject('Failed to fetch destinations'))
            .then(allDestinations => {
                // ensure we have an array
                const allDest = Array.isArray(allDestinations) ? allDestinations : (allDestinations.data || allDestinations);
                // now fetch assigned ids
                return fetch("<?= base_url('zones/get-zone-destinations') ?>/" + zoneId)
                    .then(r => r.ok ? r.json() : Promise.reject('Failed to fetch assigned ids'))
                    .then(assigned => {
                        // assigned could be an array of ids or an object — normalize
                        const assignedIds = Array.isArray(assigned) ? assigned : (assigned.data || assigned.ids || assigned);
                        renderDestinations(allDest || [], assignedIds || []);
                        assignModal.show();
                    });
            })
            .catch(err => {
                console.error('openAssignModal error:', err);
                alert('Could not load destinations. Try again.');
            });
    }

    // SEARCH FILTER (guard the element)
    const destinationSearchEl = document.getElementById('destinationSearch');
    if (destinationSearchEl) {
        destinationSearchEl.addEventListener('keyup', function () {
            const keyword = this.value.toLowerCase();

            document.querySelectorAll('.destination-item').forEach(item => {
                item.style.display =
                    item.innerText.toLowerCase().includes(keyword) ? 'block' : 'none';
            });
        });
    }

    // SAVE MAPPING
    function saveZoneDestinations() {
        const zoneIdEl = document.getElementById('zoneId');
        if (!zoneIdEl) {
            alert('Zone not selected');
            return;
        }
        const zoneId = zoneIdEl.value;
        const selected = [];

        document.querySelectorAll('#destinationChecklist input:checked')
            .forEach(cb => selected.push(cb.value));

        if (selected.length === 0) {
            alert('Please select at least one destination');
            return;
        }

        const formData = new FormData();
        formData.append('zone_id', zoneId);
        selected.forEach(id => formData.append('destination_ids[]', id));

        fetch("<?= base_url('zones/save-mapping') ?>", {
            method: 'POST',
            body: formData
        })
        .then(res => res.ok ? res.json() : Promise.reject('Failed to save mapping'))
        .then(res => {
            if (res.status === 'success') {
                // hide modal then refresh (or re-fetch zones)
                if (assignModal) assignModal.hide();
                location.reload();
            } else {
                alert(res.message || 'Something went wrong');
            }
        })
        .catch(err => {
            console.error('saveZoneDestinations error:', err);
            alert('Could not save mapping');
        });
    }

    // DESTINATION POPUP (view-only)
    function showDestinations(names) {
        if (!names) {
            alert('No destinations assigned');
            return;
        }

        const list = names
            .split(',')
            .map(d => '• ' + d.trim())
            .join('\n');

        alert('Destinations:\n\n' + list);
    }

    // EDIT ZONE
    function openEditZoneModal(id, name) {
        if (!editZoneModal) return;
        document.getElementById('editZoneId').value = id;
        document.getElementById('editZoneName').value = name;
        editZoneModal.show();
    }

    function updateZoneName() {
        const idEl = document.getElementById('editZoneId');
        const nameEl = document.getElementById('editZoneName');
        if (!idEl || !nameEl) return;

        const id = idEl.value;
        const name = nameEl.value.trim();

        if (!name) {
            alert('Zone name cannot be empty');
            return;
        }

        const formData = new FormData();
        formData.append('id', id);
        formData.append('zone_name', name);

        fetch("<?= base_url('zones/update') ?>", {
            method: 'POST',
            body: formData
        })
        .then(res => res.ok ? res.json() : Promise.reject('Failed to update zone'))
        .then(res => {
            if (res.status === 'success') {
                if (editZoneModal) editZoneModal.hide();
                location.reload();
            } else {
                alert(res.message || 'Update failed');
            }
        })
        .catch(err => {
            console.error('updateZoneName error:', err);
            alert('Update failed');
        });
    }

    // small helper
    function escapeHtml(s) {
        if (s === null || s === undefined) return '';
        return String(s)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;');
    }
</script>

</body>
</html>
