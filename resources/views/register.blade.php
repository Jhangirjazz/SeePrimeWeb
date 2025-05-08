<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - SeePrime</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Stripe.js for payment fields -->
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>

<div class="register-wrapper">
  <div class="register-overlay">
    <div class="register-form">
      <!-- SEEPRIME logo -->
      <img src="{{ asset('images/logo.png') }}" alt="SeePrime Logo">

      <!-- Form -->
      <form id="registration-form" action="/process_payment" method="POST">
        <!-- CSRF Token (for Laravel) -->
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <!-- Step 1: User Details -->
        <div class="form-step active" id="step-1">
          <h3 class="mb-3">User Details</h3>
          <div id="user-details-errors" class="error-message" role="alert"></div>
          <div class="mb-3">
            <input type="email" name="email" id="email" placeholder="Enter email" required>
          </div>
          <div class="mb-3">
            <input type="text" name="username" id="username" placeholder="Enter your user name" required>
          </div>
          <div class="mb-3">
            <input type="password" name="password" id="password" placeholder="Enter password" required>
          </div>
          <div class="mb-3">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
          </div>
          <button type="button" onclick="validateUserDetails()">Next</button>
        </div>

        <!-- Step 2: Payment Details -->
        <div class="form-step" id="step-2">
          <h3 class="mb-3">Add card details</h3>
          <div class="mb-3">
            <select name="card_type" required>
              <option value="" disabled selected>Choose card type</option>
              <option value="visa">Visa</option>
              <option value="mastercard">MasterCard</option>
              <option value="amex">American Express</option>
            </select>
          </div>
          <div class="mb-3">
            <div id="card-element"></div>
            <div id="card-errors" role="alert"></div>
          </div>
          <div class="mb-3 expiry-cvv-container">
            <div class="expiry-field">
              <input type="text" name="expiry_date" placeholder="Expiry date (MM/YY)" required>
            </div>
            <div class="cvv-field">
              <input type="text" name="cvv" placeholder="CVV" maxlength="4" required>
            </div>
          </div>
          <div class="button-container">
            <button type="button" class="back-btn" onclick="prevStep()">Back</button>
            <button type="submit" class="continue-btn">Continue</button>
          </div>
        </div>
      </form>

      <!-- Already have account -->
      <div class="already-account mt-3">
        Already have an account? <a href="{{ url('/') }}">Sign In</a>
      </div>
    </div>
  </div>
</div>

<script>
  // Step navigation
  function nextStep() {
    document.getElementById('step-1').classList.remove('active');
    document.getElementById('step-2').classList.add('active');
  }

  function prevStep() {
    document.getElementById('step-2').classList.remove('active');
    document.getElementById('step-1').classList.add('active');
  }

  // Validate User Details
  function validateUserDetails() {
    const email = document.getElementById('email').value.trim();
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const errorDisplay = document.getElementById('user-details-errors');

    errorDisplay.textContent = ''; // Clear previous errors

    // Basic validation checks
    if (!email || !username || !password || !confirmPassword) {
      errorDisplay.textContent = 'Please fill in all fields.';
      return;
    }

    // Simple email format check
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
      errorDisplay.textContent = 'Please enter a valid email address.';
      return;
    }

    // Password match check
    if (password !== confirmPassword) {
      errorDisplay.textContent = 'Passwords do not match.';
      return;
    }

    // If all checks pass, proceed to the next step
    nextStep();
  }

  // Stripe initialization
  const stripe = Stripe(''); // Replace with your Stripe publishable key
  const elements = stripe.elements();
  const cardElement = elements.create('card', {
    style: {
      base: {
        color: '#333',
        fontSize: '14px',
        '::placeholder': { color: '#aaa' },
      },
    },
  });
  cardElement.mount('#card-element');

  // Handle card input errors
  cardElement.on('change', function(event) {
    const displayError = document.getElementById('card-errors');
    if (event.error) {
      displayError.textContent = event.error.message;
    } else {
      displayError.textContent = '';
    }
  });

  // Form submission (client-side validation and token creation)
  const form = document.getElementById('registration-form');
  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const { token, error } = await stripe.createToken(cardElement);
    if (error) {
      document.getElementById('card-errors').textContent = error.message;
    } else {
      // Append token to form and submit to server
      const hiddenInput = document.createElement('input');
      hiddenInput.setAttribute('type', 'hidden');
      hiddenInput.setAttribute('name', 'stripeToken');
      hiddenInput.setAttribute('value', token.id);
      form.appendChild(hiddenInput);
      form.submit();
    }
  });
</script>

</body>
</html>