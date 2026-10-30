
document.addEventListener('DOMContentLoaded', function () {
    const menuRowsBody = document.getElementById('menuRows');
    const addMenuItemBtn = document.getElementById('addMenuItemBtn');

    const pkgName = document.getElementById('pkgName');
    const pkgPrice = document.getElementById('pkgPrice');
    const pkgCategory = document.getElementById('pkgCategory');
    const pkgGuestMin = document.getElementById('pkgGuestMin');
    const pkgGuestMax = document.getElementById('pkgGuestMax');
    const pkgDescription = document.getElementById('pkgDescription');

    const previewName = document.getElementById('previewName');
    const previewPrice = document.getElementById('previewPrice');
    const previewMeta = document.getElementById('previewMeta');
    const previewDesc = document.getElementById('previewDesc');
    const inclusionsList = document.getElementById('inclusionsList');

    const statusSwitch = document.getElementById('statusSwitch');
    const statusText = document.getElementById('statusText');

    const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    const photoPlaceholder = document.getElementById('photoPlaceholder');

    // Existing inclusions must be passed from edit-package.blade.php.
    const existingInclusions = window.EXISTING_INCLUSIONS || [];
    const oldInclusions = window.OLD_INCLUSIONS || [];
    const initialInclusions = oldInclusions.length
        ? oldInclusions
        : existingInclusions;

    function addInclusionRow(value = '') {
        const row = document.createElement('tr');

        const itemCell = document.createElement('td');
        itemCell.className = 'menu-row-item';
        itemCell.style.width = '85%';

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'inclusions[]';
        input.className = 'form-control inclusion-input';
        input.placeholder = 'e.g. Roasted Beef, Carbonara, Iced Tea';
        input.maxLength = 255;
        input.value = value;

        itemCell.appendChild(input);

        const actionCell = document.createElement('td');
        actionCell.className = 'text-center';
        actionCell.style.width = '15%';

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-delete-row remove-row';
        removeButton.setAttribute('aria-label', 'Remove inclusion');
        removeButton.innerHTML = '<i class="fa-solid fa-trash"></i>';

        actionCell.appendChild(removeButton);
        row.append(itemCell, actionCell);
        menuRowsBody.appendChild(row);
    }

    function getInclusions() {
        return [...menuRowsBody.querySelectorAll('.inclusion-input')]
            .map(input => input.value.trim())
            .filter(value => value !== '');
    }

    function updatePreview() {
        previewName.textContent =
            pkgName.value.trim() || 'Your package name';

        const price = parseFloat(pkgPrice.value);
        previewPrice.textContent = Number.isNaN(price)
            ? '₱0'
            : '₱' + price.toLocaleString('en-PH');

        const meta = [];

        if (pkgCategory.value) {
            meta.push(pkgCategory.value);
        }

        if (pkgGuestMin.value || pkgGuestMax.value) {
            meta.push(
                `${pkgGuestMin.value || '?'}–${pkgGuestMax.value || '?'} Guests`
            );
        }

        previewMeta.textContent = meta.join(' • ');

        previewDesc.textContent =
            pkgDescription.value.trim() ||
            'Your package description will appear here.';

        inclusionsList.innerHTML = '';

        const inclusions = getInclusions();

        if (inclusions.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'inclusion-empty';
            empty.textContent = 'No inclusions added yet.';
            inclusionsList.appendChild(empty);
            return;
        }

        inclusions.forEach(item => {
            const div = document.createElement('div');
            div.className = 'inclusion-item';

            const icon = document.createElement('i');
            icon.className = 'fa-solid fa-circle-check';

            div.append(icon, document.createTextNode(' ' + item));
            inclusionsList.appendChild(div);
        });
    }

    function updateStatusText() {
        if (!statusSwitch || !statusText) return;

        statusText.textContent =
            statusSwitch.checked ? 'Active' : 'Inactive';

        statusText.style.color =
            statusSwitch.checked ? '#1f7a34' : '#8a8f98';
    }

    addMenuItemBtn.addEventListener('click', function () {
        addInclusionRow();
        updatePreview();
    });

    menuRowsBody.addEventListener('input', function (event) {
        if (event.target.classList.contains('inclusion-input')) {
            updatePreview();
        }
    });

    menuRowsBody.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-row');
        if (!button) return;

        button.closest('tr').remove();

        if (menuRowsBody.querySelectorAll('tr').length === 0) {
            addInclusionRow();
        }

        updatePreview();
    });

    [pkgName, pkgPrice, pkgCategory, pkgGuestMin, pkgGuestMax, pkgDescription]
        .forEach(element => {
            if (!element) return;
            element.addEventListener('input', updatePreview);
            element.addEventListener('change', updatePreview);
        });

    if (statusSwitch) {
        statusSwitch.addEventListener('change', updateStatusText);
        updateStatusText();
    }

    if (uploadPhotoBtn && photoInput) {
        uploadPhotoBtn.addEventListener('click', () => photoInput.click());
    }

    if (photoInput && photoPreview && photoPlaceholder) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (event) {
                photoPreview.src = event.target.result;
                photoPreview.classList.remove('d-none');
                photoPlaceholder.classList.add('d-none');
            };

            reader.readAsDataURL(file);
        });
    }

    if (initialInclusions.length > 0) {
        initialInclusions.forEach(item => addInclusionRow(item));
    } else {
        addInclusionRow();
    }

    updatePreview();

    const deleteBtn = document.getElementById('deletePackageBtn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function () {
            if (confirm('Are you sure you want to delete this package?')) {
                alert('Use the package archive/delete action to remove this package.');
            }
        });
    }

    // Do not prevent the form's normal Laravel submission.
});
