/**
 * order.js - Client-side order creation handler for Jewelry Store
 */
document.addEventListener("DOMContentLoaded", function () {
    const orderForm = document.getElementById("order-form");
    const orderAlert = document.getElementById("order-alert");
    const orderResultBox = document.getElementById("order-result-box");
    const productCheckboxes = document.querySelectorAll(".product-checkbox");

    productCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener("change", function () {
            clearAlert(orderAlert);
            const qtyInput = document.getElementById(this.dataset.target);

            if (qtyInput) {
                qtyInput.disabled = !this.checked;
                if (this.checked && (!qtyInput.value || qtyInput.value < 1)) {
                    qtyInput.value = 1;
                }
            }
        });
    });

    if (orderForm) {
        orderForm.addEventListener("input", () => clearAlert(orderAlert));

        orderForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            clearAlert(orderAlert);
            if (orderResultBox.style.display === "block") {
                orderResultBox.style.display = "none";
            }

            const orderId = document.getElementById("order-id").value.trim();
            const customerName = document.getElementById("customer-name").value.trim();
            const customerAddress = document.getElementById("customer-address").value.trim();
            const customerEmail = document.getElementById("customer-email").value.trim();
            const customerPhone = document.getElementById("customer-phone").value.trim();

            if (!orderId || !customerName || !customerAddress || !customerEmail || !customerPhone) {
                showAlert(orderAlert, "Please fill in all customer details.", "danger");
                return;
            }

            const selectedProducts = document.querySelectorAll(".product-checkbox:checked");
            if (selectedProducts.length === 0) {
                showAlert(orderAlert, "Please select at least one jewelry item to order.", "danger");
                return;
            }

            const formData = new FormData(orderForm);

            try {
                const response = await fetch("../server/create_order.php", {
                    method: "POST",
                    body: formData,
                });

                if (response.status === 404) {
                    const errorHtml = await response.text();
                    document.open();
                    document.write(errorHtml);
                    document.close();
                    return;
                }

                const result = await response.json();

                if (result.success) {
                    const resultBox = document.getElementById("order-result-box");
                    const totalPriceDisplay = document.getElementById("order-total-price");
                    
                    if (totalPriceDisplay) totalPriceDisplay.textContent = result.total_with_vat + ' €';
                    if (resultBox) resultBox.style.display = "block";
                } else {
                    showAlert(orderAlert, result.message || "Could not process order.", "danger");
                }
            } catch (err) {
                console.error("Order error:", err);
                showAlert(orderAlert, "An error occurred while creating the order.", "danger");
            }
        });
    }
});