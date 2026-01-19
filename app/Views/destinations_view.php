<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>


<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Destinations <small class="text-muted">(manage states & destinations)</small></h3>
        <div>
            <a href="<?= base_url('team-builder') ?>" class="btn btn-outline-secondary">Back to Team Builder</a>
        </div>
    </div>

    <div class="row g-4">
        <?php if (empty($isViewer)): ?>
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">Add Destination</div>
                <div class="card-body">
                    <div id="destAlert" class="alert d-none"></div>

                    <form id="destForm">

                        <div class="mb-3">
                            <label class="form-label">Destination Name</label>
                            <input type="text" name="destination_name" id="destination_name" class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">State</label>
                            <div class="input-group">
                                <select name="state_id" id="state_id" class="form-select" required>
                                    <option value="">-- Select State --</option>
                                    <?php foreach($states as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= esc($s['state']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#addStateModal">
                                    <i class="bi bi-plus-lg"></i> Add State
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description (optional)</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" id="btnSaveDest" class="btn btn-warning w-100">Save Destination</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white fw-bold">
                    Destinations List
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Destination</th>
                                    <th>State</th>
                                    <th>Description</th>
                                    <?php if (empty($isViewer)): ?>
                                    <th class="text-end">Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody id="destTableBody">
                                <tr>
                                    <td colspan="<?= !empty($isViewer) ? 3 : 4 ?>" class="text-center py-4 text-muted">
                                        Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add State Modal -->
<div class="modal fade" id="addStateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add State</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="stateAlert" class="alert d-none"></div>
                <div class="mb-3">
                    <label class="form-label">State Name</label>
                    <input type="text" id="newStateName" class="form-control" placeholder="e.g. West Bengal"
                        autocomplete="off">
                </div>
                <div class="text-end">
                    <button type="button" id="btnSaveState" class="btn btn-primary">Add State</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/* role flags from PHP */
const IS_VIEWER = <?= !empty($isViewer) ? 'true' : 'false' ?>;
const IS_SUPER = <?= !empty($isSuperManager) ? 'true' : 'false' ?>;
const IS_MANAGER = <?= !empty($isManager) ? 'true' : 'false' ?>;

document.addEventListener('DOMContentLoaded', () => {
    const base = '<?= base_url('team-builder') ?>';
    const listUrl = base + '/destinations/list';

    // DOM refs (may be absent for viewers)
    const addStateModalEl = document.getElementById('addStateModal');
    const newStateInput = document.getElementById('newStateName');
    const saveStateBtn = document.getElementById('btnSaveState');
    const stateAlertBox = document.getElementById('stateAlert');
    const destFormEl = document.getElementById('destForm');

    // Always try to load the list (viewers and admins)
    loadDestinations();

    // Attach submit handler only if form exists (admins)
    if (destFormEl) {
        destFormEl.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSaveDest');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = 'Saving...';
            }

            const fd = new FormData(this);
            fetch(base + '/destinations/save', {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(json => {
                    if (btn) { btn.disabled = false; btn.innerHTML = 'Save Destination'; }
                    const alert = document.getElementById('destAlert');
                    if (alert) {
                        if (json.status === 'success') {
                            alert.className = 'alert alert-success';
                            alert.innerText = json.message || 'Saved';
                            alert.classList.remove('d-none');
                        } else {
                            alert.className = 'alert alert-danger';
                            alert.innerText = json.message || 'Failed to save';
                            alert.classList.remove('d-none');
                        }
                        setTimeout(() => alert.classList.add('d-none'), 3500);
                    }
                    if (json.status === 'success') {
                        this.reset();
                        loadDestinations();
                    }
                })
                .catch(() => {
                    if (btn) { btn.disabled = false; btn.innerHTML = 'Save Destination'; }
                    alert('Server error');
                });
        });
    }

    // If the modal element exists, attach helpful handlers (safe for viewer too since element exists)
    if (addStateModalEl && newStateInput) {
        addStateModalEl.addEventListener('shown.bs.modal', () => {
            newStateInput.focus();
            newStateInput.select();
        });

        // prevent accidental submission
        addStateModalEl.addEventListener('submit', e => e.preventDefault());

        newStateInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (saveStateBtn) saveStateBtn.click();
            }
        });
    }

    // Attach save-state only if button exists (admins)
    if (saveStateBtn) {
        saveStateBtn.addEventListener('click', function() {
            const name = (newStateInput && newStateInput.value) ? newStateInput.value.trim() : '';
            if (stateAlertBox) stateAlertBox.className = 'alert d-none';

            if (!name) {
                if (stateAlertBox) {
                    stateAlertBox.className = 'alert alert-danger';
                    stateAlertBox.innerText = 'Please enter a state name';
                    stateAlertBox.classList.remove('d-none');
                }
                return;
            }

            this.disabled = true;
            fetch(base + '/destinations/save-state', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ state: name })
                })
                .then(res => res.json())
                .then(json => {
                    this.disabled = false;
                    if (json.status === 'success') {
                        const sel = document.getElementById('state_id');
                        if (sel) {
                            const opt = document.createElement('option');
                            opt.value = json.state.id;
                            opt.textContent = json.state.state;
                            sel.appendChild(opt);
                            sel.value = json.state.id;
                        }
                        if (newStateInput) newStateInput.value = '';
                        if (stateAlertBox) {
                            stateAlertBox.className = 'alert alert-success';
                            stateAlertBox.innerText = 'State added successfully';
                            stateAlertBox.classList.remove('d-none');
                        }
                        setTimeout(() => {
                            try {
                                const modal = bootstrap.Modal.getInstance(addStateModalEl);
                                if (modal) modal.hide();
                            } catch (e) { /* ignore */ }
                        }, 500);
                    } else {
                        if (stateAlertBox) {
                            stateAlertBox.className = 'alert alert-danger';
                            stateAlertBox.innerText = json.message || 'Failed';
                            stateAlertBox.classList.remove('d-none');
                        }
                    }
                })
                .catch(() => {
                    this.disabled = false;
                    if (stateAlertBox) {
                        stateAlertBox.className = 'alert alert-danger';
                        stateAlertBox.innerText = 'Server error';
                        stateAlertBox.classList.remove('d-none');
                    }
                });
        });
    }

    // Note: edit/delete buttons only render for non-viewers (PHP). We still define the handlers below safely.
});

