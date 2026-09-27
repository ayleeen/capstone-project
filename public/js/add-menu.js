
const itemName        = document.getElementById('itemName');
const itemDescription = document.getElementById('itemDescription');
const itemCategory    = document.getElementById('itemCategory');
const itemPrice       = document.getElementById('itemPrice');
const itemStatus      = document.getElementById('itemStatus');
const itemStatusLabel = document.getElementById('itemStatusLabel');
const itemPrepTime    = document.getElementById('itemPrepTime');
const itemSpicyLevel  = document.getElementById('itemSpicyLevel');

const itemImageInput   = document.getElementById('itemImageInput');
const uploadDropzone    = document.getElementById('uploadDropzone');
const uploadPreviewImg  = document.getElementById('uploadPreviewImg');

const previewImg       = document.getElementById('previewImg');
const previewImgEmpty  = document.getElementById('previewImgEmpty');
const previewName      = document.getElementById('previewName');
const previewPrice     = document.getElementById('previewPrice');
const previewDesc      = document.getElementById('previewDesc');
const previewCategory  = document.getElementById('previewCategory');
const previewPrepTime  = document.getElementById('previewPrepTime');

const menuItemForm  = document.getElementById('menuItemForm');
const cancelFormBtn = document.getElementById('cancelFormBtn');

// Discard-changes confirmation modal (shown on Cancel)
const cancelModalOverlay     = document.getElementById('cancelModalOverlay');
const cancelModalKeepBtn     = document.getElementById('cancelModalKeepBtn');
const cancelModalDiscardBtn  = document.getElementById('cancelModalDiscardBtn');

function fmtPrice(n){
  const val = Number(n);
  if(!n || isNaN(val)) return '₱0';
  return '₱' + val.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

function updatePreview(){
  if(previewName){
    previewName.textContent = itemName?.value.trim() || 'Menu Item Name';
  }
  if(previewPrice){
    previewPrice.textContent = fmtPrice(itemPrice?.value);
  }
  if(previewDesc){
    previewDesc.textContent = itemDescription?.value.trim() || 'Description will appear here...';
  }
  if(previewCategory){
    previewCategory.textContent = itemCategory?.value || 'Category';
  }
  if(previewPrepTime){
    previewPrepTime.textContent = itemPrepTime?.value.trim() || '20 - 30 mins';
  }
}

function updateStatusLabel(){
  if(!itemStatus || !itemStatusLabel) return;
  itemStatusLabel.textContent = itemStatus.checked ? 'Active' : 'Inactive';
}

function handleImageFile(file){
  if(!file) return;

  const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
  if(!allowedTypes.includes(file.type)){
    alert('Please upload a JPG, PNG, or JPEG file.');
    return;
  }

  const maxSizeBytes = 5 * 1024 * 1024; // 5 MB
  if(file.size > maxSizeBytes){
    alert('Image must be 5 MB or smaller.');
    return;
  }

  const reader = new FileReader();
  reader.onload = (e) => {
    const dataUrl = e.target.result;

    // Show the uploaded image inside the dropzone itself
    if(uploadPreviewImg){
      uploadPreviewImg.src = dataUrl;
      uploadPreviewImg.classList.remove('d-none');
    }

    // Also reflect it in the right-hand live preview card
    if(previewImg){
      previewImg.src = dataUrl;
      previewImg.classList.remove('d-none');
    }
    if(previewImgEmpty){
      previewImgEmpty.classList.add('d-none');
    }
  };
  reader.readAsDataURL(file);
}

// File input change (click-to-upload)
itemImageInput?.addEventListener('change', (e) => {
  const file = e.target.files?.[0];
  handleImageFile(file);
});

// Drag & drop support
['dragenter', 'dragover'].forEach(evt => {
  uploadDropzone?.addEventListener(evt, (e) => {
    e.preventDefault();
    e.stopPropagation();
    uploadDropzone.classList.add('dragover');
  });
});

['dragleave', 'drop'].forEach(evt => {
  uploadDropzone?.addEventListener(evt, (e) => {
    e.preventDefault();
    e.stopPropagation();
    uploadDropzone.classList.remove('dragover');
  });
});

uploadDropzone?.addEventListener('drop', (e) => {
  const file = e.dataTransfer?.files?.[0];
  if(file){
    // keep the file input in sync in case the form relies on it
    const dt = new DataTransfer();
    dt.items.add(file);
    itemImageInput.files = dt.files;
  }
  handleImageFile(file);
});

// Live preview bindings
itemName?.addEventListener('input', updatePreview);
itemDescription?.addEventListener('input', updatePreview);
itemCategory?.addEventListener('change', updatePreview);
itemPrice?.addEventListener('input', updatePreview);
itemPrepTime?.addEventListener('input', updatePreview);

itemStatus?.addEventListener('change', updateStatusLabel);

// ---- Whether the form currently has anything worth confirming on ----
function formHasUnsavedInput(){
  return Boolean(
    itemName?.value.trim() ||
    itemDescription?.value.trim() ||
    itemCategory?.value ||
    itemPrice?.value ||
    itemPrepTime?.value.trim() ||
    itemSpicyLevel?.value ||
    (uploadPreviewImg && !uploadPreviewImg.classList.contains('d-none'))
  );
}

// ---- Discard-changes confirmation modal ----
function openCancelModal(){
  cancelModalOverlay.classList.remove('d-none');
  document.body.style.overflow = 'hidden';
}

function closeCancelModal(){
  cancelModalOverlay.classList.add('d-none');
  document.body.style.overflow = '';
}

function discardAndLeave(){
  closeCancelModal();
  window.location.href = "/admin/menu";
}

cancelFormBtn?.addEventListener('click', () => {
  // Only shown if the form has unsaved input
  if(formHasUnsavedInput()){
    openCancelModal();
  } else {
    discardAndLeave();
  }
});

cancelModalKeepBtn?.addEventListener('click', closeCancelModal);
cancelModalDiscardBtn?.addEventListener('click', discardAndLeave);

// Click on the dark overlay (outside the card) also keeps editing.
cancelModalOverlay?.addEventListener('click', (e) => {
  if(e.target === cancelModalOverlay) closeCancelModal();
});

// Esc key keeps editing when the modal is open.
document.addEventListener('keydown', (e) => {
  if(e.key === 'Escape' && !cancelModalOverlay.classList.contains('d-none')){
    closeCancelModal();
  }
});

// Form submit
menuItemForm?.addEventListener('submit', (e) => {
  if(!itemName?.value.trim() || !itemDescription?.value.trim() || !itemCategory?.value || !itemPrice?.value){
    e.preventDefault();
    alert('Please fill in all required fields marked with *.');
    return;
  }

  const payload = {
    name: itemName.value.trim(),
    description: itemDescription.value.trim(),
    category: itemCategory.value,
    price: Number(itemPrice.value),
    status: itemStatus.checked ? 'Active' : 'Inactive',
    prepTime: itemPrepTime.value.trim(),
    spicyLevel: itemSpicyLevel.value,
    image: uploadPreviewImg && !uploadPreviewImg.classList.contains('d-none') ? uploadPreviewImg.src : null
  };

  // Hook this up to your actual save/create-menu-item API call.
  console.log('Saving menu item:', payload);
  alert('Menu item saved (check the console for the payload) — wire this up to your backend.');
});

updatePreview();
updateStatusLabel();