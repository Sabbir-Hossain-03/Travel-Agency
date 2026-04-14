document.addEventListener('DOMContentLoaded', function () {
    if (typeof applyStoredTheme === "function") applyStoredTheme();
});

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add User';
    document.getElementById('modalAction').value = 'add';
    document.getElementById('userForm').reset();
    document.getElementById('userModal').style.display = 'flex';
}

function openEditModal(user) {
    document.getElementById('modalTitle').textContent = 'Edit User';
    document.getElementById('modalAction').value = 'edit';
    document.getElementById('oldEmail').value = user.email;
    document.getElementById('modalUsername').value = user.username;
    document.getElementById('modalEmail').value = user.email;
    document.getElementById('modalStatus').value = user.status || 'Active';
    document.getElementById('userModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('userModal').style.display = 'none';
}

let pendingAction = null;
let pendingEmail = null;
let pendingUsername = '';

function openConfirmModal(message, action, email, username) {
    pendingAction = action;
    pendingEmail = email;
    pendingUsername = username || '';
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmModal').style.display = 'flex';
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
    pendingAction = null;
    pendingEmail = null;
    pendingUsername = '';
}

function submitPendingAction() {
    if (!pendingAction) return closeConfirmModal();
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../controller/ManageUsersActions.php';
    
    if (pendingAction === 'approve_admin') {
        // For approving admin requests, use request_id
        form.innerHTML = `
            <input type="hidden" name="action" value="${pendingAction}">
            <input type="hidden" name="request_id" value="${pendingEmail}">
        `;
    } else {
       
        form.innerHTML = `
            <input type="hidden" name="action" value="${pendingAction}">
            <input type="hidden" name="email" value="${pendingEmail}">
        `;
    }
    
    document.body.appendChild(form);
    form.submit();
    closeConfirmModal();
}

function deleteUser(email, username) {
    openConfirmModal(`Delete user: ${username}?`, 'delete', email, username);
}

function blockUser(email) {
    openConfirmModal('Block this user?', 'block', email);
}

function unblockUser(email) {
    openConfirmModal('Unblock this user?', 'unblock', email);
}
function approveAdminRequest(requestId, username) {
    // Store data for custom 
    window.approveData = {
        requestId: requestId,
        username: username
    };
    openConfirmModal(`Approve admin request from ${username}?`, 'approve_admin', requestId);
}

function rejectAdminRequest(requestId, username) {
    const reason = prompt(`Reject admin request from ${username}?\n\nReason (optional):`, '');
    if (reason !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../controller/ManageUsersActions.php';
        form.innerHTML = `
            <input type="hidden" name="action" value="reject_admin">
            <input type="hidden" name="request_id" value="${requestId}">
            <input type="hidden" name="reason" value="${reason}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}