<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink - Your Ability, Our Connection</title>
    <link rel="stylesheet" href="templatemo-glossy-touch.css">
    <style>
        /* Style pour les pages cachées */
        .page {
            display: none;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }
        
        .page.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Animation pour le contenu des pages */
        .page-content {
            animation: fadeInUp 0.6s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- SINGLE NAVIGATION HEADER -->
    <header>
        <div class="container">
            <nav class="glass">
                <div class="logo" onclick="showPage('home')">
                    <div class="logo-icon">
                        <img src="Screenshot_2025-11-01_132947-removebg-preview.png" 
                             alt="AbleLink Logo" 
                             style="width:400px; height:auto;margin-left: 200px;">
                    </div>
                </div>

                <div class="nav-links">
                    <a href="#" onclick="showPage('home')" class="active">Home</a>
                    <a href="#" onclick="showPage('about')">About</a>
                    <a href="#" onclick="showPage('services')">Services</a>
                    <a href="#" onclick="showPage('contact')">Contact</a>
                    
                    <!-- BOUTONS DE CONNEXION -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Si l'utilisateur est connecté -->
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <a href="index1.php">Dashboard</a>
                        <?php endif; ?>
                        <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['user_prenom']); ?>)</a>
                    <?php else: ?>
                        <!-- Si l'utilisateur n'est pas connecté -->
                        <a href="login.php">Sign In</a>
                        <a href="register.php">Sign Up</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- HOME PAGE -->
    <div id="home" class="page active">
        <div class="container">
            <div class="content-wrapper page-content">
                <section class="hero glass">
                    <div class="hero-image">
                        <img src="images/templatemo-futuristic-girl.jpg" alt="Modern Technology Interaction" />
                    </div>
                    <div class="hero-content">
                        <h1>Welcome to AbleLink</h1>
                        <p>AbleLink is a dedicated employment platform connecting companies with talented job seekers with disabilities. Our mission is to foster equal opportunities, social inclusion, and a more diverse workforce, creating a bridge between talent and opportunity. Join us in building a world where every ability counts.</p>
                        <a href="#" class="cta-button" onclick="showPage('about')">Learn More</a>
                    </div>
                </section>

                <section class="features">
                    <div class="feature-card glass">
                        <div class="feature-icon">✨</div>
                        <h3>Inclusive Platform</h3>
                        <p>AbleLink is designed to connect job seekers with disabilities to opportunities, ensuring accessibility and ease of use for everyone.</p>
                    </div>
                    
                    <div class="feature-card glass">
                        <div class="feature-icon">⚡</div>
                        <h3>Fast & Efficient</h3>
                        <p>Our platform matches candidates with relevant jobs quickly, streamlining the hiring process for both companies and job seekers.</p>
                    </div>
                    
                    <div class="feature-card glass">
                        <div class="feature-icon">📱</div>
                        <h3>Fully Responsive</h3>
                        <p>Accessible on all devices, so users can browse jobs, apply, and manage profiles on mobile, tablet, or desktop.</p>
                    </div>

                    <div class="feature-card glass">
                        <div class="feature-icon">🎨</div>
                        <h3>User-Friendly Interface</h3>
                        <p>Intuitive, easy-to-navigate dashboards with interactive elements that make the application process simple and engaging.</p>
                    </div>

                    <div class="feature-card glass">
                        <div class="feature-icon">🔒</div>
                        <h3>Secure & Private</h3>
                        <p>All user data is protected with modern security standards, ensuring privacy for sensitive information.</p>
                    </div>

                    <div class="feature-card glass">
                        <div class="feature-icon">🚀</div>
                        <h3>Easy Access & Integration</h3>
                        <p>AbleLink integrates tools and features for companies and job seekers, making recruitment and job search seamless.</p>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- ABOUT PAGE -->
    <div id="about" class="page">
        <div class="container">
            <div class="content-wrapper">
                <section class="about-content">
                    <div class="about-text glass">
                        <h2>About Our Vision</h2>
                        <p>At AbleLink, we believe in a world where every talent matters. Our vision is to create a digital platform that makes inclusive employment simple, accessible, and empowering for people with disabilities.</p>
                        <p>Founded in 2025, our team is committed to connecting job seekers with opportunities, fostering social inclusion, and supporting companies in building a diverse and equitable workforce.</p>
                        <p>Every feature on AbleLink is designed with care, ensuring that the platform is intuitive, accessible, and impactful, so that both job seekers and employers can navigate it easily and confidently.</p>
                    </div>
                    <div class="stats">
                        <div class="stat-card glass">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Projects Completed</div>
                        </div>
                        <div class="stat-card glass">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Happy Clients</div>
                        </div>
                        <div class="stat-card glass">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Years Experience</div>
                        </div>
                        <div class="stat-card glass">
                            <div class="stat-number">24/7</div>
                            <div class="stat-label">Support Available</div>
                        </div>
                    </div>
                </section>

                <section class="team-section">
                    <h2>Meet Our Team</h2>
                    <div class="team-grid">
                        <div class="team-member glass">
                            <div class="team-avatar">
                                <img src="images/574939411_2029667171126372_3517891937235344246_n_.jpg" alt="Klai Nourhene" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                            </div>
                            <h3>Klai Nourhene</h3>
                            <p class="role">CEO & Founder</p>
                            <p class="bio">Visionary leader with 15+ years in digital innovation, driving our mission to create exceptional user experiences.</p>
                            <div class="team-social">
                                <a href="mailto:nourhene.klai@esprit.tn" title="Email">📧</a>
                                <a href="#" title="LinkedIn">💼</a>
                                <a href="https://github.com/klainourhene1-lab" title="GitHub">💻</a>
                            </div>
                        </div>
                        <div class="team-member glass">
                            <div class="team-avatar">
                                <img src="images/575307934_25404852132486337_3071098944079991408_n.jpg" alt="Missaoui Iness" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                            </div>
                            <h3>Missaoui Iness</h3>
                            <p class="role">Creative Director</p>
                            <p class="bio">Award-winning designer specializing in modern UI/UX, bringing artistic vision to every project.</p>
                            <div class="team-social">
                                <a href="mailto:inees.missaoui@esprit.tn" title="Email">📧</a>
                                <a href="#" title="LinkedIn">💼</a>
                                <a href="https://github.com/ines-missaoui" title="GitHub">💻</a>
                            </div>
                        </div>
                        <div class="team-member glass">
                            <div class="team-avatar">
                                <img src="images/576961852_2674771482871855_772813508712719687_n.png" alt="Jafrar Malek" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                            </div>
                            <h3>Jafrar Malek</h3>
                            <p class="role">Lead Developer</p>
                            <p class="bio">Full-stack expert passionate about clean code and innovative web technologies.</p>
                            <div class="team-social">
                                <a href="mailto:malek.jafrar@esprit.tn" title="Email">📧</a>
                                <a href="#" title="LinkedIn">💼</a>
                                <a href="https://github.com/malek-225" title="GitHub">💻</a>
                            </div>
                        </div>
                        <div class="team-member glass">
                            <div class="team-avatar">
                                <img src="images/573650370_1343476826657664_6225252124692078210_n.jpg" alt="Challouf Mohammed Amin" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                            </div>
                            <h3>Challouf Mohammed Amin</h3>
                            <p class="role">Senior Developer</p>
                            <p class="bio">Frontend specialist with expertise in React and modern JavaScript frameworks.</p>
                            <div class="team-social">
                                <a href="mailto:mohammedamine.challouf@esprit.tn" title="Email">📧</a>
                                <a href="#" title="LinkedIn">💼</a>
                                <a href="https://github.com/AmineChallouf" title="GitHub">💻</a>
                            </div>
                        </div>
                        <div class="team-member glass">
                            <div class="team-avatar">
                                <img src="images/575904854_846894627914391_5495745589115065083_n.jpg" alt="Friaa Adem" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                            </div>
                            <h3>Friaa Adem</h3>
                            <p class="role">UX Designer</p>
                            <p class="bio">User experience expert focused on creating intuitive and accessible digital products.</p>
                            <div class="team-social">
                                <a href="mailto:adem.friaa@esprit.tn" title="Email">📧</a>
                                <a href="#" title="LinkedIn">💼</a>
                                <a href="https://github.com/AdamFriaa" title="GitHub">💻</a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- SERVICES PAGE -->
    <div id="services" class="page">
        <div class="container">
            <div class="content-wrapper">
                <section class="hero glass">
                    <h1>Our Services</h1>
                    <p>Providing comprehensive solutions to connect job seekers with disabilities to inclusive employment opportunities, helping companies build diverse and equitable teams.</p>
                </section>

                <section class="services-grid">
                    <div class="service-card glass">
                        <div class="service-header">
                            <div class="service-icon">🎨</div>
                            <h3>UI/UX Design</h3>
                        </div>
                        <p>Create intuitive and accessible interfaces for job seekers and employers, focusing on usability, clarity, and inclusivity.</p>
                        <ul class="service-features">
                            <li>User Research for Accessibility</li>
                            <li>Wireframing & Prototyping</li>
                            <li>Inclusive Visual Design & Branding</li>
                            <li>Responsive and Assistive-friendly Design</li>
                        </ul>
                    </div>

                    <div class="service-card glass">
                        <div class="service-header">
                            <div class="service-icon">💻</div>
                            <h3>Web Platform Development</h3>
                        </div>
                        <p>Build a robust and scalable employment platform that connects people with disabilities to companies efficiently and securely.</p>
                        <ul class="service-features">
                            <li>Frontend Development (Accessible UI)</li>
                            <li>Backend Integration (Job Matching & Applications)</li>
                            <li>Performance & Accessibility Optimization</li>
                            <li>Data Security & Privacy</li>
                        </ul>
                    </div>

                    <div class="service-card glass">
                        <div class="service-header">
                            <div class="service-icon">📱</div>
                            <h3>Mobile App Development</h3>
                        </div>
                        <p>Develop mobile applications to allow users to browse jobs, apply, and communicate seamlessly on the go.</p>
                        <ul class="service-features">
                            <li>iOS & Android Native Apps</li>
                            <li>Cross-platform Solutions</li>
                            <li>Job Notifications & Alerts</li>
                            <li>Maintenance & Updates</li>
                        </ul>
                    </div>

                    <div class="service-card glass">
                        <div class="service-header">
                            <div class="service-icon">🚀</div>
                            <h3>Digital Strategy & Support</h3>
                        </div>
                        <p>Provide guidance and tools to maximize engagement and inclusive hiring success for both job seekers and companies.</p>
                        <ul class="service-features">
                            <li>Inclusive Recruitment Strategy</li>
                            <li>Analytics & Reporting</li>
                            <li>Growth & Outreach Strategy</li>
                            <li>Technology Consulting</li>
                        </ul>
                    </div>

                    <div class="service-card glass">
                        <div class="service-header">
                            <div class="service-icon">☁️</div>
                            <h3>Cloud & Infrastructure</h3>
                        </div>
                        <p>Ensure the platform is scalable, reliable, and accessible for users anytime, anywhere.</p>
                        <ul class="service-features">
                            <li>Cloud Hosting & Migration</li>
                            <li>DevOps & Automation</li>
                            <li>Infrastructure Management</li>
                            <li>24/7 Support & Monitoring</li>
                        </ul>
                    </div>

                    <div class="service-card glass">
                        <div class="service-header">
                            <div class="service-icon">🔐</div>
                            <h3>Security & Privacy</h3>
                        </div>
                        <p>Protect the data and privacy of all users, including sensitive personal information.</p>
                        <ul class="service-features">
                            <li>Security Auditing & Threat Protection</li>
                            <li>Penetration Testing</li>
                            <li>Secure Job Application Management</li>
                            <li>Data Privacy Compliance</li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- CONTACT PAGE -->
    <div id="contact" class="page">
        <div class="container">
            <div class="content-wrapper">
                <section class="contact-grid">
                    <div class="contact-form glass">
                        <h2>Get In Touch</h2>
                        <form id="contactForm">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" placeholder="What's this about?">
                            </div>
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" placeholder="Tell us about your project..." required></textarea>
                            </div>
                            <button type="submit" class="cta-button">Send Message</button>
                        </form>
                    </div>

                    <div class="contact-info glass">
                        <h2>Contact Information</h2>
                        
                        <div class="contact-item">
                            <div class="contact-item-icon">📧</div>
                            <div class="contact-item-text">
                                <h4>Email</h4>
                                <p>AbleLink@gmail.com</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-item-icon">📞</div>
                            <div class="contact-item-text">
                                <h4>Phone</h4>
                                <p>+126 96169711</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-item-icon">📍</div>
                            <div class="contact-item-text">
                                <h4>Address</h4>
                                <p>Ariana<br>Esprit,Bloc H</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-item-icon">🕒</div>
                            <div class="contact-item-text">
                                <h4>Business Hours</h4>
                                <p>Mon-Fri: 9AM-6PM<br>Sat-Sun: 10AM-4PM</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="contact-map-section">
                    <div class="contact-map glass">
                        <h2>Find Us</h2>
                        <div class="map-container">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3174.123456789!2d10.188765!3d36.899876!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12fd34abcd5678%3A0x1234567890abcdef!2sBloc%20H%2C%20ESPRIT%2C%20Ariana!5e0!3m2!1sen!2stn!4v1699999999999!5m2!1sen!2stn"
                                width="100%"
                                height="500"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- SINGLE FOOTER -->
    <div id="footer">
        <div class="container">
            <footer class="glass">
                <div class="footer-content">
                    <div class="footer-links">
                        <a href="#" onclick="showPage('about')">About Us</a>
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">XML Sitemap</a>
                        <a href="#" onclick="showPage('contact')">Contact</a>
                    </div>
                    <div class="copyright">
                        <p>&copy; 2025 AbleLink. All rights reserved. Empowering inclusive employment. Crafted with modern web technologies.</p>
                        Provided by <a rel="nofollow" href="#" target="_blank">AbleLink</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        function showPage(pageId) {
            // Hide all pages
            document.querySelectorAll('.page').forEach(page => {
                page.classList.remove('active');
            });
            
            // Show selected page
            document.getElementById(pageId).classList.add('active');
            
            // Update navigation active states
            document.querySelectorAll('.nav-links a').forEach(link => {
                link.classList.remove('active');
            });
            
            // Set active state for clicked link
            event.target.classList.add('active');
            
            // Scroll to top when changing pages
            window.scrollTo(0, 0);
        }

        // Optional: Add smooth scrolling for internal links
        document.addEventListener('DOMContentLoaded', function() {
            // Handle page load - ensure home is active
            if (!document.querySelector('.page.active')) {
                showPage('home');
            }
        });
    </script>
</body>
</html>