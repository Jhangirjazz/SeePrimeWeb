<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - SeePrime</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="register-wrapper">
  <div class="register-overlay">
    <div class="register-form text-white">

      <!-- SEEPRIME logo -->
      <img src="{{ asset('images/logo.png') }}" alt="SeePrime Logo">

      <!-- Form -->
      <form>
        <input type="email" placeholder="Enter email" required>
        <input type="text" placeholder="Enter your user name" required>
        <input type="password" placeholder="Enter password" required>
        <input type="password" placeholder="Confirm password" required>
        <button type="submit">Create Account</button>
      </form>

      <!-- Already have account -->
      <div class="already-account mt-3">
        Already have an account? <a href="{{ url('/') }}">Sign In</a>
      </div>

    </div>
  </div>
</div>

</body>
</html>
