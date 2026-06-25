<?php
  session_start();
  if(isset($_SESSION['usertype'])){
    if($_SESSION['usertype'] == "admin"){
      header("Location: admin/index.php");
    }
    if($_SESSION['usertype'] == "student"){
      header("Location: student/index.php");
    }
    if($_SESSION['usertype'] == "librarian"){
      header("Location: librarian/index.php");
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BookByte Library</title>
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/main.css">
  <style>
    
  </style>
</head>
<body>
<!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">🕮 BookByte Library</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#featured">Catalog</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modallogin">Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section id="home">
    <div class="overlay"></div>
    <div class="particles">
      <span></span><span></span><span></span><span></span><span></span>
    </div>

    <div class="hero-content">
      <h1>Welcome to BookByte Library</h1>
      <p>Explore knowledge that transforms – Every Byte, a World of Books.</p>
      <a href="#featured" class="btn btn-warning btn-lg">📚 Get Started</a>
    </div>
  </section>

  <!-- Featured Books -->
  <section id="featured" class="py-5">
    <div class="container text-center">
      <h2 class="fw-bold mb-5">✨ Featured Books</h2>
      <div class="row g-4">

        <div class="col-md-3">
          <div class="card book-card shadow-sm">
            <img src="assets/image/book1.png" class="card-img-top" height= "300px" alt="Book 1">
            <div class="card-body">
              <h5 class="card-title">ATMOSPHERE</h5>
              <p class="card-text">Taylor Jenkins Reid</p>
              <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card book-card shadow-sm">
            <img src="assets/image/book2.png" class="card-img-top" height= "300px" alt="Book 2">
            <div class="card-body">
              <h5 class="card-title">THE HOUSEMAID</h5>
              <p class="card-text">Freida McFadden</p>
              <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card book-card shadow-sm">
            <img src="assets/image/book3.png" class="card-img-top" height= "300px" alt="Book 3">
            <div class="card-body">
              <h5 class="card-title">LIGHTS OUT</h5>
              <p class="card-text">Navessa Allen</p>
              <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card book-card shadow-sm">
            <img src="assets/image/book4.png" class="card-img-top" height= "300px" alt="Book 4">
            <div class="card-body">
              <h5 class="card-title">BOOK 4</h5>
              <p class="card-text">Author Name</p>
              <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card book-card shadow-sm">
            <img src="assets/image/book5.png" class="card-img-top" height= "300px" alt="Book 1">
            <div class="card-body">
              <h5 class="card-title">POWER</h5>
              <p class="card-text">Robert Greene</p>
              <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card book-card shadow-sm">
            <img src="assets/image/book6.jpg" class="card-img-top" height= "300px" alt="Book 1">
            <div class="card-body">
              <h5 class="card-title">ATOMIC HABITS</h5>
              <p class="card-text">James Clear</p>
              <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
          </div>
        </div>

         <div class="col-md-3">
          <div class="card book-card shadow-sm">
            <img src="assets/image/book7.jpg" class="card-img-top" height= "300px" alt="Book 1">
            <div class="card-body">
              <h5 class="card-title">START WITH WHY</h5>
              <p class="card-text">Simon Sinek</p>
              <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
          </div>
        </div>

         <div class="col-md-3">
          <div class="card book-card shadow-sm">
            <img src="assets/image/book8.jpg" class="card-img-top" height= "300px" alt="Book 1">
            <div class="card-body">
              <h5 class="card-title">THINK AND GROW RICH</h5>
              <p class="card-text">Napoleon Hill</p>
              <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
          </div>
        </div>

         <div class="col-md-3">
          <div class="card book-card shadow-sm">
            <img src="assets/image/book9.png" class="card-img-top" height= "300px" alt="Book 1">
            <div class="card-body">
              <h5 class="card-title">THIS IS HOW YOU HEAL</h5>
              <p class="card-text">Briana Wiest</p>
              <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card book-card shadow-sm">
            <img src="assets/image/book10.jpg" class="card-img-top" height= "300px" alt="Book 1">
            <div class="card-body">
              <h5 class="card-title">A GENTLE REMINDER</h5>
              <p class="card-text">Bianca Sparacino</p>
              <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
          </div>
        </div>

         <div class="col-md-3">
          <div class="card book-card shadow-sm">
            <img src="assets/image/book11.jpg" class="card-img-top" height= "300px" alt="Book 1">
            <div class="card-body">
              <h5 class="card-title">THE BOOK THIEF</h5>
              <p class="card-text">Markus Zusak</p>
              <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
          </div>
        </div>

         <div class="col-md-3">
          <div class="card book-card shadow-sm">
            <img src="assets/image/book12.jpg" class="card-img-top" height= "300px" alt="Book 1">
            <div class="card-body">
              <h5 class="card-title">CATCH-22</h5>
              <p class="card-text">Joseph Heller</p>
              <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="py-5 text-center bg-light">
    <div class="container">
      <h2 class="fw-bold mb-3">About Our Library</h2>
      <p class="lead">
        To make knowledge accessible to everyone by providing innovative library services, 
        diverse collections, and a welcoming space for learning, growth, and discovery.
      </p>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-light py-4 text-center">
    <div class="container">
      <p class="mb-1 fw-bold">📖 Bookbyte Library</p>
      <p class="mb-1">Knowledge that Transforms.</p>
      <p class="mb-1">📧 support@Bookbytelibrary.com | 📞 +63 970 7128 431</p>
      <small>© 2025 Bookbyte Library. All rights reserved.</small>
    </div>
  </footer>

<!-- Login Modal -->
<div class="modal fade" id="modallogin" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-sign-in-alt"></i> Sign In</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="frmsignin">
          <div class="mb-3">
            <label for="username" class="form-label">Username / Email</label>
            <input type="text" class="form-control" id="username" name="username" required>
            <div class="form-text">Enter your username or email address</div>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-dark">
              <i class="fas fa-sign-in-alt"></i> Login
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Notification Modal -->
<div class="modal fade" id="modalnotification" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center p-4">
        <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: var(--danger-color); margin-bottom: 20px;"></i>
        <h4>Notification</h4>
        <p id="notificationcontent" class="mt-3"></p>
        <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $("#frmsignin").submit(function(evt) {
    evt.preventDefault();
    var formData = new FormData($(this)[0]);
    $.ajax({
      url: 'processes/login.php',
      type: 'POST',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success: function (result) {
        var res = parseInt(result);
        if (res == 1){
          location.reload();
        } else {
          $("#notificationcontent").html(result);
          $("#modalnotification").modal("show");
          $("#modallogin").modal("hide");
        }
      }
    });
  });

  // Smooth scroll
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
</script>

</body>
</html>