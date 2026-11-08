/**
 * auth.js - Client-side authentication handler
 */
document.addEventListener("DOMContentLoaded", function () {
    const authForm = document.getElementById("login-form");
    const errorDisplay = document.getElementById("auth-error");

    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");

    if (usernameInput) usernameInput.addEventListener("input", () => clearAlert(errorDisplay));
    if (passwordInput) passwordInput.addEventListener("input", () => clearAlert(errorDisplay));

    if (authForm) {
        authForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const username = usernameInput ? usernameInput.value.trim() : "";
            const password = passwordInput ? passwordInput.value.trim() : "";

            if (username === "" || password === "") {
                showAlert(errorDisplay, "Please fill in both username and password.", "danger");
                return;
            }

            const formData = new FormData();
            formData.append("username", username);
            formData.append("password", password);

            clearAlert(errorDisplay);

            try {
                // Asegúrate de que el archivo del servidor se llama auth.php
                const response = await fetch("../server/auth.php", {
                    method: "POST",
                    body: formData,
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(errorDisplay, result.message || "Authentication successful.", "success");

                    setTimeout(function () {
                        window.location.href = "operations.html";
                    }, 1200);
                } else {
                    showAlert(errorDisplay, result.message || "Authentication failed. Redirecting to home...", "danger");

                    setTimeout(function () {
                        window.location.href = "index.html";
                    }, 2000);
                }
            } catch (err) {
                console.error("Authentication error:", err);
                showAlert(errorDisplay, "An error occurred during authentication. Redirecting to home...", "danger");
                setTimeout(function () {
                    window.location.href = "index.html";
                }, 2000);
            }
        });
    }
});