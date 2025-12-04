/**
 * Toast Notification System
 * Automatically converts old alert-message elements to toast notifications
 */

// Create toast container if it doesn't exist
function createToastContainer() {
  let container = document.querySelector(".toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container";
    document.body.appendChild(container);
  }
  return container;
}

// Show toast notification
function showToast(message, type = "info", duration = 2000) {
  const container = createToastContainer();

  // Icon mapping
  const icons = {
    success: "✓",
    error: "✕",
    warning: "⚠",
    info: "ℹ",
  };

  // Create toast element
  const toast = document.createElement("div");
  toast.className = `toast-notification toast-${type}`;
  toast.innerHTML = `
    <span class="toast-icon">${icons[type] || icons.info}</span>
    <span class="toast-content">${message}</span>
    <button class="toast-close" aria-label="Close">×</button>
  `;

  // Add to container
  container.appendChild(toast);

  // Close button handler
  const closeBtn = toast.querySelector(".toast-close");
  closeBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    removeToast(toast);
  });

  // Click anywhere on toast to close
  toast.addEventListener("click", () => {
    removeToast(toast);
  });

  // Auto remove after duration
  if (duration > 0) {
    setTimeout(() => {
      removeToast(toast);
    }, duration);
  }

  return toast;
}

// Remove toast with animation
function removeToast(toast) {
  if (!toast || toast.classList.contains("hiding")) return;

  toast.classList.add("hiding");
  setTimeout(() => {
    if (toast.parentElement) {
      toast.parentElement.removeChild(toast);
    }
  }, 300); // Match animation duration
}

// Convert old alert-message elements to toasts
function convertAlertsToToasts() {
  const alerts = document.querySelectorAll(".alert-message");

  alerts.forEach((alert) => {
    let type = "info";
    if (alert.classList.contains("alert-success")) {
      type = "success";
    } else if (alert.classList.contains("alert-error")) {
      type = "error";
    } else if (alert.classList.contains("alert-warning")) {
      type = "warning";
    }

    const message = alert.textContent.trim();
    if (message) {
      showToast(message, type, 2000);
    }

    // Hide the old alert
    alert.style.display = "none";
  });
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", function () {
  convertAlertsToToasts();
});

// Make showToast globally available
window.showToast = showToast;
