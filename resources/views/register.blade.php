<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - SeePrime</title>
  <link rel="stylesheet" href="{{ asset('css/register.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  {{-- <script src="https://js.stripe.com/v3/"></script> --}}
</head>
@if ($errors->any())
  <div class="alert alert-danger">
    @foreach ($errors->all() as $error)
      <div>{{ $error }}</div>
    @endforeach
  </div>
@endif

@if (session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
@endif

{{-- <style>
  .custom-checkbox {
    position: relative;
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid white;
    border-radius: 4px;
    background-color: transparent;
    cursor: pointer;
  }

  .custom-checkbox input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
  }

  .custom-checkbox svg {
    position: absolute;
    top: 1px;
    left: 1px;
    width: 16px;
    height: 16px;
    fill: none;
    stroke: white;
    stroke-width: 3;
    visibility: hidden;
  }

  .custom-checkbox input:checked + svg {
    visibility: visible;
  }
</style> --}}
<body>

<div class="register-wrapper">
  <div class="register-overlay">
    <div class="register-form">
      <img src="{{ asset('images/logo.png') }}" alt="SeePrime Logo">

      <form id="registration-form"  action="/register" method="POST">
        @csrf

        <!-- Step 1 -->
        <div class="form-step active" id="step-1">
          <h3 class="mb-3">User Details</h3>
          <div id="user-details-errors" class="text-danger mb-2"></div>

          <div class="mb-3">
            <input type="email" name="email" id="email" placeholder="Enter email" required>
          </div>
          <div class="mb-3">
            <input type="text" name="username" id="username" placeholder="Choose a username" required>
             </div>   
          <div class="mb-3">
            <input type="password" name="password" id="password" placeholder="Enter password" required>
          </div>
          <div class="mb-3">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
          </div>
          <div class="mb-3">
            <input type="text" name="cnic" id="cnic" maxlength="15" placeholder="Enter CNIC (e.g., 12345-1234567-1)" inputmode="numeric" required>
          </div>
            

          <div class="mb-3">
            <label class="text-white">Account Type:</label>
            <select name="account_type" id="account_type" class="form-select" required>
              <option value="" disabled selected>Loading account types</option>
            </select>
          </div>
          {{-- <div class="mb-6 d-flex align-items-center">
  <label class="custom-checkbox me-2">
    <input type="checkbox" id="is_active" name="is_active" value="1">
    <svg viewBox="0 0 24 24">
      <polyline points="20 6 9 17 4 12"></polyline>
    </svg>
  </label>
  <label for="is_active" class="form-check-label text-white m-0">Activate</label>
</div> --}}
          {{-- <div class="mb-6 d-flex align-items-center">
  <input type="checkbox" id="is_active" name="is_active" value="1"
         class="form-check-input me-2"
         style="width: 18px; height: 18px; accent-color: #ffffff;">
  <label for="is_active" class="form-check-label text-white m-0">Activate</label>
</div> --}}

          {{-- <div class="mb-6 d-flex align-items-center">
          <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input me-2" style="width: 18px; height: 18px;">
          <label for="is_active" class="form-check-label text-white m-0">Activate</label>
          </div> --}}
            
          <button type="button" onclick="validateUserDetails()">Next</button>
        </div>

        <!-- Step 2 -->
        {{-- <div class="form-step" id="step-2">
          <h3 class="mb-3">Payment Details</h3>

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
            <div id="card-errors" class="text-danger" role="alert"></div>
          </div>

          <div class="mb-3 expiry-cvv-container d-flex gap-2">
            <input type="text" name="expiry_date" placeholder="Expiry (MM/YY)" required>
            <input type="text" name="cvv" placeholder="CVV" maxlength="4" required>
          </div>

          <div class="button-container">
            <button type="button" class="back-btn" onclick="prevStep()">Back</button>
            <button type="submit" class="continue-btn btn btn-danger">Pay & Register</button>
          </div>
        </div> --}}
      </form>
      @if ($errors->has('register_error'))
  <div class="alert alert-danger">{{ $errors->first('register_error') }}</div>
@endif

      <div class="already-account mt-3">
        Already have an account? <a href="{{ url('/') }}">Sign In</a>
      </div>
    </div>
  </div>
</div>

