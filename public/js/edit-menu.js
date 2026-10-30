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
const removeImageBtn    = document.getElementById('removeImageBtn');

const previewImgWrap   = document.getElementById('previewImgWrap');
const previewImg       = document.getElementById('previewImg');
const previewImgEmpty  = document.getElementById('previewImgEmpty');
const previewName      = document.getElementById('previewName');
const previewPrice     = document.getElementById('previewPrice');
const previewDesc      = document.getElementById('previewDesc');
const previewCategory  = document.getElementById('previewCategory');
const previewPrepTime  = document.getElementById('previewPrepTime');

const menuItemForm = document.getElementById('menuItemForm');
const deleteMenuBtn = document.getElementById('deleteMenuBtn');

// Delete confirmation modal elements
const deleteModalOverlay    = document.getElementById('deleteModalOverlay');
const deleteModalImg        = document.getElementById('deleteModalImg');
const deleteModalName       = document.getElementById('deleteModalName');
const deleteModalCancelBtn  = document.getElementById('deleteModalCancelBtn');
const deleteModalConfirmBtn = document.getElementById('deleteModalConfirmBtn');

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
    previewPrepTime.textContent = itemPrepTime?.value.trim() || '—';
  }
}

function updateStatusLabel(){
  if(!itemStatus || !itemStatusLabel) return;
  itemStatusLabel.textContent = itemStatus.checked ? 'Active' : 'Inactive';
}

function updateCategoryStyle(){
  if(!itemCategory) return;
  itemCategory.classList.toggle('has-value', Boolean(itemCategory.value));
}

function setPreviewImage(dataUrl){
  if(previewImg){
    previewImg.src = dataUrl;
    previewImg.classList.remove('d-none');
  }
  if(previewImgEmpty){
    previewImgEmpty.classList.add('d-none');
  }
}

function clearPreviewImage(){
  if(previewImg){
    previewImg.src = '';
    previewImg.classList.add('d-none');
  }
  if(previewImgEmpty){
    previewImgEmpty.classList.remove('d-none');
  }
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
    }
    uploadDropzone?.classList.add('has-image');

    // Also reflect it in the right-hand live preview card
    setPreviewImage(dataUrl);
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

// Remove-image ("x") button on the uploaded photo
removeImageBtn?.addEventListener('click', (e) => {
  // Don't let the click bubble up to the <label>, which would reopen the file picker.
  e.preventDefault();
  e.stopPropagation();

  if(itemImageInput) itemImageInput.value = '';
  if(uploadPreviewImg) uploadPreviewImg.src = '';
  uploadDropzone?.classList.remove('has-image');
  clearPreviewImage();
});

// Live preview bindings
itemName?.addEventListener('input', updatePreview);
itemDescription?.addEventListener('input', updatePreview);
itemCategory?.addEventListener('change', () => { updatePreview(); updateCategoryStyle(); });
itemPrice?.addEventListener('input', updatePreview);
itemPrepTime?.addEventListener('input', updatePreview);

itemStatus?.addEventListener('change', updateStatusLabel);

// ---- Delete Menu confirmation modal ----
function openDeleteModal(){
  if(deleteModalImg) deleteModalImg.src = uploadPreviewImg?.src || '';
  if(deleteModalName) deleteModalName.textContent = itemName?.value.trim() || 'this menu item';
  deleteModalOverlay.classList.remove('d-none');
  document.body.style.overflow = 'hidden';
}

function closeDeleteModal(){
  deleteModalOverlay.classList.add('d-none');
  document.body.style.overflow = '';
}

function confirmDelete(){
  closeDeleteModal();
  window.location.href = 'menu-management.html';
}

deleteMenuBtn?.addEventListener('click', openDeleteModal);
deleteModalCancelBtn?.addEventListener('click', closeDeleteModal);
deleteModalConfirmBtn?.addEventListener('click', confirmDelete);

// Click on the dark overlay (outside the card) also cancels.
deleteModalOverlay?.addEventListener('click', (e) => {
  if(e.target === deleteModalOverlay) closeDeleteModal();
});

// Esc key cancels the modal when it's open.
document.addEventListener('keydown', (e) => {
  if(e.key === 'Escape' && !deleteModalOverlay.classList.contains('d-none')){
    closeDeleteModal();
  }
});

// Form submit
menuItemForm?.addEventListener('submit', (e) => {


if(!itemName?.value.trim() || !itemDescription?.value.trim() || !itemCategory?.value){
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
    image: uploadDropzone?.classList.contains('has-image') ? uploadPreviewImg.src : null
  };

  // Hook this up to your actual save/update-menu-item API call.
  console.log('Saving menu item:', payload);
  alert('Menu item saved (check the console for the payload) — wire this up to your backend.');
});

updatePreview();
updateStatusLabel();
updateCategoryStyle();