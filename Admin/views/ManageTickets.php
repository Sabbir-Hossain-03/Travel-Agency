<?php
include('dark_mode.php');
include('../database/dbconnection.php');

include('../database/TicketsData.php');

function esc($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $q = trim($_POST['q'] ?? '');
  $status = trim($_POST['status'] ?? '');
} else {
  $q = trim($_GET['q'] ?? '');
  $status = trim($_GET['status'] ?? '');
}

$tickets = getBusTickets($q, $status);

// Pagination
$per_page    = 6;
$total       = count($tickets);
$total_pages = max(1, (int)ceil($total / $per_page));
$current_page = max(1, min($total_pages, (int)($_GET['page'] ?? 1)));
$offset       = ($current_page - 1) * $per_page;
$tickets_page = array_slice($tickets, $offset, $per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Tickets - Avestra Travel Agency</title>
  <link rel="stylesheet" href="../styleSheets/ManageTickets.css?v=<?php echo time(); ?>" />
   <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
  <link rel="icon" href="../images/logo.png" type="image/png" />
  <link rel="stylesheet" href="../styleSheets/dark-mode.css?v=<?php echo time(); ?>" />
  <script>
    localStorage.setItem('theme', '<?= $current_theme ?>');
    document.documentElement.setAttribute('data-theme', '<?= $current_theme ?>');
  </script>
</head>

<body class="<?= $is_dark ? 'dark-mode' : '' ?>">
  <div class="admin-container">
    <aside class="sidebar">
      <div class="sidebar-logo-wrap">
        <div class="sidebar-logo-center">
          <img src="../images/logo.png" alt="Avestra Logo" class="sidebar-logo-img">
        </div>
        <h2 class="sidebar-title">Admin Panel</h2>
      </div>

      <nav>
        <ul class="sidebar-menu">
                    <li><a href="Admin.php">Dashboard</a></li>
                    <li><a href="ManageUsers.php">Manage Users</a></li>
                    <li><a href="ManageTickets.php" class="active">Tickets</a></li>
                    <li><a href="ManageHotels.php">Hotels</a></li>
                    <li><a href="ManageTours.php">Tours</a></li>
                    <li><a href="ManageBookings.php">Manage Bookings</a></li>
                    <li><a href="Payments.php">Payments</a></li>
                    <li><a href="Reports.php">Reports</a></li>
                    <li><a href="Settings.php">Settings</a></li>
                    <li><a href="MyProfile.php">My Profile</a></li>
                    <li><a href="homePage.php">Logout</a></li>
                </ul>
      </nav>
    </aside>

    <main class="main-content">
      <header class="admin-header">
        <h1><i class="fa-solid fa-ticket"></i> Manage Tickets</h1>
      </header>

      <?php if ($msg): ?>
        <div class="toast success"><?= esc($msg) ?></div>
      <?php endif; ?>

      <?php if ($err): ?>
        <div class="toast error"><?= esc($err) ?></div>
      <?php endif; ?>

      <!-- MAIN CARD: action bar + form + grid + pagination -->
      <div class="admin-card">
        <div class="section-actions">
               <form class="search-wrap" method="POST" action="ManageTickets.php">
                 <input type="text" class="section-search" name="q"
                   placeholder="Search (Ticket ID)..."
                   value="<?= esc($q) ?>" />
                 <button class="search-btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                 <?php if ($q !== ''): ?>
                     <a href="ManageTickets.php" class="clear-btn"><i class="fa-solid fa-xmark"></i> Clear</a>
                 <?php endif; ?>
               </form>
          <button class="add-section-btn" type="button" id="openFormBtn"><i class="fa-solid fa-plus"></i> Add Ticket</button>
        </div>
      <!-- form panel (hidden until Add/Edit clicked) -->
      <div class="form-card" id="ticketFormCard">
        <div class="form-title">
          <h2 id="formTitle">Add Ticket</h2>
          <button type="button" class="x-btn" id="closeFormBtn">✕</button>
        </div>

        <form class="form-container" id="ticketForm" method="POST" action="../controller/ManageTicketsController.php" enctype="multipart/form-data">
          <input type="hidden" name="action" id="actionField" value="add" />
          <input type="hidden" name="id" id="idField" value="" />


          <div class="form-group">
            <label>Ticket Code</label>
            <input type="text" name="ticket_code" id="ticket_code" placeholder="Example: T202601" />
            <div class="field-error" id="err_ticket_code"></div>
          </div>

          <div class="form-group">
            <label>Bus Ticket Type</label>
            <input type="text" name="ticket_type" id="ticket_type" value="Bus" readonly />
            <div class="field-error" id="err_ticket_type"></div>
          </div>

          <div class="form-group">
            <label>Route</label>
            <input type="text" name="route" id="route" placeholder="Example: Dhaka → Cox’s Bazar" />
            <div class="field-error" id="err_route"></div>
          </div>


          <div class="form-group">
            <label>Bus Class</label>
            <select name="bus_class" id="bus_class">
              <option value="AC">AC</option>
              <option value="Non-AC">Non-AC</option>
            </select>
            <div class="field-error" id="err_bus_class"></div>
          </div>

          <div class="form-group">
            <label>Seat Count</label>
            <input type="number" name="seat_count" id="seat_count" min="1" max="45" placeholder="Enter seat count" />
            <div class="field-error" id="err_seat_count"></div>
          </div>

          <div class="form-group">
            <label>Price</label>
            <input type="number" name="price" id="price" min="0" step="0.01" placeholder="Enter ticket price" />
            <div class="field-error" id="err_price"></div>
          </div>

          <div class="form-group">
            <label>Ticket Image</label>
            <input type="file" name="ticket_image" accept="image/*" required />
            <div class="field-error" id="err_ticket_image"></div>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="status" id="status">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            <div class="field-error" id="err_status"></div>
          </div>

          <div class="form-actions">
            <button class="save-btn" type="submit" id="submitBtn">Save Ticket</button>
            <button class="cancel-btn" type="button" id="cancelFormBtn">Cancel</button>
          </div>
        </form>
      </div>




    
      <?php if (empty($tickets)): ?>
        <div class="no-tickets-msg">No tickets found.</div>
      <?php else: ?>
        <div class="ticket-grid ticket-grid-margin">
          <?php foreach ($tickets_page as $t): ?>
            <?php
              $statusClass = strtolower($t['status']) === 'active' ? 'active' : 'inactive';
              $busClass = esc($t['bus_class']);
              $statusText = ucfirst($t['status']);
            ?>
            <div class="ticket-card card-responsive" style="padding:0; overflow:hidden; border-radius:12px; border:1px solid #e0e0e0; box-shadow:0 4px 12px rgba(0,0,0,0.05); display:flex; flex-direction:column; background:#fff;"
              data-id="<?= (int)$t['id'] ?>"
              data-ticket_code="<?= esc($t['ticket_code']) ?>"
              data-ticket_type="<?= esc($t['ticket_type'] ?? 'Bus') ?>"
              data-route="<?= esc($t['route']) ?>"
              data-bus_class="<?= esc($t['bus_class']) ?>"
              data-seat_count="<?= (int)$t['seat_count'] ?>"
              data-status="<?= esc($t['status']) ?>"
            >
              <!-- IMAGE ON TOP -->
              <div class="ticket-card-img-wrap" style="position: relative;">
                <?php $imgSrc = !empty($t['image']) ? '../images/' . esc($t['image']) : null; ?>
                <?php if ($imgSrc): ?>
                  <img src="<?= $imgSrc ?>" alt="<?= esc($t['ticket_code']) ?>" style="width:100%; height:160px; object-fit:cover; display:block;" />
                <?php else: ?>
                  <div style="width:100%; height:160px; background:#f0f4f8; display:flex; align-items:center; justify-content:center; font-size:4rem; color:#ccc;">🚌</div>
                <?php endif; ?>
                <span style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.7); color: #fff; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 700;">ID: <?= esc($t['ticket_code']) ?></span>
              </div>

              <!-- BLUE HEADER -->
              <div class="ticket-card-header" style="background:#2563eb; color:#fff; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                <div class="ticket-card-title" style="font-weight:700; font-size:1.1rem; display:flex; align-items:center; gap:8px;">
                  <span>🚌</span> <?= esc($t['ticket_type'] ?? 'Bus') ?>
                </div>
                <span style="background:rgba(255,255,255,0.2); color:#fff; padding:4px 12px; border-radius:20px; font-size:0.85rem; font-weight:600; display:flex; align-items:center; gap:6px;">
                  <span style="width:8px; height:8px; border-radius:50%; background:<?= $statusClass === 'active' ? '#10b981' : '#ef4444' ?>;"></span> <?= $statusText ?>
                </span>
              </div>

              <!-- BODY INFO -->
              <div class="ticket-card-body" style="padding: 16px; flex-grow:1; display:flex; flex-direction:column; gap:10px; font-size:0.95rem; color:#333;">
                <div><i class="fas fa-route" style="width:20px; color:#4b5563;"></i> <b>Route:</b> <?= esc($t['route']) ?></div>
                <div><i class="fas fa-bus" style="width:20px; color:#4b5563;"></i> <b>Bus Class:</b> <?= $busClass ?></div>
                <div><i class="fas fa-chair" style="width:20px; color:#4b5563;"></i> <b>Seats:</b> <?= (int)$t['seat_count'] ?></div>
                <div><i class="fas fa-bangladeshi-taka-sign" style="width:20px; color:#4b5563;"></i> <b>Price:</b> <?= isset($t['price']) ? (float)$t['price'] . ' ৳' : 'N/A' ?></div>
              </div>

              <!-- ACTIONS -->
              <div class="ticket-card-actions" style="padding: 12px 16px 16px; display:flex; gap:8px; border-top:1px solid #f3f4f6;">
                <button class="edit-btn" type="button" data-action="edit" style="flex:1; padding:8px; border-radius:6px; font-weight:600; font-size:0.9rem; background:#eff6ff; color:#2563eb; border:none; cursor:pointer;"><i class="fa-regular fa-pen-to-square"></i> Edit</button>
                <form method="POST" action="../controller/ManageTicketsController.php" style="flex:1; margin:0;">
                  <input type="hidden" name="action" value="toggle_status">
                  <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                  <input type="hidden" name="current_status" value="<?= esc($t['status']) ?>">
                  <?php if (strtolower($t['status']) === 'active'): ?>
                    <button type="submit" style="width:100%; padding:8px; border-radius:6px; font-weight:600; font-size:0.9rem; background:#fef3c7; color:#d97706; border:none; cursor:pointer;"><i class="fa-solid fa-toggle-off"></i> Inactive</button>
                  <?php else: ?>
                    <button type="submit" style="width:100%; padding:8px; border-radius:6px; font-weight:600; font-size:0.9rem; background:#dcfce7; color:#16a34a; border:none; cursor:pointer;"><i class="fa-solid fa-toggle-on"></i> Active</button>
                  <?php endif; ?>
                </form>
                <form method="POST" action="../controller/ManageTicketsController.php" style="flex:1; margin:0;">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                  <button type="submit" style="width:100%; padding:8px; border-radius:6px; font-weight:600; font-size:0.9rem; background:#fee2e2; color:#dc2626; border:none; cursor:pointer; <?php if (strtolower($t['status']) === 'active') echo 'opacity:0.5; cursor:not-allowed;'; ?>" <?php if (strtolower($t['status']) === 'active') echo 'disabled'; ?>><i class="fa-solid fa-trash"></i> Delete</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($total_pages > 1): ?>
        <div class="pagination-bar">
          <div class="pagination-info">
            Showing <?= $offset + 1 ?>–<?= min($offset + $per_page, $total) ?> of <?= $total ?> tickets
          </div>
          <div class="pagination-controls">
            <?php if ($current_page > 1): ?>
              <a class="page-btn" href="?q=<?= urlencode($q) ?>&status=<?= urlencode($status) ?>&page=<?= $current_page - 1 ?>">
                <i class="fa-solid fa-chevron-left"></i> Prev
              </a>
            <?php endif; ?>
            <span class="pagination-page">Page <?= $current_page ?> of <?= $total_pages ?></span>
            <?php if ($current_page < $total_pages): ?>
              <a class="page-btn" href="?q=<?= urlencode($q) ?>&status=<?= urlencode($status) ?>&page=<?= $current_page + 1 ?>">
                Next <i class="fa-solid fa-chevron-right"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      </div><!-- /admin-card -->
    </main>
  </div>
  <script src="../js/ManageTicket.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Toast auto-hide
      var toast = document.querySelector('.toast');
      if (toast) {
        setTimeout(function() {
          toast.style.opacity = '0';
          setTimeout(function() { toast.remove(); }, 500);
        }, 3000);
        setTimeout(function() {
          if (window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.delete('msg');
            url.searchParams.delete('err');
            window.history.replaceState({}, document.title, url.pathname + url.search);
          }
        }, 1000);
      }
      // Hide form by default, show on button click
      var openFormBtn = document.getElementById('openFormBtn');
      var closeFormBtn = document.getElementById('closeFormBtn');
      var ticketFormCard = document.getElementById('ticketFormCard');
      if (openFormBtn && ticketFormCard) {
        openFormBtn.addEventListener('click', function() {
          ticketFormCard.style.display = 'block';
        });
      }
      if (closeFormBtn && ticketFormCard) {
        closeFormBtn.addEventListener('click', function() {
          ticketFormCard.style.display = 'none';
        });
      }
      var cancelFormBtn = document.getElementById('cancelFormBtn');
      if (cancelFormBtn && ticketFormCard) {
        cancelFormBtn.addEventListener('click', function() {
          ticketFormCard.style.display = 'none';
        });
      }
    });
  </script>
</body>
</html>