<script>
fetch("/api/account-types")
.then(res => res.json())
.then(data => {
  const select = document.getElementById('account_type');
  select.innerHTML = '<option value="" disabled selected>Select account type</option>';
  data.forEach(type=> {
    const label = type.DESCRIPTION;
    const value = label.includes("PRIME") ? "prime"
                : label.includes("TRIAL") ? "trial"
                : "free";
                
    const option = document.createElement('option');
    option.value = value;
    option.textContent = label;
    select.appendChild(option);    
  });
}) 
.catch(err => {
  console.error("Failed to load account types", err);
  alert("could not load account type please refresh.");
});

document.getElementById('cnic').addEventListener('input', function () {
  let value = this.value.replace(/\D/g, ''); // Remove non-digits

  if (value.length > 13) value = value.slice(0, 13); // Limit to 13 digits

  // Apply formatting: 5 digits - 7 digits - 1 digit
  let formatted = value;
  if (value.length > 5 && value.length <= 12) {
    formatted = value.slice(0, 5) + '-' + value.slice(5);
  } else if (value.length > 12) {
    formatted = value.slice(0, 5) + '-' + value.slice(5, 12) + '-' + value.slice(12);
  }
 this.value = formatted;
});
// const stripe = Stripe('pk_test_51Nxxx...'); // Replace with real publishable key
// const elements = stripe.elements();
// const cardElement = elements.create('card', {
//   style: {
//     base: {
//       color: '#333',
//       fontSize: '14px',
//       '::placeholder': { color: '#aaa' },
//     },
//   },
// });
// cardElement.mount('#card-element');

// cardElement.on('change', function (event) {
//   document.getElementById('card-errors').textContent = event.error ? event.error.message : '';
// });

const form = document.getElementById('registration-form');
// Step logic
function nextStep() {
  document.getElementById('step-1').classList.remove('active');
  document.getElementById('step-2').classList.add('active');
}
function prevStep() {
  document.getElementById('step-2').classList.remove('active');
  document.getElementById('step-1').classList.add('active');
}

// Validate
function validateUserDetails() {
  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;
  const confirmPassword = document.getElementById('confirm_password').value;
  const accountType = document.getElementById('account_type').value;
  const errorDisplay = document.getElementById('user-details-errors');

  errorDisplay.textContent = '';

  if (!email || !password || !confirmPassword || !accountType) {
    errorDisplay.textContent = 'Please fill all fields.';
    return;
  }

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    errorDisplay.textContent = 'Invalid email format.';
    return;
  }

  if (password !== confirmPassword) {
    errorDisplay.textContent = 'Passwords do not match.';
    return;
  }

  form.submit();

  // if (accountType === 'free') {
  //   form.submit(); // Directly register free user
  // } else {
  //   nextStep(); // Show payment step for prime user
  // }
}


// form.addEventListener('submit', async (event) => {
//   const accountType = document.getElementById('account_type').value; 

//   if (accountType === 'prime') {
//     event.preventDefault();
//     const { token, error } = await stripe.createToken(cardElement);

//     if (error) {
//       document.getElementById('card-errors').textContent = error.message;
//     } else {
//       // Append Stripe token to form
//       const tokenInput = document.createElement('input');
//       tokenInput.type = 'hidden';
//       tokenInput.name = 'stripeToken';
//       tokenInput.value = token.id;
//       form.appendChild(tokenInput);

//       // Append account type as hidden input (in case it's not sent)
//       const accTypeInput = document.createElement('input');
//       accTypeInput.type = 'hidden';
//       accTypeInput.name = 'account_type';
//       accTypeInput.value = 'prime';
//       form.appendChild(accTypeInput);

//       // Submit the form
//       form.submit();
//     }
//   }
// });



// function extractFormInputsAsJson() {
//   const form = document.getElementById('registration-form');
//   const inputs = form.querySelectorAll('input, select');
//   const inputData = {};

//   inputs.forEach(input => {
//     if (input.name && input.type !== 'submit' && !input.disabled) {
//       inputData[input.name] = input.value || '';
//     }
//   });

//   console.log("Form Input JSON:");
//   console.log(JSON.stringify(inputData, null, 2));
//   alert("check console for form data");
// }


</script>

</body>
</html>


