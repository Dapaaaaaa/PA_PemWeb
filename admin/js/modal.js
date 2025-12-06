/**
 * Custom Delete Modal for Admin Panel
 * Reusable confirmation modal
 */

let deleteTargetId = null;
let deleteRedirectUrl = null;

/**
 * Show delete confirmation modal
 * @param {number} id - ID of item to delete
 * @param {string} name - Name of item to delete
 * @param {string} redirectUrl - URL to redirect after confirmation
 */
function confirmDelete(id, name, redirectUrl) {
  deleteTargetId = id;
  deleteRedirectUrl = redirectUrl;

  document.getElementById("deleteMessage").innerHTML =
    'Yakin ingin menghapus <strong>"' +
    name +
    '"</strong>?<br><br>' +
    '<span style="color: #dc3545;">Data akan dihapus permanen!</span>';

  document.getElementById("deleteModal").classList.add("show");
}

/**
 * Close delete modal
 */
function closeDeleteModal() {
  document.getElementById("deleteModal").classList.remove("show");
  deleteTargetId = null;
  deleteRedirectUrl = null;
}

// Confirm delete button event
document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("confirmDeleteBtn")
    .addEventListener("click", function () {
      if (deleteTargetId && deleteRedirectUrl) {
        window.location.href = deleteRedirectUrl.replace(
          "{id}",
          deleteTargetId
        );
      }
    });

  // Close modal on background click
  document
    .getElementById("deleteModal")
    .addEventListener("click", function (e) {
      if (e.target === this) {
        closeDeleteModal();
      }
    });
});
