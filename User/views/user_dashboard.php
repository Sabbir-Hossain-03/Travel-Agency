<?php
include 'session_check.php';
include 'dark_mode.php'; // Include the user theme helper
include '../database/dbconnection.php';

// Check if theme session is set
$session_theme_set = isset($_SESSION['theme']);
$is_dark = $session_theme_set && $_SESSION['theme'] === 'dark';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Avestra Travel Agency</title>
    <!-- Premium Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../styleSheets/user.css">
    <link rel="stylesheet" href="../styleSheets/user_dashboard.css">
    <link rel="stylesheet" href="../styleSheets/homePage.css">
    <link rel="stylesheet" href="../styleSheets/user-dark-mode.css">
</head>
<body class="<?= $session_theme_set ? ($is_dark ? 'dark-mode' : 'light-mode') : '' ?>">
    <script>
        // Fallback for session-less theme application
        if (!<?= $session_theme_set ? 'true' : 'false' ?>) {
            const theme = localStorage.getItem('theme') || 'light';
            document.body.classList.add(theme + '-mode');
        }
    </script>

<?php include 'nav.php'; ?>

<!-- Premium Hero section -->
<div class="dash-hero">
    <div class="dash-hero-content">
        <h1>Welcome, <span><?= htmlspecialchars($_SESSION['username']); ?></span> <i class="fas fa-hand-sparkles"></i></h1>
        <p>Your next great adventure begins here.</p>
    </div>
</div>

<div class="dashboard-wrapper">
    <!-- Service Grid Section -->
    <section class="dash-section" style="background: transparent; box-shadow: none; border: none; padding: 0;">
        <div class="dash-section-header" style="text-align: center; margin-bottom: 40px; display: none;">
            <!-- Hidden header because cards visually replace it -->
        </div>
        
        <div class="dash-services-grid">
            <a href="start_Booking.php" class="service-card card-flight">
                <div class="service-icon"><i class="fas fa-plane-departure"></i></div>
                <h3>Book Travel</h3>
                <p>Find the best routes on flights, buses, and modern trains.</p>
                <span class="service-link">Find Tickets <i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="find_Hotels.php" class="service-card card-hotel">
                <div class="service-icon"><i class="fas fa-hotel"></i></div>
                <h3>Stay in Comfort</h3>
                <p>Discover premium stays, luxury hotels, and top-rated resorts.</p>
                <span class="service-link">Browse Hotels <i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="explore_Tour_Packages.php" class="service-card card-tour">
                <div class="service-icon"><i class="fas fa-map-marked-alt"></i></div>
                <h3>Explore Tours</h3>
                <p>Curated adventures and unforgettable guided trips globally.</p>
                <span class="service-link">View Packages <i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="bookingHistory.php" class="service-card card-history">
                <div class="service-icon"><i class="fas fa-history"></i></div>
                <h3>Your Journeys</h3>
                <p>Review and securely manage all your past and upcoming travels.</p>
                <span class="service-link">View History <i class="fas fa-arrow-right"></i></span>
            </a>
        </div>
    </section>

    <!-- Travel Discovery Section -->
    <section class="dash-section" id="destinations" style="background: transparent; box-shadow: none; border: none; padding: 0;">
        <div class="dash-section-header">
            <h2>Popular Destinations</h2>
            <p>Get inspired by some of the most breathtaking places.</p>
        </div>
        
        <div class="dash-destinations-grid">
            <div class="destination-card">
                <a href="https://www.youtube.com/watch?v=JxCDg3qZBOE" target="_main">
                    <img src="../images/coxs.png" alt="Cox's Bazar">
                    <div class="destination-overlay">
                        <h3>Cox's Bazar</h3>
                        <p>Longest natural sandy sea beach</p>
                    </div>
                </a>
            </div>
            <div class="destination-card">
                <a href="https://www.youtube.com/watch?v=-Thd47J4o6g" target="_main">
                    <img src="../images/sajek.png" alt="Sajek Valley">
                    <div class="destination-overlay">
                        <h3>Sajek Valley</h3>
                        <p>The Queen of Hills</p>
                    </div>
                </a>
            </div>
            <div class="destination-card">
                <a href="https://www.youtube.com/watch?v=YORirX6i0rQ" target="_main">
                    <img src="../images/bandarban.png" alt="Bandarban">
                    <div class="destination-overlay">
                        <h3>Bandarban</h3>
                        <p>Lush green hills and waterfalls</p>
                    </div>
                </a>
            </div>
            <div class="destination-card">
                <a href="https://www.youtube.com/watch?v=3oILOKS7M2w" target="_main">
                    <img src="../images/tanguar.png" alt="Tanguar Haor">
                    <div class="destination-overlay">
                        <h3>Tanguar Haor</h3>
                        <p>Seasonal wetland paradise</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- About and Contact Split Section -->
    <div class="dash-split-section">
        <section class="about-card">
            <h2>About Us</h2>
            <p>Avestra Travel Agency transforms the way you discover, plan, and book your dream journeys. From seamless transportation setups and luxury hotels to curated local tours, we bring flawless travel experiences to life.</p>
            <p>Founded by passionate explorers and powered by state-of-the-art technology, we are deeply committed to making every trip you take easier, safer, and infinitely more inspiring.</p>
            <ul class="about-list">
                <li><i class="fas fa-shield-check"></i> Globally Trusted Partners</li>
                <li><i class="fas fa-headset"></i> 24/7 Dedicated Support</li>
                <li><i class="fas fa-tags"></i> Unbeatable Price Guarantee</li>
            </ul>
        </section>

        <section class="contact-card">
            <h2>Contact Us</h2>
            <p>Have a question or need to build a custom trip? Send us a message and our travel concierges will assist you.</p>
            <div id="contact-message" class="contact-feedback"></div>
            
            <form class="simple-contact-form" id="contactForm">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label for="message">How can we help?</label>
                    <textarea id="message" name="message" rows="3" placeholder="Tell us about your next adventure..." required></textarea>
                </div>
                <button type="submit" class="primary-btn">Send Message <i class="fas fa-paper-plane" style="margin-left: 8px;"></i></button>
            </form>
            
            <div class="contact-info-footer">
                <span><i class="fas fa-phone-alt"></i> +88 0123 456 789</span>
                <span><i class="fas fa-envelope-open-text"></i> support@avestra.travel</span>
            </div>
        </section>
    </div>
</div>

<script src="../js/theme.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // AJAX Contact Form
        const contactForm = document.getElementById('contactForm');
        const contactMessage = document.getElementById('contact-message');
        
        if (contactForm && contactMessage) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(contactForm);
                const submitBtn = contactForm.querySelector('button[type="submit"]');
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Sending... <i class="fas fa-spinner fa-spin" style="margin-left:8px;"></i>';
                
                fetch('../../Admin/controller/ContactFormHandler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        contactMessage.className = 'contact-feedback success';
                        contactMessage.textContent = data.message;
                        contactForm.reset();
                    } else {
                        contactMessage.className = 'contact-feedback error';
                        contactMessage.textContent = data.message;
                    }
                    contactMessage.style.display = 'block';
                    setTimeout(() => contactMessage.style.display = 'none', 5000);
                })
                .catch(error => {
                    contactMessage.className = 'contact-feedback error';
                    contactMessage.textContent = 'Error sending message.';
                    contactMessage.style.display = 'block';
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Send Message <i class="fas fa-paper-plane" style="margin-left: 8px;"></i>';
                });
            });
        }
    });
</script>
</body>
<?php include 'footer.php'; ?>
</html>