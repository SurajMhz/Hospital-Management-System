<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>CityMed Hospital Management System</title>

  <!-- bootstrap-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <!-- Google Fonts -->
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet">
  <!-- Your Custom CSS (always LAST so it can override Bootstrap) -->
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNavbar">
    <div class="container">

      <!-- Hospital Brand / Logo -->
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <div class="brand-icon">
          <i class="bi bi-heart-pulse-fill"></i>
        </div>
        <span class="brand-name">CityMed</span>
      </a>

      <!-- Menu  toggle-->
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Nav Links -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto gap-1">
          <li class="nav-item">
            <a class="nav-link" href="#home">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#about">About Us</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#services">Services</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#doctors">Doctors</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#contact">Contact</a>
          </li>
        </ul>

        <!-- Login and Register Buttons -->
        <div class="d-flex gap-2">
          <a href="./Pages/login.php" class="btn btn-outline-light btn-sm px-3">Login</a>
          <a href="./Pages/register.php" class="btn btn-primary btn-sm px-3">Register</a>
        </div>
      </div>

    </div>
  </nav>


  <!--  Hero section-->
  <section class="hero-section" id="home">
    <div class="hero-overlay">
      <div class="container hero-content text-center text-white">
        <p class="hero-tagline">Trusted Healthcare Since 1995</p>
        <h1 class="display-3 fw-bold mb-3">Your Health Is <br><span class="text-highlight">Our Priority</span></h1>
        <p class="lead mb-5 hero-sub">Compassionate care, expert doctors, and modern facilities<br>for you and your
          family — 24 hours a day, 7 days a week.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="#contact" class="btn btn-primary btn-lg px-5 py-3">
            <i class="bi bi-calendar-check me-2"></i>Book Appointment
          </a>
          <a href="#about" class="btn btn-outline-light btn-lg px-5 py-3">
            Learn More <i class="bi bi-arrow-right ms-2"></i>
          </a>
        </div>
      </div>
    </div>
  </section>


  <!-- Stats section  -->
  <section class="stats-section py-5" id="stats">
    <div class="container">
      <div class="row text-center g-4">

        <div class="col-6 col-md-3">
          <div class="stat-card">
            <i class="bi bi-people-fill stat-icon text-primary"></i>
            <h2 class="stat-number">50+</h2>
            <p class="stat-label">Expert Doctors</p>
          </div>
        </div>

        <div class="col-6 col-md-3">
          <div class="stat-card">
            <i class="bi bi-person-heart stat-icon text-success"></i>
            <h2 class="stat-number">10,000+</h2>
            <p class="stat-label">Happy Patients</p>
          </div>
        </div>

        <div class="col-6 col-md-3">
          <div class="stat-card">
            <i class="bi bi-building-fill-cross stat-icon text-danger"></i>
            <h2 class="stat-number">15</h2>
            <p class="stat-label">Departments</p>
          </div>
        </div>

        <div class="col-6 col-md-3">
          <div class="stat-card">
            <i class="bi bi-clock-fill stat-icon text-warning"></i>
            <h2 class="stat-number">24/7</h2>
            <p class="stat-label">Emergency Care</p>
          </div>
        </div>

      </div>
    </div>
  </section>


  <!--  About section  -->
  <section class="py-5 bg-light" id="about">
    <div class="container">
      <div class="row align-items-center g-5">

        <!-- Left: Image -->
        <div class="col-md-6">
          <div class="about-img-wrap">
          
            <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=600&q=80" alt="Hospital Building"
              class="img-fluid rounded-4 shadow-lg">
            <div class="about-badge">
              <i class="bi bi-award-fill text-warning fs-4"></i>
              <div>
                <strong>Award Winning</strong>
                <small class="d-block text-muted">Hospital of the Year 2024</small>
              </div>
            </div>
          </div>
        </div>

        
        <div class="col-md-6">
          <p class="section-label text-primary">WHO WE ARE</p>
          <h2 class="fw-bold mb-3">Dedicated to Providing<br>Exceptional Healthcare</h2>
          <p class="text-muted mb-3">
            CityMed Hospital has been serving the community for over 25 years with a team of highly qualified doctors,
            nurses, and support staff committed to delivering the best possible care.
          </p>
          <p class="text-muted mb-4">
            We combine cutting-edge medical technology with a patient-first approach to ensure every
            individual who walks through our doors receives personalized, compassionate treatment.
          </p>

          <div class="row g-3 mb-4">
            <div class="col-6">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-primary"></i>
                <span>ISO Certified</span>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-primary"></i>
                <span>Modern Equipment</span>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-primary"></i>
                <span>Expert Specialists</span>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-primary"></i>
                <span>Insurance Accepted</span>
              </div>
            </div>
          </div>

          <a href="#contact" class="btn btn-primary px-4">Get in Touch</a>
        </div>

      </div>
    </div>
  </section>


  <!-- Services Section -->
  <section class="py-5" id="services">
    <div class="container">

      <div class="section-heading text-center mb-5">
        <p class="section-label text-primary">WHAT WE OFFER</p>
        <h2 class="fw-bold">Our Medical Services</h2>
        <div class="heading-line"></div>
      </div>

      <div class="row g-4">

        <div class="col-md-4 col-sm-6">
          <div class="service-card text-center p-4">
            <div class="service-icon-wrap bg-primary-soft mb-3">
              <i class="bi bi-heart-pulse fs-2 text-primary"></i>
            </div>
            <h5 class="fw-semibold">Cardiology</h5>
            <p class="text-muted small">Expert heart care including diagnosis, treatment, and cardiac surgery.</p>
          </div>
        </div>

        <div class="col-md-4 col-sm-6">
          <div class="service-card text-center p-4">
            <div class="service-icon-wrap bg-success-soft mb-3">
              <i class="bi bi-brain fs-2 text-success"></i>
            </div>
            <h5 class="fw-semibold">Neurology</h5>
            <p class="text-muted small">Comprehensive care for brain, spine, and nervous system disorders.</p>
          </div>
        </div>

        <div class="col-md-4 col-sm-6">
          <div class="service-card text-center p-4">
            <div class="service-icon-wrap bg-danger-soft mb-3">
              <i class="bi bi-bandaid fs-2 text-danger"></i>
            </div>
            <h5 class="fw-semibold">Orthopedics</h5>
            <p class="text-muted small">Bone, joint, and muscle treatments including surgery and physiotherapy.</p>
          </div>
        </div>

        <div class="col-md-4 col-sm-6">
          <div class="service-card text-center p-4">
            <div class="service-icon-wrap bg-warning-soft mb-3">
              <i class="bi bi-emoji-smile fs-2 text-warning"></i>
            </div>
            <h5 class="fw-semibold">Pediatrics</h5>
            <p class="text-muted small">Specialized medical care for infants, children, and adolescents.</p>
          </div>
        </div>

        <div class="col-md-4 col-sm-6">
          <div class="service-card text-center p-4">
            <div class="service-icon-wrap bg-primary-soft mb-3">
              <i class="bi bi-activity fs-2 text-primary"></i>
            </div>
            <h5 class="fw-semibold">Emergency Care</h5>
            <p class="text-muted small">Round-the-clock emergency services with a fully equipped trauma unit.</p>
          </div>
        </div>

        <div class="col-md-4 col-sm-6">
          <div class="service-card text-center p-4">
            <div class="service-icon-wrap bg-success-soft mb-3">
              <i class="bi bi-droplet-half fs-2 text-success"></i>
            </div>
            <h5 class="fw-semibold">Laboratory</h5>
            <p class="text-muted small">Advanced diagnostic lab with fast and accurate test results.</p>
          </div>
        </div>

      </div>
    </div>
  </section>


  <!-- Doctors section -->
  <section class="py-5 bg-light" id="doctors">
    <div class="container">

      <div class="section-heading text-center mb-5">
        <p class="section-label text-primary">MEET THE TEAM</p>
        <h2 class="fw-bold">Our Expert Doctors</h2>
        <div class="heading-line"></div>
      </div>

      <div class="row g-4 justify-content-center">

        <div class="col-md-3 col-sm-6">
          <div class="doctor-card text-center p-4">
            <img src="https://i.pravatar.cc/150?img=11" alt="Doctor" class="rounded-circle doctor-img mb-3">
            <h6 class="fw-bold mb-1">Dr. Sarah Johnson</h6>
            <span class="badge bg-primary-soft text-primary mb-2">Cardiologist</span>
            <p class="text-muted small">15 years of experience in cardiac care.</p>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="doctor-card text-center p-4">
            <img src="https://i.pravatar.cc/150?img=33" alt="Doctor" class="rounded-circle doctor-img mb-3">
            <h6 class="fw-bold mb-1">Dr. Michael Chen</h6>
            <span class="badge bg-success-soft text-success mb-2">Neurologist</span>
            <p class="text-muted small">Specialist in brain and nervous system disorders.</p>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="doctor-card text-center p-4">
            <img src="https://i.pravatar.cc/150?img=47" alt="Doctor" class="rounded-circle doctor-img mb-3">
            <h6 class="fw-bold mb-1">Dr. Priya Sharma</h6>
            <span class="badge bg-danger-soft text-danger mb-2">Pediatrician</span>
            <p class="text-muted small">Dedicated to children's health and development.</p>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="doctor-card text-center p-4">
            <img src="https://i.pravatar.cc/150?img=52" alt="Doctor" class="rounded-circle doctor-img mb-3">
            <h6 class="fw-bold mb-1">Dr. James Rai</h6>
            <span class="badge bg-warning-soft text-warning mb-2">Orthopedic</span>
            <p class="text-muted small">Expert in bone and joint reconstruction.</p>
          </div>
        </div>

      </div>
    </div>
  </section>


  <!-- Contact Section -->
  <section class="py-5" id="contact">
    <div class="container">



      <div class="row g-5 align-items-start">

        <!-- Left: Contact Info -->
        <div class="col-md-5">
          <h5 class="fw-semibold mb-4">Get In Touch</h5>

          <div class="d-flex gap-3 mb-4">
            <div class="contact-icon-wrap bg-primary text-white">
              <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div>
              <strong>Address</strong>
              <p class="text-muted mb-0 small">123 Medical Avenue, Kathmandu, Nepal</p>
            </div>
          </div>

          <div class="d-flex gap-3 mb-4">
            <div class="contact-icon-wrap bg-success text-white">
              <i class="bi bi-telephone-fill"></i>
            </div>
            <div>
              <strong>Phone</strong>
              <p class="text-muted mb-0 small">+977 01-4567890</p>
            </div>
          </div>

          <div class="d-flex gap-3 mb-4">
            <div class="contact-icon-wrap bg-danger text-white">
              <i class="bi bi-envelope-fill"></i>
            </div>
            <div>
              <strong>Email</strong>
              <p class="text-muted mb-0 small">info@citymedhospital.com</p>
            </div>
          </div>

          <div class="d-flex gap-3">
            <div class="contact-icon-wrap bg-warning text-white">
              <i class="bi bi-clock-fill"></i>
            </div>
            <div>
              <strong>Hours</strong>
              <p class="text-muted mb-0 small">Mon–Sat: 7:00 AM – 9:00 PM<br>Emergency: 24/7</p>
            </div>
          </div>
        </div>

        <!-- Right: Appointment Form -->


      </div>
    </div>
  </section>


  <!-- Footer-->
  <footer class="bg-dark text-white pt-5 pb-3">
    <div class="container">
      <div class="row g-4 mb-4">

        <div class="col-md-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-heart-pulse-fill text-primary fs-4"></i>
            <span class="fw-bold fs-5">CityMed Hospital</span>
          </div>
          <p class="text-secondary small">Dedicated to your health and wellbeing since 1995. Serving the community with
            expert care and modern facilities.</p>
        </div>

        <div class="col-md-2">
          <h6 class="fw-semibold mb-3">Quick Links</h6>
          <ul class="list-unstyled">
            <li><a href="#home" class="footer-link">Home</a></li>
            <li><a href="#about" class="footer-link">About Us</a></li>
            <li><a href="#services" class="footer-link">Services</a></li>
            <li><a href="#doctors" class="footer-link">Doctors</a></li>
            <li><a href="#contact" class="footer-link">Contact</a></li>
          </ul>
        </div>

        <div class="col-md-3">
          <h6 class="fw-semibold mb-3">Services</h6>
          <ul class="list-unstyled">
            <li><a href="#services" class="footer-link">Cardiology</a></li>
            <li><a href="#services" class="footer-link">Neurology</a></li>
            <li><a href="#services" class="footer-link">Orthopedics</a></li>
            <li><a href="#services" class="footer-link">Pediatrics</a></li>
            <li><a href="#services" class="footer-link">Emergency Care</a></li>
          </ul>
        </div>

        <div class="col-md-3">
          <h6 class="fw-semibold mb-3">Emergency</h6>
          <p class="text-secondary small">24/7 Emergency Helpline:</p>
          <p class="text-primary fw-bold fs-5">+977 01-4567890</p>
          <p class="text-secondary small">123 Medical Avenue,<br>Kathmandu, Nepal</p>
        </div>

      </div>

      <hr class="border-secondary">
      <p class="text-center text-secondary small mb-0">
        &copy; 2025 CityMed Hospital Management System. All rights reserved.
      </p>
    </div>
  </footer>


  <!-- Bootstrap JS link -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Navbar scroll effect s-->
  <script>
    window.addEventListener('scroll', function () {
      const navbar = document.getElementById('mainNavbar');
      if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
      } else {
        navbar.classList.remove('navbar-scrolled');
      }
    });
  </script>

</body>

</html>