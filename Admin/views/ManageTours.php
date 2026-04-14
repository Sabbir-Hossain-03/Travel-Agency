<?php
include('dark_mode.php');
  include(__DIR__ . '/../database/dbconnection.php');

function esc($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// Flash messages from controller
$success = $_SESSION['tour_success'] ?? '';
$error   = $_SESSION['tour_error'] ?? '';
unset($_SESSION['tour_success'], $_SESSION['tour_error']);

// Search
$q = trim($_GET['q'] ?? '');

// Fetch tours (with search)
$tours = [];
if ($q !== '') {
  $stmt = $conn->prepare("SELECT * FROM tours WHERE name LIKE ? OR destination LIKE ? ORDER BY id DESC");
  $like = "%$q%";
  $stmt->bind_param("ss", $like, $like);
  $stmt->execute();
  $res = $stmt->get_result();
} else {
  $res = $conn->query("SELECT * FROM tours ORDER BY id DESC");
}
if ($res) {
  while ($row = $res->fetch_assoc()) $tours[] = $row;
}

// Pagination
$per_page     = 6;
$total        = count($tours);
$total_pages  = max(1, (int)ceil($total / $per_page));
$current_page = max(1, min($total_pages, (int)($_GET['page'] ?? 1)));
$offset       = ($current_page - 1) * $per_page;
$tours_page   = array_slice($tours, $offset, $per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Tours - Avestra Travel Agency</title>

  <link rel="stylesheet" href="../styleSheets/ManageTours.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="../styleSheets/ManageToursExtra.css?v=<?php echo time(); ?>" />
  <link rel="icon" href="../images/logo.png" type="image/png" />
  <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css"/>
  <link rel="stylesheet" href="../styleSheets/dark-mode.css?v=<?php echo time(); ?>" />
  <script>
    localStorage.setItem('theme', '<?= $current_theme ?>');
    document.documentElement.setAttribute('data-theme', '<?= $current_theme ?>');
  </script>
</head>

<body class="<?= $is_dark ? 'dark-mode' : '' ?>">

  <!-- ✅ Toast -->
  <div id="customMessage"
       style="display:none; position:fixed; top:32px; right:32px; z-index:99999; min-width:320px; max-width:420px;">
    <div id="customMessageBox"
         style="background:#4fc3f7; padding:14px 16px; border-radius:12px; box-shadow:0 12px 30px rgba(0,0,0,.15); display:flex; align-items:center; justify-content:space-between; gap:10px;">
      <span id="customMessageText" style="color:#fff; font-weight:900;"></span>
      <button type="button"
              onclick="document.getElementById('customMessage').style.display='none'"
              style="border:none;background:rgba(255,255,255,.25);color:#fff;font-weight:900;padding:6px 10px;border-radius:10px;cursor:pointer;">
        Close
      </button>
    </div>
  </div>

  <!-- ✅ Hidden alerts used by JS -->
  <?php if ($success): ?>
    <div class="alert-success" style="display:none;"><?= esc($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert-error" style="display:none;"><?= esc($error) ?></div>
  <?php endif; ?>

  <!-- ✅ Confirm Modal -->
  <div id="confirmModal"
       style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:10000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; box-shadow:0 12px 40px rgba(0,0,0,.18); padding:26px 22px; min-width:320px; max-width:90vw; text-align:center;">
      <div id="confirmModalMessage" style="font-weight:900; color:#22304a; margin-bottom:16px;"></div>
      <div style="display:flex; gap:10px; justify-content:center;">
        <button type="button"
                onclick="handleConfirmModalYes()"
                style="border:none; background:#22304a; color:#fff; font-weight:900; padding:10px 16px; border-radius:10px; cursor:pointer;">
          Yes
        </button>
        <button type="button"
                onclick="hideConfirmModal()"
                style="border:1px solid #dbe6f3; background:#f3f6fb; color:#22304a; font-weight:900; padding:10px 16px; border-radius:10px; cursor:pointer;">
          No
        </button>
      </div>
    </div>
  </div>

  <div class="admin-container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div style="padding: 24px 32px;">
        <div style="text-align: center; margin-bottom: 16px;">
          <img src="../images/logo.png" alt="Avestra Logo" style="width: 60px; height: auto;">
        </div>
        <h2 class="sidebar-title">Admin Panel</h2>
      </div>

      <nav>
        <ul class="sidebar-menu">
                    <li><a href="Admin.php">Dashboard</a></li>
                    <li><a href="ManageUsers.php">Manage Users</a></li>
                    <li><a href="ManageTickets.php">Tickets</a></li>
                    <li><a href="ManageHotels.php">Hotels</a></li>
                    <li><a href="ManageTours.php" class="active">Tours</a></li>
                    <li><a href="ManageBookings.php">Manage Bookings</a></li>
                    <li><a href="Payments.php">Payments</a></li>
                    <li><a href="Reports.php">Reports</a></li>
                    <li><a href="Settings.php">Settings</a></li>
                    <li><a href="MyProfile.php">My Profile</a></li>
                    <li><a href="homePage.php">Logout</a></li>
                </ul>
      </nav>
    </aside>

    <!-- MAIN -->
    <main class="main-content">
      <header class="admin-header">
        <h1><i class="fa-solid fa-map-location-dot"></i> Manage Tours</h1>
      </header>

      <!-- MAIN CARD -->
      <div class="admin-card">
        <div class="section-actions">
          <form class="search-wrap" method="GET" action="ManageTours.php">
            <input type="text" class="section-search" name="q"
              placeholder="Search by name or destination..."
              value="<?= esc($q) ?>" />
            <button class="mini-btn search-btn" type="submit">
              <i class="fa-solid fa-magnifying-glass"></i> Search
            </button>
          </form>
          <button type="button" class="add-tour-btn" id="openAddTourBtn">
            <i class="fas fa-plus"></i> Add Tour
          </button>
        </div>

      <!-- ✅ FORM CARD (HIDDEN BY DEFAULT) -->
      <div class="tour-form-card" id="tourFormCard" style="display:none;">
        <div class="form-title" style="display:flex; align-items:center; justify-content:space-between;">
          <h2 id="modalTitle"><i class="fas fa-plus-circle"></i> Add Tour</h2>
          <button type="button" class="modal-close" id="closeTourFormBtn">✕</button>
        </div>

        <form id="tourForm" method="POST" action="../controller/ManageToursController.php" class="form-container" enctype="multipart/form-data">
          <input type="hidden" name="action" id="formAction" value="add">
          <input type="hidden" name="old_id" id="oldTourId" value="">

          <div class="form-group">
            <label>Tour ID (Package ID)</label>
            <input type="text" name="id" id="tourId" placeholder="Example: TRV-001" required>
          </div>


          <div class="form-group">
            <label>Tour Name</label>
            <input type="text" name="name" id="tourName" placeholder="Example: Sundarbans Adventure" required>
          </div>

          <div class="form-group">
            <label>Destination</label>
            <input type="text" name="destination" id="tourDestination" placeholder="Example: Khulna" required>
          </div>

          <div class="form-group">
            <label>Duration</label>
            <input type="text" name="duration" id="tourDuration" placeholder="Example: 3 Days / 2 Nights" required>
          </div>

          <div class="form-group">
            <label>Price</label>
            <input type="number" step="0.01" name="price" id="tourPrice" placeholder="Example: 6500" required>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="status" id="tourStatus">
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>

          <div class="form-group" style="grid-column: 1/-1;">
            <label>Includes (e.g. Breakfast, Guide, Transport)</label>
            <input type="text" name="includes_text" id="tourIncludes" placeholder="Example: Breakfast, Guide, Transport">
          </div>

          <div class="form-group" style="grid-column: 1/-1;">
            <label>Tour Image</label>
            <input type="file" name="image" id="tourImage" accept="image/*">
          </div>

          <div class="form-actions" style="grid-column:1/-1; display:flex; gap:10px; justify-content:flex-end;">
            <button type="submit" class="save-btn">Save</button>
            <button type="button" class="cancel-btn" id="cancelTourFormBtn">Cancel</button>
          </div>
        </form>
      </div>

      <!-- TOURS GRID -->
        <?php if (empty($tours)): ?>
          <div style="padding:14px; font-weight:900;">No tours found.</div>
        <?php else: ?>
          <div class="tour-cards-grid">
            <?php foreach ($tours_page as $tour): ?>
              <?php
                $isActive = (strtolower(trim($tour['status'])) === 'active');
                $toggleText = $isActive ? 'Inactive' : 'Active';
                $toggleIcon = $isActive ? 'fa-toggle-off' : 'fa-toggle-on';
              ?>
              <div class="tour-card" style="padding:0; overflow:hidden;">
                <!-- IMAGE ON TOP -->
                <div class="tour-card-img-wrap" style="position: relative;">
                  <?php if (!empty($tour['image'])): ?>
                    <img src="../../User/images/<?= esc($tour['image']) ?>" alt="Tour" style="width:100%; height:180px; object-fit:cover; display:block; border-radius:14px 14px 0 0;">
                  <?php else: ?>
                    <img src="../images/logo.png" alt="Default" style="width:100%; height:180px; object-fit:contain; background:#f0f4f8; display:block; border-radius:14px 14px 0 0;">
                  <?php endif; ?>
                  <span style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.65); color: #fff; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 700; letter-spacing: 0.5px;">ID: <?= esc($tour['id']) ?></span>
                </div>

                <!-- BLUE HEADER -->
                <div class="tour-card-header" style="border-radius:0;">
                  <div class="tour-title">
                    <i class="fas fa-tag"></i> <?= esc($tour['name']) ?>
                  </div>
                  <span class="tour-pill <?= $isActive ? 'active' : 'inactive' ?>">
                    <span class="dot"></span> <?= esc($tour['status']) ?>
                  </span>
                </div>

                <!-- BODY INFO -->
                <div class="tour-card-body" style="padding: 18px;">
                  <div class="info-row"><i class="fas fa-location-dot"></i> Destination: <?= esc($tour['destination']) ?></div>
                  <div class="info-row"><i class="fas fa-calendar-days"></i> Duration: <?= esc($tour['duration']) ?></div>
                  <?php if (!empty($tour['includes_text'])): ?>
                    <div class="info-row"><i class="fas fa-check-circle"></i> Includes: <?= esc($tour['includes_text']) ?></div>
                  <?php endif; ?>
                  <div class="info-row"><i class="fas fa-bangladeshi-taka-sign"></i> Price: <?= number_format((float)$tour['price'], 2) ?></div>
                </div>

                <!-- ACTIONS -->
                <div class="tour-card-actions" style="padding: 0 18px 18px 18px;">
                  <a href="#" class="btn btn-edit edit-btn"
                     data-id="<?= (int)$tour['id'] ?>"
                     data-name="<?= esc($tour['name']) ?>"
                     data-destination="<?= esc($tour['destination']) ?>"
                     data-duration="<?= esc($tour['duration']) ?>"
                     data-price="<?= esc($tour['price']) ?>"
                     data-status="<?= esc($tour['status']) ?>"
                     data-includes="<?= esc($tour['includes_text'] ?? '') ?>">
                    <i class="fas fa-pen-to-square"></i> Edit
                  </a>
                  <a href="#" class="btn btn-toggle toggle-btn" data-id="<?= (int)$tour['id'] ?>">
                    <i class="fas <?= esc($toggleIcon) ?>"></i> <?= esc($toggleText) ?>
                  </a>
                  <?php if (!$isActive): ?>
                    <a href="#" class="btn btn-delete delete-btn"
                       data-enabled="1"
                       data-id="<?= (int)$tour['id'] ?>"
                       data-name="<?= esc($tour['name']) ?>">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  <?php else: ?>
                    <a href="#" class="btn btn-delete btn-disabled delete-btn"
                       data-enabled="0"
                       title="Only inactive tours can be deleted">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if ($total_pages > 1): ?>
          <div class="pagination-bar">
            <div class="pagination-info">
              Showing <?= $offset + 1 ?>–<?= min($offset + $per_page, $total) ?> of <?= $total ?> tours
            </div>
            <div class="pagination-controls">
              <?php if ($current_page > 1): ?>
                <a class="page-btn" href="?q=<?= urlencode($q) ?>&page=<?= $current_page - 1 ?>">
                  <i class="fa-solid fa-chevron-left"></i> Prev
                </a>
              <?php endif; ?>
              <span class="pagination-page">Page <?= $current_page ?> of <?= $total_pages ?></span>
              <?php if ($current_page < $total_pages): ?>
                <a class="page-btn" href="?q=<?= urlencode($q) ?>&page=<?= $current_page + 1 ?>">
                  Next <i class="fa-solid fa-chevron-right"></i>
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

      </div><!-- /admin-card -->

    </main>
  </div>

  <script src="../js/ManageTours.js"></script>
</body>
</html>
