document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.payment-action-group form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            // Use event submitter
            let btn = e.submitter || form.querySelector('button[type="submit"]');
            if (btn) {
                let action = btn.value;
                let msg = '';
                if (action === 'accept') {
                    msg = 'Are you sure you want to ACCEPT this payment?';
                } else if (action === 'reject') {
                    msg = 'Are you sure you want to REJECT this payment?';
                }
                if (msg) {
                    e.preventDefault();
                    showCustomConfirm(msg, function(confirmed) {
                        if (confirmed) {
                            btn.disabled = true;
                            btn.innerHTML = action === 'accept' ? 'Accepting...' : 'Rejecting...';
                            form.submit();
                        }
                    });
                    return false;
                }
                btn.disabled = true;
                btn.innerHTML = action === 'accept' ? 'Accepting...' : 'Rejecting...';
            }
        });
    });
});


function showCustomConfirm(message, callback) {
    let modal = document.getElementById('customConfirmModal');
    if (!modal) return callback(window.confirm(message)); // fallback
    document.getElementById('customConfirmText').textContent = message;
    modal.style.display = 'flex';
    function cleanup(result) {
        modal.style.display = 'none';
        callback(result);
    }
    const yesBtn = document.getElementById('customConfirmYes');
    const noBtn = document.getElementById('customConfirmNo');
    function yesHandler() { cleanup(true); }
    function noHandler() { cleanup(false); }
    yesBtn.onclick = yesHandler;
    noBtn.onclick = noHandler;
   
    modal.onkeydown = function(e) { if (e.key === 'Escape') cleanup(false); };
    yesBtn.focus();
}
