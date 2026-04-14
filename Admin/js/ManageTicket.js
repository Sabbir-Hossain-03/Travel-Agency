document.addEventListener("DOMContentLoaded", () => {
  const formCard = document.getElementById("ticketFormCard");
  const openBtn = document.getElementById("openFormBtn");
  const closeBtn = document.getElementById("closeFormBtn");
  const cancelBtn = document.getElementById("cancelFormBtn");

  const form = document.getElementById("ticketForm");
  const formTitle = document.getElementById("formTitle");
  const submitBtn = document.getElementById("submitBtn");

  const actionField = document.getElementById("actionField");
  const idField = document.getElementById("idField");

  const f_ticket_code = document.getElementById("ticket_code");
  const f_seat_count = document.getElementById("seat_count");
  const f_bus_class = document.getElementById("bus_class");
  const f_status = document.getElementById("status");

  const err_ticket_code = document.getElementById("err_ticket_code");
  const err_seat_count = document.getElementById("err_seat_count");
  const err_bus_class = document.getElementById("err_bus_class");
  const err_status = document.getElementById("err_status");

  function showForm() {
    formCard.classList.add("show");
    formCard.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  function hideForm() {
    formCard.classList.remove("show");
    clearErrors();
  }

  function clearErrors() {
    [err_ticket_code, err_seat_count, err_status, err_bus_class]
      .forEach(el => el && (el.textContent = ""));
  }

  function setError(el, msg) {
    if (el) el.textContent = msg;
  }

  function resetToAdd() {
    actionField.value = "add";
    idField.value = "";
    formTitle.textContent = "Add Ticket";
    submitBtn.textContent = "Save Ticket";

    f_ticket_code.value = "";
    f_seat_count.value = "";
    f_bus_class.value = "AC";
    f_status.value = "Pending";
    clearErrors();
  }

  function fillEditFromRow(row) {
    actionField.value = "update";
    idField.value = row.dataset.id || "";
    formTitle.textContent = "Edit Ticket";
    submitBtn.textContent = "Update Ticket";

    f_ticket_code.value = row.dataset.ticket_code || "";
    f_seat_count.value = row.dataset.seat_count || "";
    f_bus_class.value = row.dataset.bus_class || "AC";
    f_status.value = row.dataset.status || "Pending";
    clearErrors();
  }

  function getSelectedAvailable() {
    const opt = f_trip_id.options[f_trip_id.selectedIndex];
    if (!opt) return null;
    const av = opt.getAttribute("data-available");
    if (av === null) return null;
    const n = parseInt(av, 10);
    return Number.isNaN(n) ? null : n;
  }

  function validateClient() {
    clearErrors();
    let ok = true;

    const code = (f_ticket_code.value || "").trim();
    const route = (document.getElementById('route').value || "").trim();
    const seats = parseInt(f_seat_count.value, 10);
    const busClass = f_bus_class.value;

    if (!code) { setError(err_ticket_code, "Ticket Code is required."); ok = false; }
    else if (!/^[A-Za-z0-9\-_]{2,30}$/.test(code)) {
      setError(err_ticket_code, "2–30 chars, letters/numbers/-/_ only.");
      ok = false;
    }

    if (!route) {
      setError(document.getElementById('err_route'), "Route is required.");
      ok = false;
    }

    if (!busClass || !["AC","Non-AC"].includes(busClass)) {
      setError(err_bus_class, "Select Bus Class.");
      ok = false;
    }

    if (!f_seat_count.value || Number.isNaN(seats) || seats < 1) {
      setError(err_seat_count, "Seat count must be at least 1.");
      ok = false;
    }

    if (!["active","inactive"].includes(f_status.value)) {
      setError(err_status, "Invalid status.");
      ok = false;
    }

    return ok;
  }

  // Buttons
  openBtn?.addEventListener("click", () => { resetToAdd(); showForm(); });
  closeBtn?.addEventListener("click", hideForm);
  cancelBtn?.addEventListener("click", () => { hideForm(); resetToAdd(); });

  // Grid quick-add + table edit


  // Custom modal for confirmation
  function showCustomConfirm(message, onConfirm) {
    let modal = document.createElement("div");
    modal.className = "custom-modal-bg";
    modal.innerHTML = `
      <div class="custom-modal">
        <div class="custom-modal-msg">${message}</div>
        <div class="custom-modal-actions">
          <button class="custom-modal-ok">Yes</button>
          <button class="custom-modal-cancel">Cancel</button>
        </div>
      </div>
    `;
    Object.assign(modal.style, {
      position: "fixed", top: 0, left: 0, width: "100vw", height: "100vh", background: "rgba(0,0,0,0.18)", zIndex: 99999,
      display: "flex", alignItems: "center", justifyContent: "center"
    });
    let box = modal.querySelector(".custom-modal");
    Object.assign(box.style, {
      background: "#fff", borderRadius: "12px", padding: "32px 32px 24px 32px", boxShadow: "0 8px 32px rgba(0,0,0,0.18)", minWidth: "320px", textAlign: "center"
    });
    let msg = modal.querySelector(".custom-modal-msg");
    msg.style.marginBottom = "18px";
    msg.style.fontSize = "18px";
    msg.style.fontWeight = "500";
    let actions = modal.querySelector(".custom-modal-actions");
    actions.style.display = "flex";
    actions.style.justifyContent = "center";
    actions.style.gap = "18px";
    let okBtn = modal.querySelector(".custom-modal-ok");
    let cancelBtn = modal.querySelector(".custom-modal-cancel");
    okBtn.style.background = "#2563eb";
    okBtn.style.color = "#fff";
    okBtn.style.border = "none";
    okBtn.style.borderRadius = "6px";
    okBtn.style.padding = "8px 24px";
    okBtn.style.fontWeight = "600";
    okBtn.style.cursor = "pointer";
    cancelBtn.style.background = "#f3f4f6";
    cancelBtn.style.color = "#222";
    cancelBtn.style.border = "none";
    cancelBtn.style.borderRadius = "6px";
    cancelBtn.style.padding = "8px 24px";
    cancelBtn.style.fontWeight = "600";
    cancelBtn.style.cursor = "pointer";
    okBtn.onclick = () => { document.body.removeChild(modal); onConfirm(); };
    cancelBtn.onclick = () => { document.body.removeChild(modal); };
    document.body.appendChild(modal);
  }

  document.addEventListener("click", (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;

    // Handle grid card edit
    if (btn.classList.contains("edit-btn") && btn.closest(".ticket-card")) {
      // Find ticket info from card
      const card = btn.closest(".ticket-card");
      // Fill form fields from card's data attributes
      actionField.value = "update";
      idField.value = card.getAttribute('data-id') || "";
      formTitle.textContent = "Edit Ticket";
      submitBtn.textContent = "Update Ticket";
      f_ticket_code.value = card.getAttribute('data-ticket_code') || "";
      document.getElementById('ticket_type').value = card.getAttribute('data-ticket_type') || "Bus";
      document.getElementById('route').value = card.getAttribute('data-route') || "";
      f_bus_class.value = card.getAttribute('data-bus_class') || "AC";
      f_seat_count.value = card.getAttribute('data-seat_count') || "";
      f_status.value = card.getAttribute('data-status') || "active";
      clearErrors();
      showForm();
      return;
    }

    // Handle delete/inactive/active confirmation for grid and table
    if (btn.classList.contains("delete-btn")) {
      e.preventDefault();
      showCustomConfirm("Are you sure you want to delete this ticket?", () => {
        btn.closest("form").submit();
      });
      return;
    }
    // Custom Inactive/Active toggle confirmation
    if (btn.textContent.trim() === "Make Inactive") {
      e.preventDefault();
      showCustomConfirm("Are you sure you want to make this ticket inactive?", () => {
        btn.closest("form").submit();
      });
      return;
    }
    if (btn.textContent.trim() === "Make Active") {
      e.preventDefault();
      showCustomConfirm("Are you sure you want to make this ticket active?", () => {
        btn.closest("form").submit();
      });
      return;
    }

    // Table row edit
    const action = btn.dataset.action;
    if (action === "edit") {
      const row = btn.closest("tr");
      if (!row) return;
      fillEditFromRow(row);
      showForm();
      return;
    }
  });

  // Toast message utility
  function showToast(msg, type) {
    let toast = document.createElement("div");
    toast.className = "toast " + (type || "info");
    toast.textContent = msg;
    toast.style.position = "fixed";
    toast.style.top = "30px";
    toast.style.right = "30px";
    toast.style.zIndex = 9999;
    toast.style.padding = "16px 32px";
    toast.style.borderRadius = "8px";
    toast.style.background = type === "success" ? "#0ecb81" : type === "info" ? "#2563eb" : "#f87171";
    toast.style.color = "#fff";
    toast.style.fontWeight = "600";
    document.body.appendChild(toast);
    setTimeout(() => { toast.remove(); }, 2000);
  }

  // Submit validation
  form?.addEventListener("submit", (e) => {
    if (!validateClient()) {
      e.preventDefault();
      showForm();
    }
  });
});
