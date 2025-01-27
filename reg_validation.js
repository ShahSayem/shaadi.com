// Live password match validation
function validatePassword() {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const message = document.getElementById("password-message");
    if (password !== confirmPassword) {
        message.textContent = "Passwords do not match!";
        message.style.color = "red";
    } else {
        message.textContent = "";
    }
}

// Form validation
document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById("registerForm");

    if (registerForm) {
        registerForm.addEventListener("submit", (e) => {
            e.preventDefault(); // Prevent form submission

            const email = document.getElementById("email");
            const password = document.getElementById("password");
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Validate email format
            const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/; // Password rules
            let isValid = true; // Track form validity

            // Email validation
            if (!emailPattern.test(email.value.trim())) {
                email.classList.add("is-invalid");
                isValid = false;
            } else {
                email.classList.remove("is-invalid");
            }

            // Password validation
            if (!passwordPattern.test(password.value)) {
                const passwordFeedback = document.getElementById("password-message");
                passwordFeedback.textContent =
                    "Password must be at least 6 characters long, contain at least one letter, and one number.";
                passwordFeedback.style.color = "red";
                isValid = false;
            }

            // Submit the form if valid
            if (isValid) {
                registerForm.submit();
            }
        });
    }
});
