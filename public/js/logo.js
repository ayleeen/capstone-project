// // settings.js — Business Profile

// // ---- DOM refs ----
// const businessForm   = document.getElementById('businessForm');
// const bizName        = document.getElementById('bizName');
// const bizDescription = document.getElementById('bizDescription');
// const bizEmail       = document.getElementById('bizEmail');
// const bizPhone       = document.getElementById('bizPhone');
// const bizAddress     = document.getElementById('bizAddress');
// const bizWebsite     = document.getElementById('bizWebsite');
// const bizFacebook    = document.getElementById('bizFacebook');
// const descCount      = document.getElementById('descCount');

// const logoPreview    = document.getElementById('logoPreview');
// const logoEmpty      = document.getElementById('logoEmpty');
// const logoInput      = document.getElementById('logoInput');
// const changeLogoBtn  = document.getElementById('changeLogoBtn');
// const removeLogoBtn  = document.getElementById('removeLogoBtn');

// const alertModalEl      = document.getElementById('alertModal');
// const alertModal        = new bootstrap.Modal(alertModalEl);
// const removeLogoModalEl = document.getElementById('removeLogoModal');
// const removeLogoModal   = new bootstrap.Modal(removeLogoModalEl);
// const supportModalEl    = document.getElementById('supportModal');
// const supportModal      = new bootstrap.Modal(supportModalEl);

// // ---- Settings ----
// const STORAGE_KEY    = 'lolays.businessProfile';
// const DEFAULT_LOGO   = '../image/lolaysLogo.jpg';
// const MAX_LOGO_BYTES = 2 * 1024 * 1024; // 2MB
// const ALLOWED_TYPES  = ['image/jpeg', 'image/png', 'image/svg+xml'];

// // Starting values (taken from the design). Replace with data from your backend.
// const DEFAULT_PROFILE = {
//   name: "Lolay's Catering",
//   description: 'We provide delicious and affordable catering services for all kinds of events. From birthdays to corporate gatherings, we make every occasion extra special.',
//   email: 'lolayscatering@gmail.com',
//   phone: '+63 912 345 6789',
//   address: '123 Food Street, San Pedro, Laguna',
//   website: 'www.lolayscatering.com',
//   facebook: 'facebook.com/lolayscatering',
//   logo: DEFAULT_LOGO
// };

// let currentLogo = '';

// // ---- Alert modal (replaces alert()) ----
// const ALERT_TYPES = {
//   info:    { cls: 'info',    icon: 'fa-circle-info' },
//   warning: { cls: 'warning', icon: 'fa-triangle-exclamation' },
//   success: { cls: 'success', icon: 'fa-circle-check' },
//   danger:  { cls: 'danger',  icon: 'fa-circle-xmark' }
// };

// // okText changes the button label; onClose runs once the alert has been dismissed.
// function showAlert(message, { title = 'Notice', type = 'info', okText = 'OK', onClose = null } = {}){
//   const cfg = ALERT_TYPES[type] || ALERT_TYPES.info;
//   document.getElementById('alertIcon').className = `confirm-icon-circle ${cfg.cls}`;
//   document.getElementById('alertIconGlyph').className = `fa-solid ${cfg.icon}`;
//   document.getElementById('alertTitle').textContent = title;
//   document.getElementById('alertMessage').textContent = message;
//   document.getElementById('alertOkBtn').textContent = okText;
//   if(onClose){
//     alertModalEl.addEventListener('hidden.bs.modal', onClose, { once: true });
//   }
//   alertModal.show();
// }

// // ---- Load / save (localStorage so you can test without a backend) ----
// function loadProfile(){
//   try {
//     const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null');
//     return saved ? { ...DEFAULT_PROFILE, ...saved } : { ...DEFAULT_PROFILE };
//   } catch (err) {
//     return { ...DEFAULT_PROFILE };
//   }
// }

// function persistProfile(profile){
//   try {
//     localStorage.setItem(STORAGE_KEY, JSON.stringify(profile));
//     return true;
//   } catch (err) {
//     return false; // storage full or blocked
//   }
// }

