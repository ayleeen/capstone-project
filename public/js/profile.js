// profile.js — Profile Setting

// ---- DOM refs ----
const profileForm    = document.getElementById('profileForm');
const fullName       = document.getElementById('fullName');
const email          = document.getElementById('email');
const phone          = document.getElementById('phone');
const secondaryPhone = document.getElementById('secondaryPhone');
const address1       = document.getElementById('address1');
const address2       = document.getElementById('address2');

const photoPreview   = document.getElementById('photoPreview');
const photoEmpty     = document.getElementById('photoEmpty');
const photoInput     = document.getElementById('photoInput');
const changePhotoBtn = document.getElementById('changePhotoBtn');
const removePhotoBtn = document.getElementById('removePhotoBtn');

const adminAvatar = document.getElementById('adminAvatar');
const adminName   = document.getElementById('adminName');

const alertModalEl       = document.getElementById('alertModal');
const alertModal         = new bootstrap.Modal(alertModalEl);
const removePhotoModalEl = document.getElementById('removePhotoModal');
const removePhotoModal   = new bootstrap.Modal(removePhotoModalEl);

// ---- Settings ----
const STORAGE_KEY    = 'lolays.adminProfile';
const DEFAULT_PHOTO  = 'https://i.pravatar.cc/100?img=12';
const DEFAULT_NAME   = 'Admin User';
const MAX_PHOTO_BYTES = 2 * 1024 * 1024; // 2MB
const ALLOWED_TYPES  = ['image/jpeg', 'image/png'];
const PHONE_PATTERN  = /^[0-9+()\-\s]{7,20}$/;
const EMAIL_PATTERN  = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Grey person icon used in the top bar when there is no photo
const PLACEHOLDER_AVATAR = 'data:image/svg+xml;utf8,' + encodeURIComponent(
  '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="#e9eeea"/>' +
  '<circle cx="50" cy="38" r="18" fill="#b5bcc8"/><path d="M14 92c4-22 20-32 36-32s32 10 36 32z" fill="#b5bcc8"/></svg>'
);

// Starting values. The fields start empty; replace with data from your backend.
const DEFAULT_PROFILE = {
  fullName: '',
  email: '',
  phone: '',
  secondaryPhone: '',
  address1: '',
  address2: '',
  photo: DEFAULT_PHOTO
};

let currentPhoto = '';

// ---- Alert modal (replaces alert()) ----
const ALERT_TYPES = {
  info:    { cls: 'info',    icon: 'fa-circle-info' },
  warning: { cls: 'warning', icon: 'fa-triangle-exclamation' },
  success: { cls: 'success', icon: 'fa-circle-check' },
  danger:  { cls: 'danger',  icon: 'fa-circle-xmark' }
};

function showAlert(message, { title = 'Notice', type = 'info', okText = 'OK' } = {}){
  const cfg = ALERT_TYPES[type] || ALERT_TYPES.info;
  document.getElementById('alertIcon').className = `confirm-icon-circle ${cfg.cls}`;
  document.getElementById('alertIconGlyph').className = `fa-solid ${cfg.icon}`;
  document.getElementById('alertTitle').textContent = title;
  document.getElementById('alertMessage').textContent = message;
  document.getElementById('alertOkBtn').textContent = okText;
  alertModal.show();
}

// ---- Load / save (localStorage so you can test without a backend) ----
function loadProfile(){
  try {
    const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null');
    return saved ? { ...DEFAULT_PROFILE, ...saved } : { ...DEFAULT_PROFILE };
  } catch (err) {
    return { ...DEFAULT_PROFILE };
  }
}

function persistProfile(profile){
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(profile));
    return true;
  } catch (err) {
    return false; // storage full or blocked
  }
}

// ---- Photo ----
function setPhoto(src){
  currentPhoto = src || '';
  if(currentPhoto){
    photoPreview.src = currentPhoto;
    photoPreview.classList.remove('d-none');
    photoEmpty.classList.add('d-none');
  } else {
    photoPreview.removeAttribute('src');
    photoPreview.classList.add('d-none');
    photoEmpty.classList.remove('d-none');
  }
}

