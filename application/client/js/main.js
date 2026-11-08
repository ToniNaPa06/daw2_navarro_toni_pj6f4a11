/**
 * main.js - Shared UI utilities across the application
 */
function showAlert(element, message, type = "danger") {
    if (!element) return;
    element.className = `alert alert-${type}`;
    element.textContent = message;
    element.style.display = "block";
}

function clearAlert(element) {
    if (!element) return;
    element.textContent = "";
    element.style.display = "none";
}