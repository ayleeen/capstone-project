function selectMenuPath(choice, destination) {
  sessionStorage.setItem('lolaysMenuChoice', choice);
}

document.getElementById('customizeMenuBtn').addEventListener('click', (e) => {
  selectMenuPath('customize-menu.html');
});

document.getElementById('packageMenuBtn').addEventListener('click', (e) => {
  selectMenuPath('package.html');
});

// Cancel with confirmation 

document.getElementById('cancelBtn').addEventListener('click', (e) => {
  e.preventDefault();
  const confirmCancel = confirm('Go back to Step 2: Event Details? Your menu selection progress will be discarded.');
  if (confirmCancel) {
    window.location.href = 'reservation-process-2.html';
  }
});