// // ---- Logo ----
// function setLogo(src){
//   currentLogo = src || '';
//   if(currentLogo){
//     logoPreview.src = currentLogo;
//     logoPreview.classList.remove('d-none');
//     logoEmpty.classList.add('d-none');
//   } else {
//     logoPreview.removeAttribute('src');
//     logoPreview.classList.add('d-none');
//     logoEmpty.classList.remove('d-none');
//   }
// }

// changeLogoBtn.addEventListener('click', () => logoInput.click());

// logoInput.addEventListener('change', () => {
//   const file = logoInput.files[0];
//   logoInput.value = ''; // lets the same file be picked again later
//   if(!file) return;

//   if(!ALLOWED_TYPES.includes(file.type)){
//     showAlert('Please choose a JPG, PNG or SVG image.', { title: 'Unsupported File', type: 'warning' });
//     return;
//   }
//   if(file.size > MAX_LOGO_BYTES){
//     showAlert('That image is larger than 2MB. Please choose a smaller one.', { title: 'File Too Large', type: 'warning' });
//     return;
//   }

//   const reader = new FileReader();
//   reader.onload = () => setLogo(reader.result);
//   reader.onerror = () => showAlert('We couldn\'t read that file. Please try another image.', { title: 'Upload Failed', type: 'danger' });
//   reader.readAsDataURL(file);
// });

// removeLogoBtn.addEventListener('click', () => {
//   if(!currentLogo){
//     showAlert('There is no logo to remove.', { title: 'No Logo', type: 'info' });
//     return;
//   }
//   removeLogoModal.show();
// });

// document.getElementById('confirmRemoveLogoBtn').addEventListener('click', () => {
//   setLogo('');
//   removeLogoModal.hide();
// });

// // ---- Description counter ----
// function updateCount(){
//   descCount.textContent = `${bizDescription.value.length}/${bizDescription.maxLength}`;
// }
// bizDescription.addEventListener('input', updateCount);

// // ---- Fill the form ----
// function fillForm(p){
//   bizName.value = p.name;
//   bizDescription.value = p.description;
//   bizEmail.value = p.email;
//   bizPhone.value = p.phone;
//   bizAddress.value = p.address;
//   bizWebsite.value = p.website;
//   bizFacebook.value = p.facebook;
//   setLogo(p.logo);
//   updateCount();
// }

// // ---- Save ----
// businessForm.addEventListener('submit', (e) => {
//   e.preventDefault();

//   const profile = {
//     name: bizName.value.trim(),
//     description: bizDescription.value.trim(),
//     email: bizEmail.value.trim(),
//     phone: bizPhone.value.trim(),
//     address: bizAddress.value.trim(),
//     website: bizWebsite.value.trim(),
//     facebook: bizFacebook.value.trim(),
//     logo: currentLogo
//   };

//   if(!profile.name){
//     showAlert('Please enter your business name.', { title: 'Missing Information', type: 'warning' });
//     return;
//   }
//   if(!/^[0-9+()\-\s]{7,20}$/.test(profile.phone)){
//     showAlert('Please enter a valid contact number, for example +63 912 345 6789.', { title: 'Invalid Contact Number', type: 'warning' });
//     return;
//   }

//   if(persistProfile(profile)){
//     showAlert('Your business profile has been updated successfully.', {
//       title: 'Changes Saved',
//       type: 'success',
//       okText: 'Go to Dashboard',
//       onClose: () => { window.location.href = '/admin/dashboard'; }
//     });
//   } else {
//     showAlert('Your changes couldn\'t be stored in this browser. If you uploaded a logo, try a smaller image.', { title: 'Could Not Save', type: 'danger' });
//   }
// });

// // ---- Contact Support ----
// const supportForm = document.getElementById('supportForm');

// document.getElementById('contactSupportBtn').addEventListener('click', () => {
//   supportForm.reset();
//   supportModal.show();
// });

// supportForm.addEventListener('submit', (e) => {
//   e.preventDefault();
//   // TODO: send { subject, message } to your backend / email service here.

