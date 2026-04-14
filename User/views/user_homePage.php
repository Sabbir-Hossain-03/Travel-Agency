<?php
header("Location: user_dashboard.php");
exit();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avestra Travel Agency : Book Tickets, Hotels, Packages</title>
    <link rel="stylesheet" href="../styleSheets/homePage.css">
    <link rel="stylesheet" href="../styleSheets/user-dark-mode.css">
    <script>
        // Intelligent theme application
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const sessionThemeSet = <?= $session_theme_set ? 'true' : 'false' ?>;
            const currentTheme = '<?= $current_theme ?>';
            
            if (sessionThemeSet) {
                localStorage.setItem('theme', currentTheme);
                document.documentElement.setAttribute('data-theme', currentTheme);
            } else if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            }
        })();
    </script>
    <link rel="icon" href="../images/logo.png" type="image/png">
</head>

<body class="<?= $session_theme_set ? ($is_dark ? 'dark-mode' : 'light-mode') : '' ?>">
    <script>
        if (!<?= $session_theme_set ? 'true' : 'false' ?>) {
            const theme = localStorage.getItem('theme') || 'light';
            document.body.classList.add(theme + '-mode');
        }
    </script>
    <header>
        <button id="mode-toggle">🌙</button>
        <div class="container">
            <div class="logo-container">
                <a href="user_homePage.php">
                    <img src="../images/logo.png" alt="Avestra Travel Agency Logo">
                </a>
            </div>
            <div id="branding">
                <h1><span class="highlight">Avestra</span> Travel Agency</h1>
            </div>

            <nav>
                <ul>
                    <li class="current"><a href="user_homePage.php"> Home</a></li>
                    <li><a href="user_dashboard.php" > Dashboard</a></li>
                    <li><a href="#services-section-tickets"><img src="../images/ticket-detailed-fill.svg"
                                alt="Tickets Icon" class="ticket-icon"> Tickets</a></li>
                    <li><a href="#services-section-hotel"><img src="../images/house-add.svg" alt="Hotel Icon"
                                class="hotel-icon"> Hotel</a></li>
                    <li><a href="#services-section-tour"><img src="../images/suitcase-lg.svg" alt="Tour Icon"
                                class="tour-icon"> Tour Packages</a></li>
                    <li><a href="#contact-section"> Contact Us</a></li>
                    <li><a href="#about-section"> About Us</a></li>
                  
                </ul>
            </nav>
        </div>
    </header>
    <section id="showcase">
        <div class="container">
            <h1>Welcome to Avestra Travel Agency</h1>
            <p>Travel today, treasure forever.</p>
        </div>
    </section>
    <section id="travel-cards">
        <div class="container">
            <h2>Discover Amazing Destinations</h2>
            <div class="travel-card-grid">
                <div class="travel-card">
                    <a href="https://www.youtube.com/watch?v=JxCDg3qZBOE" target="_main">
                    <img src="../images/coxs.png" alt="Cox's Bazar">
                    </a>
                    <div class="travel-card-overlay">
                        <h3>Cox's Bazar</h3>
                        <p>The world's longest natural sandy sea beach.</p>
                    </div>
                </div>
                <div class="travel-card">
                    <a href="https://www.youtube.com/watch?v=-Thd47J4o6g" target="_main">
                    <img src="../images/sajek.png" alt="Sajek Valley">
                    </a>
                    <div class="travel-card-overlay">
                        <h3>Sajek Valley</h3>
                        <p>This place is referred to as the "Queen of Hills" it is known for its greenery and dense forests.</p>
                    </div>
                </div>
                <div class="travel-card">
                    <a href="https://www.youtube.com/watch?v=YORirX6i0rQ" target="_main">
                    <img src="../images/bandarban.png" alt="Bandarban">
                    </a>
                    <div class="travel-card-overlay">
                        <h3>Bandarban</h3>
                        <p>This place features lush green hills, exotic waterfalls, serene lakes, and misty viewpoints.</p>
                    </div>
                </div>
                <div class="travel-card">
                    <a href="https://www.youtube.com/watch?v=3oILOKS7M2w" target="_main">
                    <img src="../images/tanguar.png" alt="Tanguar Haor">
                    </a>
                    <div class="travel-card-overlay">
                        <h3>Tanguar Haor</h3>
                        <p>A seasonal wetland paradise for migratory birds, famous for its dramatic seasonal changes.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services-section">
        <div class="container services-grid">
            <div id="services-section-tickets" class="service-card">
                <img src="../images/ticket-detailed-fill.svg" alt="Tickets" class="service-icon">
                <h3>Tickets</h3>
                <p>Book flights, trains, and buses in minutes. Compare routes and prices across trusted carriers.</p>
                <!-- <a href="BookTickets.php" class="button_1">Start Booking</a> -->
                <a href="start_Booking.php" class="button_1">Start Booking</a>
            </div>
            <div id="services-section-hotel" class="service-card">
                <img src="../images/house-add.svg" alt="Hotel" class="service-icon">
                <h3>Hotel</h3>
                <p>Find the right stay — from boutique stays to luxury resorts — with honest reviews and great deals.</p>
                <!-- <a href="BookHotel.php" class="button_1">Find Hotels</a> -->
                <a href="find_Hotels.php" class="button_1">Find Hotels</a>
            </div>
            <div id="services-section-tour" class="service-card">
                <img src="../images/suitcase-lg.svg" alt="Tour" class="service-icon">
                <h3>Tour Packages</h3>
                <p>Browse guided tours and experiences tailored to your interests, from city highlights to offbeat adventures.</p>
                <!-- <a href="BookTour.php" class="button_1">Explore Tours</a> -->
                <a href="explore_Tour_Packages.php" class="button_1">Explore Tour Packages</a>
            </div>
        </div>
    </section>

    <section id="contact-section">
        <div class="container contact-grid">
            <div class="contact-copy">
                <h2>Contact Us</h2>
                <p>Questions about bookings, custom trips, or support? We are here to help every day.</p>
                <ul>
                    <li><strong>Call:</strong> +88012345678910</li>
                    <li><strong>Email:</strong> support@avestra.travel</li>
                    <li><strong>Visit:</strong> Kuril,khilkhet, Dhaka, Bangladesh</li>
                </ul>
            </div>
            <div>
                <div id="contact-message" style="display:none; margin-bottom:15px;" class="alert"></div>
                <form class="contact-form" id="contactForm">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Your name" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required>

                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="4" placeholder="Tell us how we can help" required></textarea>

                    <button type="submit" class="button_1">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <section id="about-section">
        <div class="container about-grid">
            <div class="about-copy">
                <h2>About Us</h2>
                <p>Avestra Travel Agency helps travelers discover, plan, and book memorable journeys. From transportation and hotels to curated tours, we bring seamless travel experiences to life.</p>
                <p>Founded by explorers, powered by technology, and guided by customer love — we’re committed to making every trip easier and more inspiring.</p>
            </div>
            <div class="about-highlights">
                <div class="highlight-card">
                    <h3>Trusted Partners</h3>
                    <p>We work with globally recognized airlines, hotels, and tour operators.</p>
                </div>
                <div class="highlight-card">
                    <h3>Support 24/7</h3>
                    <p>Our team is here for you before, during, and after your trip.</p>
                </div>
                <div class="highlight-card">
                    <h3>Best Prices</h3>
                    <p>Competitive rates and exclusive deals, without hidden fees.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; <?= date('Y') ?> Avestra Travel Agency. All rights reserved.</p>
    </footer>


    <script src="../js/theme.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            applyStoredTheme();
            wireThemeToggle('mode-toggle');

            const logoLink = document.querySelector('.logo-container a');
            if (logoLink) {
                logoLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    window.location.href = 'loader.php';
                });
            }

            // AJAX Contact Form
            const contactForm = document.getElementById('contactForm');
            const contactMessage = document.getElementById('contact-message');
            
            if (contactForm && contactMessage) {
                contactForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(contactForm);
                    const submitBtn = contactForm.querySelector('button[type="submit"]');
                    
                    // Disable button during submission
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Sending...';
                    
                    fetch('../../Admin/controller/ContactFormHandler.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            contactMessage.style.color = '#155724';
                            contactMessage.style.backgroundColor = '#d4edda';
                            contactMessage.style.borderColor = '#c3e6cb';
                            contactMessage.style.padding = '15px';
                            contactMessage.style.borderRadius = '5px';
                            contactMessage.textContent = data.message;
                            contactForm.reset();
                        } else {
                            contactMessage.style.color = '#721c24';
                            contactMessage.style.backgroundColor = '#f8d7da';
                            contactMessage.style.borderColor = '#f5c6cb';
                            contactMessage.style.padding = '15px';
                            contactMessage.style.borderRadius = '5px';
                            contactMessage.textContent = data.message;
                        }
                        contactMessage.style.display = 'block';
                        
                        // Hide message after 5 seconds
                        setTimeout(() => {
                            contactMessage.style.display = 'none';
                        }, 5000);
                    })
                    .catch(error => {
                        contactMessage.style.color = '#721c24';
                        contactMessage.style.backgroundColor = '#f8d7da';
                        contactMessage.style.borderColor = '#f5c6cb';
                        contactMessage.style.padding = '15px';
                        contactMessage.style.borderRadius = '5px';
                        contactMessage.textContent = 'Error sending message. Please try again.';
                        contactMessage.style.display = 'block';
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Send Message';
                    });
                });
            }
        });
    </script>
</body>
</html>