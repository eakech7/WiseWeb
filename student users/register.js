// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function() {
    
    // DOM Elements
    const fullname = document.getElementById("fullname");
    const email = document.getElementById("email");
    const course = document.getElementById("course");
    const dob = document.getElementById("dob");
    const genderRadios = document.querySelectorAll("input[name='gender']");
    const username = document.getElementById("username");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirmPassword");
    const form = document.getElementById("registrationForm");

    // Error Elements
    const usererror = document.getElementById("usererror");
    const emailerror = document.getElementById("emailerror");
    const courseerror = document.getElementById("courseerror");
    const doberror = document.getElementById("doberror");
    const gendererror = document.getElementById("gendererror");
    const usernameerror = document.getElementById("usernameerror");
    const passerror = document.getElementById("passerror");
    const confirmerror = document.getElementById("confirmerror");

    // VALIDATION FUNCTIONS
    function validateFullName() {
        const val = fullname.value.trim();
        if (!val) {
            usererror.textContent = "Full Name is required.";
            fullname.className = "invalid";
            return false;
        } else if (/^[A-Za-z\s]+$/.test(val)) {
            usererror.textContent = "";
            fullname.className = "valid";
            return true;
        } else {
            usererror.textContent = "Full Name must contain only letters and spaces.";
            fullname.className = "invalid";
            return false;
        }
    }

    function validateEmail() {
        const val = email.value.trim();
        if (!val) {
            emailerror.textContent = "Email is required.";
            email.className = "invalid";
            return false;
        } else if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
            emailerror.textContent = "";
            email.className = "valid";
            return true;
        } else {
            emailerror.textContent = "Invalid email format.";
            email.className = "invalid";
            return false;
        }
    }

    function validateCourse() {
        if (course.value === "") {
            courseerror.textContent = "Please select your course.";
            course.className = "invalid";
            return false;
        } else {
            courseerror.textContent = "";
            course.className = "valid";
            return true;
        }
    }

    function validateDOB() {
        const val = dob.value;
        if (!val) {
            doberror.textContent = "Date of Birth is required.";
            dob.className = "invalid";
            return false;
        }
        
        const birthDate = new Date(val);
        const today = new Date();
        
        if (birthDate > today) {
            doberror.textContent = "Date cannot be in the future.";
            dob.className = "invalid";
            return false;
        }

        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        if (age < 18) {
            doberror.textContent = "You must be at least 18 years old.";
            dob.className = "invalid";
            return false;
        }

        doberror.textContent = "";
        dob.className = "valid";
        return true;
    }

    function validateGender() {
        const selected = document.querySelector('input[name="gender"]:checked');
        if (!selected) {
            gendererror.textContent = "Please select your gender.";
            return false;
        } else {
            gendererror.textContent = "";
            return true;
        }
    }

    function validateUsername() {
        const val = username.value.trim();
        if (!val) {
            usernameerror.textContent = "Username is required.";
            username.className = "invalid";
            return false;
        } else if (val.length < 4) {
            usernameerror.textContent = "Username must be at least 4 characters.";
            username.className = "invalid";
            return false;
        } else if (!/^[A-Za-z0-9_]+$/.test(val)) {
            usernameerror.textContent = "Username can only contain letters, numbers, and underscores.";
            username.className = "invalid";
            return false;
        } else {
            usernameerror.textContent = "";
            username.className = "valid";
            return true;
        }
    }

    function validatePassword() {
        const val = password.value;
        if (!val) {
            passerror.textContent = "Password is required.";
            password.className = "invalid";
            return false;
        }
        const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        if (!strongRegex.test(val)) {
            passerror.textContent = "Password must contain uppercase, lowercase, number, special character, and be at least 8 characters.";
            password.className = "invalid";
            return false;
        } else {
            passerror.textContent = "";
            password.className = "valid";
            return true;
        }
    }

    function validateConfirmPassword() {
        const val = confirmPassword.value;
        if (!val) {
            confirmerror.textContent = "Please confirm your password.";
            confirmPassword.className = "invalid";
            return false;
        } else if (password.value !== val) {
            confirmerror.textContent = "Passwords do not match.";
            confirmPassword.className = "invalid";
            return false;
        } else {
            confirmerror.textContent = "";
            confirmPassword.className = "valid";
            return true;
        }
    }

    // ADD EVENT LISTENERS FOR REAL-TIME VALIDATION
    fullname.addEventListener("input", validateFullName);
    email.addEventListener("input", validateEmail);
    course.addEventListener("change", validateCourse);
    dob.addEventListener("change", validateDOB);
    username.addEventListener("input", validateUsername);
    password.addEventListener("input", validatePassword);
    confirmPassword.addEventListener("input", validateConfirmPassword);
    
    genderRadios.forEach(function(radio) {
        radio.addEventListener("change", validateGender);
    });

    // FORM SUBMISSION
    form.addEventListener("submit", function(e) {
        console.log("Form submitted!"); // Debug log
        e.preventDefault();
        
        const isFormValid = 
            validateFullName() &&
            validateEmail() &&
            validateCourse() &&
            validateDOB() &&
            validateGender() &&
            validateUsername() &&
            validatePassword() &&
            validateConfirmPassword();

        console.log("Form validation result:", isFormValid); // Debug log

        if (isFormValid) {
            console.log("Form is valid, showing success message..."); // Debug log
            
            // Show success message
            const successMessage = document.createElement('div');
            successMessage.innerHTML = `
                <div style="
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: #28a745;
                    color: white;
                    padding: 20px 40px;
                    border-radius: 10px;
                    text-align: center;
                    font-size: 18px;
                    z-index: 1000;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                ">
                    ✅ Registration Successful!<br>
                    <small>Redirecting to login page...</small>
                </div>
            `;
            document.body.appendChild(successMessage);

            // Redirect after 5 seconds
            setTimeout(() => {
                console.log("Redirecting to login page..."); // Debug log
                try {
                    window.location.href = "login.html";
                    console.log("Redirect attempted with window.location.href");
                } catch (error) {
                    console.error("Redirect failed:", error);
                    // Fallback redirect method
                    window.location.replace("login.html");
                }
            }, 5000);
        } else {
            console.log("Form validation failed"); // Debug log
            alert("Please fix all errors before submitting.");
        }
    });
});