//   // Close the form first, then show the confirmation
//   supportModalEl.addEventListener('hidden.bs.modal', () => {
//     showAlert('Your message has been sent to our support team. We\'ll get back to you soon.', { title: 'Message Sent', type: 'success' });
//   }, { once: true });
//   supportModal.hide();
// });

// // ---- Init ----
// fillForm(loadProfile());
// logo.js — Business Profile

// ---- DOM refs ----
const businessForm   = document.getElementById('businessForm');
const bizName        = document.getElementById('bizName');
const bizDescription = document.getElementById('bizDescription');
const bizEmail       = document.getElementById('bizEmail');
const bizPhone       = document.getElementById('bizPhone');
const bizAddress     = document.getElementById('bizAddress');
const bizWebsite     = document.getElementById('bizWebsite');
const bizFacebook    = document.getElementById('bizFacebook');
const descCount      = document.getElementById('descCount');

const logoPreview    = document.getElementById('logoPreview');
const logoEmpty      = document.getElementById('logoEmpty');
const logoInput      = document.getElementById('logoInput');
const changeLogoBtn  = document.getElementById('changeLogoBtn');
const removeLogoBtn  = document.getElementById('removeLogoBtn');

const alertModalEl      = document.getElementById('alertModal');
const alertModal        = new bootstrap.Modal(alertModalEl);
const removeLogoModalEl = document.getElementById('removeLogoModal');
const removeLogoModal   = new bootstrap.Modal(removeLogoModalEl);
const supportModalEl    = document.getElementById('supportModal');
const supportModal      = new bootstrap.Modal(supportModalEl);

// ---- Settings ----
const MAX_LOGO_BYTES = 2 * 1024 * 1024; // 2MB
const ALLOWED_TYPES  = ['image/jpeg', 'image/png', 'image/svg+xml'];
const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]').content;

let currentLogo      = '';    // ang ipinapakita sa preview
let selectedLogoFile = null;  // bagong file na ia-upload
let removeLogoFlag   = false; // true kapag pinindot ang Remove

// ---- Alert modal (replaces alert()) ----
const ALERT_TYPES = {
  info:    { cls: 'info',    icon: 'fa-circle-info' },
  warning: { cls: 'warning', icon: 'fa-triangle-exclamation' },
  success: { cls: 'success', icon: 'fa-circle-check' },
  danger:  { cls: 'danger',  icon: 'fa-circle-xmark' }
};

function showAlert(message, { title = 'Notice', type = 'info', okText = 'OK', onClose = null } = {}){
  const cfg = ALERT_TYPES[type] || ALERT_TYPES.info;
  document.getElementById('alertIcon').className = `confirm-icon-circle ${cfg.cls}`;
  document.getElementById('alertIconGlyph').className = `fa-solid ${cfg.icon}`;
  document.getElementById('alertTitle').textContent = title;
  document.getElementById('alertMessage').textContent = message;
  document.getElementById('alertOkBtn').textContent = okText;
  if(onClose){
    alertModalEl.addEventListener('hidden.bs.modal', onClose, { once: true });
  }
  alertModal.show();
}

// ---- Logo ----
function setLogo(src){
  currentLogo = src || '';
  if(currentLogo){
    logoPreview.src = currentLogo;
    logoPreview.classList.remove('d-none');
    logoEmpty.classList.add('d-none');
  } else {
    logoPreview.removeAttribute('src');
    logoPreview.classList.add('d-none');
    logoEmpty.classList.remove('d-none');
  }
}

changeLogoBtn.addEventListener('click', () => logoInput.click());

logoInput.addEventListener('change', () => {
  const file = logoInput.files[0];
  logoInput.value = ''; // lets the same file be picked again later
  if(!file) return;

  if(!ALLOWED_TYPES.includes(file.type)){
    showAlert('Please choose a JPG, PNG or SVG image.', { title: 'Unsupported File', type: 'warning' });
    return;
  }
  if(file.size > MAX_LOGO_BYTES){
    showAlert('That image is larger than 2MB. Please choose a smaller one.', { title: 'File Too Large', type: 'warning' });
    return;
  }

  const reader = new FileReader();
  reader.onload = () => {
    selectedLogoFile = file;   // ito ang ipapadala sa server
    removeLogoFlag = false;
    setLogo(reader.result);    // preview lang
  };
  reader.onerror = () => showAlert('We couldn\'t read that file. Please try another image.', { title: 'Upload Failed', type: 'danger' });
  reader.readAsDataURL(file);
});

