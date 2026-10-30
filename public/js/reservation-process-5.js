document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('paymentForm');
  const fileInput = document.getElementById('proofFile');
  const errorEl = document.getElementById('uploadError');
  const confirmBtn = document.getElementById('confirmBtn');

  const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'application/pdf'];
  const MAX_FILE_BYTES = 5 * 1024 * 1024;

  function validateFile() {
    errorEl.textContent = '';
    const file = fileInput.files[0];

    if (!file) {
      errorEl.textContent = 'Please upload your proof of payment.';
      return null;
    }
    if (!ALLOWED_TYPES.includes(file.type)) {
      errorEl.textContent = 'Only JPG, JPEG, PNG or PDF files are accepted.';
      return null;
    }
    if (file.size > MAX_FILE_BYTES) {
      errorEl.textContent = 'File is too large. Maximum size is 5MB.';
      return null;
    }
    return file;
  }

  fileInput.addEventListener('change', validateFile);

  form.addEventListener('submit', (e) => {
    if (!validateFile()) {
      e.preventDefault();   // block lang kapag may mali sa file
      return;
    }
    confirmBtn.disabled = true;  // iwas double-click
    // walang preventDefault: isusumite ang form sa Laravel
  });
});