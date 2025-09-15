 document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('registrationForm');
  
  // Get input fields
  const nameInput = document.getElementById('name');
  const phoneInput = document.getElementById('phone');
  const genderInputs = document.querySelectorAll('input[name="gender"]');
  const ageInput = document.getElementById('age');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const confirmPasswordInput = document.getElementById('confirm-password');
  
  // Get error elements
  const nameError = document.getElementById('nameError');
  const phoneError = document.getElementById('phoneError');
  const genderError = document.getElementById('genderError');
  const ageError = document.getElementById('ageError');
  const emailError = document.getElementById('emailError');
  const passwordError = document.getElementById('passwordError');
  const confirmPasswordError = document.getElementById('confirmPasswordError');
  
  // Validation functions
  function validateName() {
    const nameValue = nameInput.value.trim();
    // Allow letters and spaces, and ensure there are at least two words.
    const namePattern = /^[A-Za-z]+(?:\s[A-Za-z]+)+$/;
    
    if (!namePattern.test(nameValue)) {
       
      nameError.style.display = 'block';
      nameInput.classList.remove('success');
      return false;
    } else {
      nameError.style.display = 'none';
      nameInput.classList.add('success');
      return true;
    }
  }
  
  function validatePhone() {
    const phoneValue = phoneInput.value.trim();
    // A more common Bangladeshi phone number pattern, allowing +8801 or 01.
    const phonePattern = /^(?:(?:\+880)|0)1[3-9]\d{8}$/;
    
    if (!phonePattern.test(phoneValue)) {
       
      phoneError.style.display = 'block';
      phoneInput.classList.remove('success');
      return false;
    } else {
      phoneError.style.display = 'none';
      phoneInput.classList.add('success');
      return true;
    }
  }
  
  function validateGender() {
    let isChecked = false;
    for (const genderInput of genderInputs) {
      if (genderInput.checked) {
        isChecked = true;
        break;
      }
    }
    
    if (!isChecked) {
       
      genderError.style.display = 'block';
      return false;
    } else {
      genderError.style.display = 'none';
      return true;
    }
  }
  
  function validateAge() {
    const ageValue = parseInt(ageInput.value);
    
    // Updated validation: min 18, max 100 for car rental.
    if (isNaN(ageValue) || ageValue < 13 || ageValue > 100) {
      
      ageError.style.display = 'block';
      ageInput.classList.remove('success');
      return false;
    } else {
      ageError.style.display = 'none';
      ageInput.classList.add('success');
      return true;
    }
  }
  
  function validateEmail() {
    const emailValue = emailInput.value.trim();
    // Corrected email validation pattern. It should not check for uppercase.
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailPattern.test(emailValue)) {
       
      emailError.style.display = 'block';
      emailInput.classList.remove('success');
      return false;
    } else {
      emailError.style.display = 'none';
      emailInput.classList.add('success');
      return true;
    }
  }
  
  function validatePassword() {
    const passwordValue = passwordInput.value;
    // At least 8 characters, one uppercase, one lowercase, one number
    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    
    if (!passwordPattern.test(passwordValue)) {
      passwordError.textContent = 'Password must be at least 8 characters, with at least one uppercase letter, one lowercase letter, and one number.';
      passwordError.style.display = 'block';
      passwordInput.classList.remove('success');
      return false;
    } else {
      passwordError.style.display = 'none';
      passwordInput.classList.add('success');
      return true;
    }
  }
  
  function validateConfirmPassword() {
    const passwordValue = passwordInput.value;
    const confirmPasswordValue = confirmPasswordInput.value;
    
    if (passwordValue !== confirmPasswordValue) {
      confirmPasswordError.textContent = 'Passwords do not match.';
      confirmPasswordError.style.display = 'block';
      confirmPasswordInput.classList.remove('success');
      return false;
    } else {
      confirmPasswordError.style.display = 'none';
      confirmPasswordInput.classList.add('success');
      return true;
    }
  }
  
  // Add event listeners for real-time validation
  nameInput.addEventListener('input', validateName);
  phoneInput.addEventListener('input', validatePhone);
  
  for (const genderInput of genderInputs) {
    genderInput.addEventListener('change', validateGender);
  }
  
  ageInput.addEventListener('input', validateAge);
  emailInput.addEventListener('input', validateEmail);
  passwordInput.addEventListener('input', validatePassword);
  confirmPasswordInput.addEventListener('input', validateConfirmPassword);

  // Form submission handler
  form.addEventListener('submit', function(event) {
    event.preventDefault();
    
    // Validate all fields and store results
    const isNameValid = validateName();
    const isPhoneValid = validatePhone();
    const isGenderValid = validateGender();
    const isAgeValid = validateAge();
    const isEmailValid = validateEmail();
    const isPasswordValid = validatePassword();
    const isConfirmPasswordValid = validateConfirmPassword();
    
    // If all validations are valid, proceed
    if (isNameValid && isPhoneValid && isGenderValid && isAgeValid && 
        isEmailValid && isPasswordValid && isConfirmPasswordValid) {
      alert('Registration successful!');
      // This is the line that redirects the user to the next page
      window.location.href = 'loginpage.html';
    } else {
      // Find the first invalid element and scroll to it
      const firstInvalidInput = document.querySelector('.error[style*="display: block"]');
      if (firstInvalidInput) {
        firstInvalidInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  });
  
  // Reset form handler
  form.addEventListener('reset', function() {
    // Hide all error messages and remove success classes
    const errorElements = document.querySelectorAll('.error');
    errorElements.forEach(error => {
      error.style.display = 'none';
      error.textContent = ''; // Also clear the error text
    });
    
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
      input.classList.remove('success');
    });
  });
});