/* Load and render destinations safely for both viewers and admins */
function loadDestinations() {
    const base = '<?= base_url('team-builder') ?>';
    const listUrl = base + '/destinations/list';
    fetch(listUrl)
        .then(r => r.json())
        .then(json => {
            if (json.status !== 'success') return;
            const tbody = document.getElementById('destTableBody');
            if (!tbody) return;
            tbody.innerHTML = '';

            const isViewer = <?= !empty($isViewer) ? 'true' : 'false' ?>;
            if (!json.destinations || json.destinations.length === 0) {
                const colspan = isViewer ? 3 : 4;
                tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-4 text-muted">No destinations yet.</td></tr>`;
                return;
            }

            json.destinations.forEach(d => {
                let actionsHtml = '';
                if (!isViewer) {
                    actionsHtml = `
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary me-1"
                                onclick="editDestination(
                                    ${d.id},
                                    '${escapeHtml(d.destination_name)}',
                                    ${d.state_id},
                                    '${escapeHtml(d.state)}',
                                    '${escapeHtml(d.description || '')}'
                                )">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger"
                                onclick="deleteDestination(${d.id}, this)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>`;
                }

                tbody.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td class="fw-bold">${escapeHtml(d.destination_name)}</td>
                        <td>${escapeHtml(d.state)}</td>
                        <td>${escapeHtml(d.description || '')}</td>
                        ${actionsHtml}
                    </tr>
                `);
            });
        })
        .catch(err => {
            console.error('Failed to load destinations', err);
        });
}

/* delete handler (safe) */
window.deleteDestination = function(id, btn) {
    if (!confirm('Delete this destination?')) return;
    if (btn) btn.disabled = true;
    const base = '<?= base_url('team-builder') ?>';
    fetch(base + '/destinations/delete', {
        method: 'POST',
        body: new URLSearchParams({ id }),
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(json => {
        if (json.status === 'success') {
            loadDestinations();
        } else {
            alert(json.message || 'Cannot delete');
            if (btn) btn.disabled = false;
        }
    })
    .catch(() => {
        if (btn) btn.disabled = false;
        alert('Server error');
    });
};

/* edit handler (safe: checks DOM before using it) */
window.editDestination = function(id, name, stateId, stateName, desc) {
    const editId = document.getElementById('edit_id');
    const editName = document.getElementById('edit_name');
    const editState = document.getElementById('edit_state');
    const editStateName = document.getElementById('edit_state_name');
    const editDesc = document.getElementById('edit_desc');
    const modalEl = document.getElementById('editDestModal');

    if (!editId || !editName || !editState || !editStateName || !editDesc || !modalEl) {
        // Admin UI not present — silently return (viewer)
        return;
    }

    editId.value = id;
    editName.value = name;
    editState.value = stateId;
    editStateName.value = stateName;
    editDesc.value = desc || '';

    new bootstrap.Modal(modalEl).show();
};

/* update handler */
window.updateDestination = function() {
    const alertBox = document.getElementById('editAlert');
    if (alertBox) alertBox.className = 'alert d-none';

    const idEl = document.getElementById('edit_id');
    const nameEl = document.getElementById('edit_name');
    const stateEl = document.getElementById('edit_state');
    const descEl = document.getElementById('edit_desc');

    if (!idEl || !nameEl || !stateEl || !descEl) return;

    const data = new URLSearchParams({
        id: idEl.value,
        destination_name: nameEl.value,
        state_id: stateEl.value,
        description: descEl.value
    });

    const base = '<?= base_url('team-builder') ?>';
    fetch(base + '/destinations/update', {
        method: 'POST',
        body: data,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(json => {
        if (json.status === 'success') {
            const modal = document.getElementById('editDestModal');
            try {
                const m = bootstrap.Modal.getInstance(modal);
                if (m) m.hide();
            } catch (e) { /* ignore */ }
            loadDestinations();
        } else {
            if (alertBox) {
                alertBox.className = 'alert alert-danger';
                alertBox.innerText = json.message || 'Update failed';
                alertBox.classList.remove('d-none');
            }
        }
    })
    .catch(() => {
        if (alertBox) {
            alertBox.className = 'alert alert-danger';
            alertBox.innerText = 'Server error';
            alertBox.classList.remove('d-none');
        }
    });
};

/* small util */
function escapeHtml(s) {
    if (!s) return '';
    return s.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;');
}
</script>

<!-- Edit Destination Modal -->
<div class="modal fade" id="editDestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Destination</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="editAlert" class="alert d-none"></div>

                <input type="hidden" id="edit_id">

                <div class="mb-3">
                    <label class="form-label">Destination Name</label>
                    <input type="text" id="edit_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">State</label>
                    <input type="hidden" id="edit_state">

                    <input type="text" id="edit_state_name" class="form-control" readonly>
                    
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea id="edit_desc" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="updateDestination()">Update</button>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>