removeLogoBtn.addEventListener('click', () => {
  if(!currentLogo){
    showAlert('There is no logo to remove.', { title: 'No Logo', type: 'info' });
    return;
  }
  removeLogoModal.show();
});

document.getElementById('confirmRemoveLogoBtn').addEventListener('click', () => {
  selectedLogoFile = null;
  removeLogoFlag = true;
  setLogo('');
  removeLogoModal.hide();
});

// ---- Description counter ----
function updateCount(){
  descCount.textContent = `${bizDescription.value.length}/${bizDescription.maxLength}`;
}
bizDescription.addEventListener('input', updateCount);

// ---- Save (sa database) ----
businessForm.addEventListener('submit', async (e) => {
  e.preventDefault();

  const profile = {
    name: bizName.value.trim(),
    description: bizDescription.value.trim(),
    email: bizEmail.value.trim(),
    phone: bizPhone.value.trim(),
    address: bizAddress.value.trim(),
    website: bizWebsite.value.trim(),
    facebook: bizFacebook.value.trim()
  };

  if(!profile.name){
    showAlert('Please enter your business name.', { title: 'Missing Information', type: 'warning' });
    return;
  }
  if(!/^[0-9+()\-\s]{7,20}$/.test(profile.phone)){
    showAlert('Please enter a valid contact number, for example +63 912 345 6789.', { title: 'Invalid Contact Number', type: 'warning' });
    return;
  }

  const fd = new FormData();
  fd.append('business_name',  profile.name);
  fd.append('description',    profile.description);
  fd.append('email',          profile.email);
  fd.append('contact_number', profile.phone);
  fd.append('address',        profile.address);
  fd.append('website',        profile.website);
  fd.append('facebook',       profile.facebook);

  if(selectedLogoFile){
    fd.append('logo', selectedLogoFile);
  } else if(removeLogoFlag){
    fd.append('remove_logo', '1');
  }

  const saveBtn = businessForm.querySelector('.btn-save');
  saveBtn.disabled = true;

  try {
    const res = await fetch(businessForm.action, {
      method: 'POST',
      body: fd,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF_TOKEN
      }
    });

    const data = await res.json().catch(() => ({}));

    if(!res.ok){
      const firstError = data.errors
        ? Object.values(data.errors)[0][0]
        : (data.message || 'Something went wrong. Please try again.');
      showAlert(firstError, { title: 'Could Not Save', type: 'danger' });
      return;
    }

    selectedLogoFile = null;
    removeLogoFlag = false;
    setLogo(data.logo_url || '');

    showAlert('Your business profile has been updated successfully.', {
      title: 'Changes Saved',
      type: 'success',
      okText: 'Go to Dashboard',
      onClose: () => { window.location.href = '/admin/dashboard'; }
    });
  } catch (err) {
    showAlert('We couldn\'t reach the server. Please check your connection and try again.', { title: 'Could Not Save', type: 'danger' });
  } finally {
    saveBtn.disabled = false;
  }
});

// ---- Contact Support ----
const supportForm = document.getElementById('supportForm');

document.getElementById('contactSupportBtn').addEventListener('click', () => {
  supportForm.reset();
  supportModal.show();
});

supportForm.addEventListener('submit', (e) => {
  e.preventDefault();
  // TODO: send { subject, message } to your backend / email service here.

  supportModalEl.addEventListener('hidden.bs.modal', () => {
    showAlert('Your message has been sent to our support team. We\'ll get back to you soon.', { title: 'Message Sent', type: 'success' });
  }, { once: true });
  supportModal.hide();
});

// ---- Init ----
// Ang laman ng form at ang logo ay galing na sa Blade (database).
setLogo(logoPreview.getAttribute('src'));
updateCount();