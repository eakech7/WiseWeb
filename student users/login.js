// Detect caps lock
const passwordInput = document.getElementById("password");
const capsWarning = document.getElementById("caps-lock-warning");

passwordInput.addEventListener("keyup", (e) => {
  if (e.getModifierState("CapsLock")) {
    capsWarning.style.display = "block";
  } else {
    capsWarning.style.display = "none";
  }
});

// Optional basic error simulation
document.getElementById("loginForm").addEventListener("submit", function(e) {
  const email = document.getElementById("email").value;
  const password = passwordInput.value;

  if (!email || !password) {
    e.preventDefault();
    document.getElementById("error-message").textContent = "All fields are required.";
    document.getElementById("error-message").style.display = "block";
  }
});
