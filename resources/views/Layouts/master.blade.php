<!DOCTYPE html>
<html lang="en">
  @livewireStyles
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <title>@yield('title')</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Plyr CSS -->
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body> 
    <nav id="mainHeader" class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
          <a class="navbar-brand" href={{ url('/welcome') }}>
            <img src="{{ asset('images/logo.png') }}"  alt="Logo" width="70" height="70">
          </a>
      
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          
          <div class="collapse navbar-collapse" id="navbarSupportedContent">          
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href={{ url('/welcome') }} style="color: white !important; " >Start</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href={{ url('/shows') }} style="color: white !important; " >Shows</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href={{url('/movies')}} style="color: white !important; "  >Movies</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href={{url('/documentaries')}} style="color: white !important; "  >Documentaries</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href={{url('/webseries')}} style="color: white !important; " >WebSeries</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href={{url('/new')}} style="color: white !important; ">New</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href={{url('/mylist')}} style="color: white !important; " >My List</a>
              </li>
              
              
            </ul>
      
            <!-- Search Icon + Bell + User -->
            <div class="d-flex align-items-center gap-3">

  
<!-- Right-side icons: Search, Bell, Logout -->
<div class="d-flex align-items-center gap-3">

  <!-- Search Bar -->
  <form method="GET" action="{{ route('search') }}" class="d-flex align-items-center bg-white px-2 rounded" style="height: 38px;">
      <button type="submit" class="border-0 bg-transparent p-0">
          <i class="fas fa-search text-dark"></i>
      </button>
      <input 
          type="text" 
          name="query" 
          class="form-control border-0 ms-2 shadow-none" 
          placeholder="Search" 
          style="width: 180px;" 
          required
      >
  </form>

  <!-- Notification Icon -->
  <a href="#" class="text-white fs-5 d-flex align-items-center">
    <i class="fas fa-bell"></i>
  </a>

  <!-- Logout / Profile -->
  @if(session()->has('user_id'))
    <a href="{{ url('/logout') }}" class="btn btn-danger btn-sm d-flex align-items-center">Logout</a>
  @else
    <a href="{{ url('/') }}" class="text-white">
      <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width:35px; height:35px;">
        <i class="bi bi-person-circle"></i>
      </div>
    </a>
  @endif

</div>

  
      
          </div>
        </div>
      </nav>
      
    
      
      
    
   
      <div class="containeryy mt-4y">
        @yield('content') 
    </div>
        
        
    
      <footer>
        <div class="container-fluid">
        <div class="footer-content">
          <!-- Logo and contact section -->
          <div class="footer-logo-section">
            <div class="logo footer-logo">
              <a href="#">
                <img src="{{ asset('images/logo.png') }}" alt="SEEPRIME Logo" width="100" height="70" >
              </a>
            </div>
            
            <div class="footer-contact">
              <p class="text-white"><a href="mailto:uscustomer@streamit.com" class="text-white">Email: uscustomer@streamit.com</a></p>
              <p class="contact-title">CUSTOMER SERVICES</p>
              <p class="phone"><a href="tel:(480) 555-0103" class="text-white">+ (480) 555-0103</a></p>
            </div>
          </div>
          
          <!-- Quick Links section -->
          <div class="footer-column">
            <h3>Quick Links</h3>
            <ul>
              <li><a href="#">About Us</a></li>
              <li><a href="#">Blog</a></li>
              <li><a href="#">Pricing Plan</a></li>
              <li><a href="#">FAQ</a></li>
            </ul>
          </div>
          
          <!-- Movies To Watch section -->
          <div class="footer-column">
            <h3>Movies To Watch</h3>
            <ul>
              <li><a href="#">Top Trending</a></li>
              <li><a href="#">Recommended</a></li>
              <li><a href="#">Popular</a></li>
            </ul>
          </div>
          
          <!-- About Company section -->
          <div class="footer-column">
            <h3>About Company</h3>
            <ul>
              <li><a href="#">Contact Us</a></li>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Terms Of Use</a></li>
            </ul>
          </div>

        
          
          <!-- Newsletter section -->
          <div class="footer-column">
            <h3>Subscribe Newsletter</h3>
            <div class="newsletter-form">
              <input type="email" placeholder="Enter your email address">
              <button type="submit">SUBSCRIBE</button>
            </div>
            
            <div class="social-links">
              <span>Follow Us:</span>
              <div class="social-icons">
                <a href="https://www.facebook.com/seeprimetv/" class="social-icon" target="_blank">
                  <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.instagram.com/see.prime/?hl=en" class="social-icon" target="_blank">
                  <i class="fab fa-instagram"></i>
                </a>
                <a href="https://twitter.com/seeprimeTV/" class="social-icon" target="_blank">
                  <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.youtube.com/seeprime" class="social-icon" target="_blank">
                  <i class="fab fa-youtube"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer bottom section -->
        <div class="footer-bottom">
          <div class="footer-nav">
            <a href="#">Terms Of Use</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Blog</a>
            <a href="#">FAQ</a>
            <a href="#">Watch List</a>
          </div>
          
          <div class="copyright">
            &copy; 2025 <span class="brand">SeePrime</span>. All Rights Reserved.
          </div>
        </div>
    </div>
    </footer>

    @if (session('toast'))
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
  <div id="liveToast" class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        {{ session('toast') }}
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
@endif

@livewireScripts 
@stack('scripts')

<!-- Plyr JS -->
<script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
<script>
  window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });

  const toastEl = document.getElementById('liveToast');
  if (toastEl) {
      const toast = new bootstrap.Toast(toastEl);
      toast.show();
  }
</script>

</body>
</html>



