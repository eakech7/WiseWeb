// Get elements
const recoveryForm = document.getElementById("recoveryForm");
const emailInput = document.getElementById("email");
const emailError = document.getElementById("emailError");

// Validate email format
function validateEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

// Show error if invalid
emailInput.addEventListener("input", () => {
  const email = emailInput.value.trim();
  if (!validateEmail(email)) {
    emailError.textContent = "Please enter a valid email address.";
  } else {
    emailError.textContent = "";
  }
});

// Handle form submission
recoveryForm.addEventListener("submit", (e) => {
  e.preventDefault();
  const email = emailInput.value.trim();

  if (!validateEmail(email)) {
    emailError.textContent = "Invalid email format.";
    return;
  }

  alert(`Recovery link will be sent to: ${email}`);
  emailInput.value = "";
});