// If the default photo can't load (e.g. offline), show the placeholder icon instead
photoPreview.addEventListener('error', () => {
  photoPreview.classList.add('d-none');
  photoEmpty.classList.remove('d-none');
});

changePhotoBtn.addEventListener('click', () => photoInput.click());

photoInput.addEventListener('change', () => {
  const file = photoInput.files[0];
  photoInput.value = ''; // lets the same file be picked again later
  if(!file) return;

  if(!ALLOWED_TYPES.includes(file.type)){
    showAlert('Please choose a JPG or PNG image.', { title: 'Unsupported File', type: 'warning' });
    return;
  }
  if(file.size > MAX_PHOTO_BYTES){
    showAlert('That image is larger than 2 MB. Please choose a smaller one.', { title: 'File Too Large', type: 'warning' });
    return;
  }

  const reader = new FileReader();
  reader.onload = () => setPhoto(reader.result);
  reader.onerror = () => showAlert('We couldn\'t read that file. Please try another image.', { title: 'Upload Failed', type: 'danger' });
  reader.readAsDataURL(file);
});

removePhotoBtn.addEventListener('click', () => {
  if(!currentPhoto){
    showAlert('There is no profile picture to remove.', { title: 'No Photo', type: 'info' });
    return;
  }
  removePhotoModal.show();
});

document.getElementById('confirmRemovePhotoBtn').addEventListener('click', () => {
  setPhoto('');
  removePhotoModal.hide();
});

// ---- Top bar (name + avatar) ----
function applyToTopbar(profile){
  adminName.textContent = profile.fullName || DEFAULT_NAME;
  adminAvatar.src = profile.photo || PLACEHOLDER_AVATAR;
}

// ---- Fill the form ----
function fillForm(p){
  fullName.value = p.fullName;
  email.value = p.email;
  phone.value = p.phone;
  secondaryPhone.value = p.secondaryPhone;
  address1.value = p.address1;
  address2.value = p.address2;
  setPhoto(p.photo);
}

// Clear the red border as soon as the user edits a field
[fullName, email, phone, secondaryPhone].forEach(input =>
  input.addEventListener('input', () => input.classList.remove('is-invalid'))
);

// ---- Save ----
function invalid(input, message, title){
  input.classList.add('is-invalid');
  // focus the field once the alert is closed, so the user can fix it right away
  alertModalEl.addEventListener('hidden.bs.modal', () => input.focus(), { once: true });
  showAlert(message, { title, type: 'warning' });
}

profileForm.addEventListener('submit', (e) => {
  e.preventDefault();

  const profile = {
    fullName: fullName.value.trim(),
    email: email.value.trim(),
    phone: phone.value.trim(),
    secondaryPhone: secondaryPhone.value.trim(),
    address1: address1.value.trim(),
    address2: address2.value.trim(),
    photo: currentPhoto
  };

  if(!profile.fullName){
    return invalid(fullName, 'Please enter your full name.', 'Missing Information');
  }
  if(profile.email && !EMAIL_PATTERN.test(profile.email)){
    return invalid(email, 'Please enter a valid email address, for example name@example.com.', 'Invalid Email Address');
  }
  if(profile.phone && !PHONE_PATTERN.test(profile.phone)){
    return invalid(phone, 'Please enter a valid phone number, for example +63 912 345 6789.', 'Invalid Phone Number');
  }
  if(profile.secondaryPhone && !PHONE_PATTERN.test(profile.secondaryPhone)){
    return invalid(secondaryPhone, 'Please enter a valid secondary phone number, for example +63 912 345 6789.', 'Invalid Phone Number');
  }

  if(persistProfile(profile)){
    applyToTopbar(profile);
    showAlert('Your profile has been updated successfully.', { title: 'Profile Updated', type: 'success' });
  } else {
    showAlert('Your changes couldn\'t be stored in this browser. If you uploaded a photo, try a smaller image.', { title: 'Could Not Save', type: 'danger' });
  }
});

// ---- Init ----
const initialProfile = loadProfile();
fillForm(initialProfile);
applyToTopbar(initialProfile);