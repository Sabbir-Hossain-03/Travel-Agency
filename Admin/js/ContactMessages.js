// Contact Messages JavaScript

document.addEventListener('DOMContentLoaded', function() {
    applyStoredTheme();
});

function viewMessage(id, name, message) {
    document.getElementById('modalName').textContent = name;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('viewModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('viewModal').style.display = 'none';
}

function deleteMessage(id) {
    if (confirm('Are you sure you want to delete this message?')) {
        fetch('../controller/ContactMessageActions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete&id=${encodeURIComponent(id)}`
        }).then(() => {
            // Reload to reflect changes
            window.location.reload();
        }).catch(() => {
            alert('Error deleting message.');
        });
    }
}

// Close modal on outside click
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('viewModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    }
});
