// ManageTours.js

function showCustomMessage(message, type = 'success') {
  var msgDiv = document.getElementById('customMessage');
  var msgText = document.getElementById('customMessageText');
  var msgBox = document.getElementById('customMessageBox');

  if (msgDiv && msgText && msgBox) {
    msgText.textContent = message;
    msgBox.style.background = (type === 'success') ? '#4fc3f7' : '#c62828';
    msgDiv.style.display = 'block';
    setTimeout(function () { msgDiv.style.display = 'none'; }, 3500);
  }
}

// ===== Confirm modal =====
let confirmAction = null;
let confirmArgs = [];

function showConfirmModal(message, action, args) {
  confirmAction = action;
  confirmArgs = args || [];
  document.getElementById('confirmModalMessage').textContent = message;
  document.getElementById('confirmModal').style.display = 'flex';
}

function hideConfirmModal() {
  document.getElementById('confirmModal').style.display = 'none';
  confirmAction = null;
  confirmArgs = [];
}

function handleConfirmModalYes() {
  if (confirmAction) confirmAction.apply(null, confirmArgs);
  hideConfirmModal();
}

// ===== Show / Hide Form =====
function showTourForm() {
  const card = document.getElementById('tourFormCard');
  if (card) card.style.display = 'block';
  // optional: scroll to form
  card?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function hideTourForm() {
  const card = document.getElementById('tourFormCard');
  if (card) card.style.display = 'none';
}

function setAddMode() {
  const form = document.getElementById('tourForm');
  if (form) form.reset();

  document.getElementById('formAction').value = 'add';
  document.getElementById('tourId').value = '';
  document.getElementById('oldTourId').value = '';
  document.getElementById('tourId').readOnly = false;
  document.getElementById('modalTitle').innerHTML =
    '<i class="fas fa-plus-circle"></i> Add Tour';

  showTourForm();
}

// ===== AJAX Delete =====
function submitDeleteTour(id) {
  const formData = new FormData();
  formData.append('action', 'delete');
  formData.append('id', id);

  fetch('../controller/ManageToursController.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        showCustomMessage(data.message, 'success');
        setTimeout(() => window.location.reload(), 1200);
      } else {
        showCustomMessage(data.message || 'Delete failed', 'error');
      }
    })
    .catch(() => showCustomMessage('Network error', 'error'));
}

// ===== AJAX Toggle =====
function submitToggleTour(id) {
  const formData = new FormData();
  formData.append('action', 'toggle');
  formData.append('id', id);

  fetch('../controller/ManageToursController.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        showCustomMessage(data.message, 'success');
        setTimeout(() => window.location.reload(), 1200);
      } else {
        showCustomMessage(data.message || 'Status update failed', 'error');
      }
    })
    .catch(() => showCustomMessage('Network error', 'error'));
}

// ===== Main DOM =====
document.addEventListener('DOMContentLoaded', function () {

  // show toast from session alerts
  var successMsg = document.querySelector('.alert-success');
  var errorMsg = document.querySelector('.alert-error');
  if (successMsg) { showCustomMessage(successMsg.textContent, 'success'); successMsg.style.display = 'none'; }
  if (errorMsg) { showCustomMessage(errorMsg.textContent, 'error'); errorMsg.style.display = 'none'; }

  // open form
  const openBtn = document.getElementById('openAddTourBtn');
  if (openBtn) openBtn.addEventListener('click', setAddMode);

  // close/cancel form
  const closeBtn = document.getElementById('closeTourFormBtn');
  const cancelBtn = document.getElementById('cancelTourFormBtn');
  if (closeBtn) closeBtn.addEventListener('click', hideTourForm);
  if (cancelBtn) cancelBtn.addEventListener('click', hideTourForm);

  // grid actions
  const grid = document.querySelector('.tour-cards-grid');
  if (!grid) return;

  grid.addEventListener('click', function (e) {
    const editBtn = e.target.closest('.edit-btn');
    if (editBtn) {
      e.preventDefault();

      document.getElementById('formAction').value = 'edit';
      document.getElementById('tourId').value = editBtn.dataset.id;
      document.getElementById('oldTourId').value = editBtn.dataset.id;
      document.getElementById('tourId').readOnly = true;
      document.getElementById('tourName').value = editBtn.dataset.name || '';
      document.getElementById('tourDestination').value = editBtn.dataset.destination || '';
      document.getElementById('tourDuration').value = editBtn.dataset.duration || '';
      document.getElementById('tourPrice').value = editBtn.dataset.price || '';
      document.getElementById('tourStatus').value = editBtn.dataset.status || 'Active';
      document.getElementById('tourIncludes').value = editBtn.dataset.includes || '';
      document.getElementById('tourImage').value = ''; // Clear image input

      document.getElementById('modalTitle').innerHTML =
        '<i class="fas fa-pen-to-square"></i> Edit Tour';

      showTourForm();
      return;
    }

    const toggleBtn = e.target.closest('.toggle-btn');
    if (toggleBtn) {
      e.preventDefault();
      const id = toggleBtn.dataset.id;
      showConfirmModal('Change this tour status (Active/Inactive)?', submitToggleTour, [id]);
      return;
    }

    const deleteBtn = e.target.closest('.delete-btn');
    if (deleteBtn) {
      e.preventDefault();

      if (deleteBtn.dataset.enabled !== '1') {
        showCustomMessage('Only inactive tours can be deleted.', 'error');
        return;
      }

      const id = deleteBtn.dataset.id;
      const name = deleteBtn.dataset.name || '';
      showConfirmModal('Delete "' + name + '"? This cannot be undone.', submitDeleteTour, [id]);
    }
  });
});

// expose modal functions
window.hideConfirmModal = hideConfirmModal;
window.handleConfirmModalYes = handleConfirmModalYes;
