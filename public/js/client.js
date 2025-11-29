// public/js/client.js
// Clean, single implementation for client page: intl-tel-input initialization, validation and E.164 handling

document.addEventListener('DOMContentLoaded', function () {
    // Intl init helper
    function initIntl(el) {
        if (!el || !window.intlTelInput) return null;
        if (el.dataset.itiInitialized) return null;
        const iti = window.intlTelInput(el, {
            separateDialCode: true,
            initialCountry: 'gn',
            preferredCountries: ['gn', 'sn', 'ci', 'ml'],
            utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js'
        });
        el.dataset.itiInitialized = '1';
        return iti;
    }

    // Validation helper -- toggles bootstrap valid/invalid state and returns boolean
    function validatePhone(el, iti, feedbackEl) {
        if (!el) return false;
        try {
            if (iti) {
                if (iti.isValidNumber()) {
                    el.classList.remove('is-invalid');
                    el.classList.add('is-valid');
                    if (feedbackEl) feedbackEl.style.display = 'none';
                    return true;
                } else {
                    el.classList.remove('is-valid');
                    if (el.value.trim().length === 0) {
                        el.classList.remove('is-invalid');
                        if (feedbackEl) feedbackEl.style.display = 'none';
                        return false;
                    }
                    el.classList.add('is-invalid');
                    if (feedbackEl) feedbackEl.style.display = 'block';
                    return false;
                }
            }
            const val = el.value.trim();
            if (val.length >= 6) { el.classList.add('is-valid'); el.classList.remove('is-invalid'); if (feedbackEl) feedbackEl.style.display = 'none'; return true; }
            el.classList.remove('is-valid'); el.classList.add('is-invalid'); if (feedbackEl) feedbackEl.style.display = 'block'; return false;
        } catch (err) { console.warn('validatePhone error', err); return false; }
    }

    // Setup add form phone inputs
    (function () {
        const telInput = document.getElementById('tel');
        const waInput = document.getElementById('whatsapp');
        const telFeedback = document.getElementById('telFeedback');
        const waFeedback = document.getElementById('whatsappFeedback');

        const telIti = initIntl(telInput);
        const waIti = initIntl(waInput);

        function attachEvents(inputEl, iti, feedbackEl) {
            if (!inputEl) return;
            inputEl.addEventListener('blur', () => validatePhone(inputEl, iti, feedbackEl));
            inputEl.addEventListener('input', () => { inputEl.classList.remove('is-valid'); inputEl.classList.remove('is-invalid'); if (feedbackEl) feedbackEl.style.display = 'none'; });
        }

        attachEvents(telInput, telIti, telFeedback);
        attachEvents(waInput, waIti, waFeedback);

        // On add form submit: set hidden e164 fields if intl returns a valid number
        const formAdd = document.getElementById('formAjoutClient');
        if (formAdd) {
            formAdd.addEventListener('submit', function (e) {
                if (telIti && !telIti.isValidNumber()) { validatePhone(telInput, telIti, telFeedback); e.preventDefault(); telInput.focus(); return false; }
                if (waIti && waInput && waInput.value.trim().length > 0 && !waIti.isValidNumber()) { validatePhone(waInput, waIti, waFeedback); e.preventDefault(); waInput.focus(); return false; }
                try { const hTel = document.getElementById('tel_e164'); if (telIti && hTel && telIti.isValidNumber()) hTel.value = telIti.getNumber(); const hWa = document.getElementById('whatsapp_e164'); if (waIti && hWa && waIti.isValidNumber()) hWa.value = waIti.getNumber(); } catch (err) { console.warn('set e164 error', err); }
            });
        }
    })();

    // Modal show (view)
    const voirModalEl = document.getElementById('voirClientModal');
    const voirModal = voirModalEl ? new bootstrap.Modal(voirModalEl) : null;
    const voirModalBody = document.getElementById('voirClientContent');
    async function showClient(id) {
        if (!voirModalBody) return;
        voirModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        try {
            const res = await fetch(`/clients/${id}/ajax-show`);
            const client = await res.json();
            if (client.error) { voirModalBody.innerHTML = `<p class="text-danger">${client.error}</p>`; return; }
            const statut = client.statut === 'actif' ? '<span class="text-success">Actif</span>' : '<span class="text-danger">Inactif</span>';
            voirModalBody.innerHTML = `
        <div class="row g-3">
          <div class="col-md-4 text-center"><img src="${client.image || 'https://via.placeholder.com/150'}" class="img-fluid rounded"/></div>
          <div class="col-md-8">
            <p><strong>Nom :</strong> ${client.nom}</p>
            <p><strong>Téléphone :</strong> ${client.tel}</p>
            <p><strong>WhatsApp :</strong> ${client.whatsapp || '-'}</p>
            <p><strong>Adresse :</strong> ${client.adresse || '-'}</p>
            ${client.latitude && client.longitude ? `<p><strong>Coordonnées :</strong> ${client.latitude}, ${client.longitude}</p><p><a href="https://www.google.com/maps?q=${client.latitude},${client.longitude}" target="_blank" rel="noopener">Voir sur Google Maps</a></p>` : ''}
            <p><strong>Statut :</strong> ${statut}</p>
            <p>${client.description || ''}</p>
          </div>
        </div>`;
            if (voirModal) voirModal.show();
        } catch (err) { console.error(err); voirModalBody.innerHTML = '<p class="text-danger">Erreur</p>'; }
    }

    // Modal edit
    const editModalEl = document.getElementById('editClientModal');
    const editModalObj = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    const editModalBody = document.getElementById('editClientContent');
    async function editClient(id) {
        if (!editModalBody) return;
        editModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        try {
            const res = await fetch(`/clients/${id}/ajax-edit`);
            const client = await res.json();
            if (client.error) { editModalBody.innerHTML = `<p class="text-danger">${client.error}</p>`; return; }
            editModalBody.innerHTML = `
        <form id="editClientForm" enctype="multipart/form-data">
        <div class="row g-3">
          <div class="col-md-4 text-center">
            <label class="form-label fw-bold">Photo</label>
            <div id="imageContainer" style="width:130px;height:130px;background:#f8f9fa;border-radius:50%;overflow:hidden;margin:auto;position:relative;">
              <img id="currentImage" src="${client.image || 'https://via.placeholder.com/130'}" class="w-100 h-100" style="object-fit:cover;"/>
              <input type="file" id="editImage" name="image" accept="image/*" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;"/>
            </div>
          </div>
          <div class="col-md-8">
            <label class="form-label fw-bold">Nom</label>
            <input type="text" id="editNom" class="form-control" value="${client.nom}" />
            <label class="form-label fw-bold mt-2">Téléphone</label>
            <input type="tel" id="editTel" class="form-control" value="${client.tel}" />
            <label class="form-label fw-bold mt-2">WhatsApp</label>
            <input type="tel" id="editWhatsapp" class="form-control" value="${client.whatsapp || ''}" />
            <label class="form-label fw-bold mt-2">Adresse</label>
            <div class="input-group">
              <input type="text" id="editAdresse" class="form-control" value="${client.adresse || ''}" />
              <button id="editDetectPositionBtn" class="btn btn-outline-secondary" type="button" title="Détecter position"><i class="fa fa-map-marker-alt"></i></button>
            </div>
            <input type="hidden" id="editLatitude" value="${client.latitude || ''}" />
            <input type="hidden" id="editLongitude" value="${client.longitude || ''}" />
            <div class="mt-3 text-end">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
              <button type="button" id="saveClientBtn" class="btn btn-success">Enregistrer</button>
            </div>
          </div>
        </div>
        </form>`;

            // Preview
            const inputImage = document.getElementById('editImage');
            const currentImage = document.getElementById('currentImage');
            if (inputImage && currentImage) {
                inputImage.addEventListener('change', function (e) {
                    const f = e.target.files[0]; if (!f) return; const r = new FileReader(); r.onload = ev => currentImage.src = ev.target.result; r.readAsDataURL(f);
                });
            }

            // Init intl on edit inputs
            const elEditTel = document.getElementById('editTel');
            const elEditWhatsapp = document.getElementById('editWhatsapp');
            const itiEditTel = initIntl(elEditTel);
            const itiEditWhatsapp = initIntl(elEditWhatsapp);

            // Geo detect
            const editDetectBtn = document.getElementById('editDetectPositionBtn');
            if (editDetectBtn) {
                editDetectBtn.addEventListener('click', function () {
                    if (!navigator.geolocation) { alert('Géolocalisation non supportée'); return; }
                    editDetectBtn.disabled = true; editDetectBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                    navigator.geolocation.getCurrentPosition(async (pos) => {
                        const lat = pos.coords.latitude; const lon = pos.coords.longitude;
                        document.getElementById('editLatitude').value = lat; document.getElementById('editLongitude').value = lon;
                        try { const r2 = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`); const d = await r2.json(); if (d && d.display_name) document.getElementById('editAdresse').value = d.display_name; } catch (err) { console.warn(err); }
                        editDetectBtn.disabled = false; editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>';
                    }, (err) => { editDetectBtn.disabled = false; editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>'; alert('Impossible de détecter la position'); }, { enableHighAccuracy: true, timeout: 10000 });
                });
            }

            // Save via AJAX - we prefer E.164 when available
            document.getElementById('saveClientBtn').addEventListener('click', function () {
                const f = new FormData(); f.append('_method', 'PUT'); f.append('nom', document.getElementById('editNom').value || '');
                if (itiEditTel && itiEditTel.isValidNumber()) { f.append('tel', itiEditTel.getNumber()); f.append('tel_e164', itiEditTel.getNumber()); } else { f.append('tel', document.getElementById('editTel').value || ''); }
                if (itiEditWhatsapp && itiEditWhatsapp.isValidNumber()) { f.append('whatsapp', itiEditWhatsapp.getNumber()); f.append('whatsapp_e164', itiEditWhatsapp.getNumber()); } else { f.append('whatsapp', document.getElementById('editWhatsapp').value || ''); }
                f.append('adresse', document.getElementById('editAdresse').value || ''); f.append('latitude', document.getElementById('editLatitude').value || ''); f.append('longitude', document.getElementById('editLongitude').value || '');
                if (inputImage && inputImage.files && inputImage.files[0]) f.append('image', inputImage.files[0]);
                fetch(`/clients/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: f })
                    .then(r => r.json()).then(resp => { if (resp.success) location.reload(); else alert(resp.message || 'Erreur'); }).catch(e => { console.error(e); alert('Erreur'); });
            });

            if (editModalObj) editModalObj.show();
        } catch (err) { console.error(err); editModalBody.innerHTML = '<p class="text-danger">Erreur</p>'; }
    }

    // Attach events
    document.querySelectorAll('.btn-view').forEach(btn => btn.addEventListener('click', () => showClient(btn.dataset.id)));
    document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', () => editClient(btn.dataset.id)));
});
// client.js — single clean version
document.addEventListener('DOMContentLoaded', () => {
    const voirModal = new bootstrap.Modal(document.getElementById('voirClientModal'));
    const voirModalBody = document.getElementById('voirClientContent');
    const editModal = new bootstrap.Modal(document.getElementById('editClientModal'));
    const editModalBody = document.getElementById('editClientContent');

    const initIntl = (el) => {
        if (!el || !window.intlTelInput) return null;
        if (el.dataset.itiInitialized) return null;
        const iti = window.intlTelInput(el, {
            separateDialCode: true,
            initialCountry: 'gn',
            preferredCountries: ['gn', 'sn', 'ci', 'ml'],
            utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js'
        });
        el.dataset.itiInitialized = '1';
        return iti;
    };

    // Init add form
    const telEl = document.getElementById('tel');
    const waEl = document.getElementById('whatsapp');
    const formAdd = document.getElementById('formAjoutClient');
    const itiTel = initIntl(telEl);
    const itiWhatsapp = initIntl(waEl);
    if (formAdd) formAdd.addEventListener('submit', () => {
        try { if (itiTel && itiTel.isValidNumber()) { telEl.value = itiTel.getNumber(); const h = document.getElementById('tel_e164'); if (h) h.value = itiTel.getNumber(); } } catch (e) { console.warn(e) }
        try { if (itiWhatsapp && itiWhatsapp.isValidNumber()) { waEl.value = itiWhatsapp.getNumber(); const h = document.getElementById('whatsapp_e164'); if (h) h.value = itiWhatsapp.getNumber(); } } catch (e) { console.warn(e) }
    });

    const showClient = async (id) => {
        if (!voirModalBody) return;
        voirModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        try {
            const r = await fetch(`/clients/${id}/ajax-show`);
            const client = await r.json();
            if (client.error) { voirModalBody.innerHTML = `<p class="text-danger">${client.error}</p>`; return; }
            const statut = client.statut === 'actif' ? '<span class="text-success">Actif</span>' : '<span class="text-danger">Inactif</span>';
            voirModalBody.innerHTML = `
        <div class="row g-3">
          <div class="col-md-4 text-center"><img src="${client.image || 'https://via.placeholder.com/150'}" class="img-fluid rounded"/></div>
          <div class="col-md-8">
            <p><strong>Nom :</strong> ${client.nom}</p>
            <p><strong>Téléphone :</strong> ${client.tel}</p>
            <p><strong>WhatsApp :</strong> ${client.whatsapp || '-'}</p>
            <p><strong>Adresse :</strong> ${client.adresse || '-'}</p>
            ${client.latitude && client.longitude ? `<p><strong>Coord :</strong> ${client.latitude}, ${client.longitude}</p><p><a href="https://www.google.com/maps?q=${client.latitude},${client.longitude}" target="_blank">Voir sur Google Maps</a></p>` : ''}
            <p><strong>Statut :</strong> ${statut}</p>
            <p>${client.description || ''}</p>
          </div>
        </div>`;
            voirModal.show();
        } catch (err) { console.error(err); voirModalBody.innerHTML = '<p class="text-danger">Erreur</p>'; }
    };

    const editClient = async (id) => {
        if (!editModalBody) return;
        editModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        try {
            const r = await fetch(`/clients/${id}/ajax-edit`);
            const client = await r.json();
            if (client.error) { editModalBody.innerHTML = `<p class="text-danger">${client.error}</p>`; return; }
            editModalBody.innerHTML = `
        <form id="editClientForm" enctype="multipart/form-data">
          <div class="row g-3">
            <div class="col-md-4 text-center">
              <label class="form-label fw-bold">Photo</label>
              <div id="imageContainer" style="width:130px;height:130px;background:#f8f9fa;border-radius:50%;overflow:hidden;margin:auto;position:relative;">
                <img id="currentImage" src="${client.image || 'https://via.placeholder.com/130'}" class="w-100 h-100" style="object-fit:cover;"/>
                <input type="file" id="editImage" name="image" accept="image/*" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;" />
              </div>
            </div>
            <div class="col-md-8">
              <label class="form-label fw-bold">Nom</label>
              <input type="text" id="editNom" value="${client.nom}" class="form-control" />
              <label class="form-label fw-bold mt-2">Téléphone</label>
              <input type="tel" id="editTel" value="${client.tel}" class="form-control" />
              <label class="form-label fw-bold mt-2">WhatsApp</label>
              <input type="tel" id="editWhatsapp" value="${client.whatsapp || ''}" class="form-control" />
              <label class="form-label fw-bold mt-2">Adresse</label>
              <div class="input-group">
                <input type="text" id="editAdresse" value="${client.adresse || ''}" class="form-control" />
                <button id="editDetectPositionBtn" class="btn btn-outline-secondary" type="button"><i class="fa fa-map-marker-alt"></i></button>
              </div>
              <input type="hidden" id="editLatitude" value="${client.latitude || ''}" />
              <input type="hidden" id="editLongitude" value="${client.longitude || ''}" />
              <div class="mt-3 text-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" id="saveClientBtn" class="btn btn-success">Enregistrer</button>
              </div>
            </div>
          </div>
        </form>`;

            // preview
            const inputImage = document.getElementById('editImage'); const currentImage = document.getElementById('currentImage');
            if (inputImage && currentImage) inputImage.addEventListener('change', (e) => { const f = e.target.files[0]; if (!f) return; const r = new FileReader(); r.onload = ev => currentImage.src = ev.target.result; r.readAsDataURL(f); });

            // intl
            const elEditTel = document.getElementById('editTel'); const elEditWhatsapp = document.getElementById('editWhatsapp');
            const itiEditTel = initIntl(elEditTel); const itiEditWhatsapp = initIntl(elEditWhatsapp);

            // geolocation
            const editDetectBtn = document.getElementById('editDetectPositionBtn');
            if (editDetectBtn) editDetectBtn.addEventListener('click', () => {
                if (!navigator.geolocation) { alert('Géolocalisation non supportée'); return; }
                editDetectBtn.disabled = true; editDetectBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                navigator.geolocation.getCurrentPosition(async (pos) => {
                    const lat = pos.coords.latitude, lon = pos.coords.longitude; document.getElementById('editLatitude').value = lat; document.getElementById('editLongitude').value = lon;
                    try { const r2 = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`); const d = await r2.json(); if (d && d.display_name) document.getElementById('editAdresse').value = d.display_name; } catch (e) { console.warn(e); }
                    editDetectBtn.disabled = false; editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>';
                }, (err) => { editDetectBtn.disabled = false; editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>'; alert('Impossible de détecter la position'); }, { enableHighAccuracy: true, timeout: 10000 });
            });

            // save
            document.getElementById('saveClientBtn').addEventListener('click', () => {
                const f = new FormData(); f.append('_method', 'PUT'); f.append('nom', document.getElementById('editNom').value || '');
                if (itiEditTel && itiEditTel.isValidNumber()) { f.append('tel', itiEditTel.getNumber()); f.append('tel_e164', itiEditTel.getNumber()); } else { f.append('tel', document.getElementById('editTel').value || ''); }
                if (itiEditWhatsapp && itiEditWhatsapp.isValidNumber()) { f.append('whatsapp', itiEditWhatsapp.getNumber()); f.append('whatsapp_e164', itiEditWhatsapp.getNumber()); } else { f.append('whatsapp', document.getElementById('editWhatsapp').value || ''); }
                f.append('adresse', document.getElementById('editAdresse').value || ''); f.append('latitude', document.getElementById('editLatitude').value || ''); f.append('longitude', document.getElementById('editLongitude').value || '');
                if (inputImage && inputImage.files && inputImage.files[0]) f.append('image', inputImage.files[0]);
                fetch(`/clients/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: f })
                    .then(r => r.json()).then(resp => { if (resp.success) location.reload(); else alert(resp.message || 'Erreur'); }).catch(e => { console.error(e); alert('Erreur'); });
            });

            editModal.show();
        }).catch(e => { console.error(e); editModalBody.innerHTML = '<p class="text-danger">Erreur</p>'; });
  };

// Table buttons
document.querySelectorAll('.btn-view').forEach(btn => btn.addEventListener('click', () => showClient(btn.dataset.id)));
document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', () => editClient(btn.dataset.id)));
});
// client.js — Single clean file: modals, tel input and geolocation
document.addEventListener('DOMContentLoaded', () => {
    const voirModal = new bootstrap.Modal(document.getElementById('voirClientModal'));
    const voirModalBody = document.getElementById('voirClientContent');
    const editModal = new bootstrap.Modal(document.getElementById('editClientModal'));
    const editModalBody = document.getElementById('editClientContent');

    function initIntl(el) {
        if (!el || !window.intlTelInput) return null;
        if (el.dataset.itiInitialized) return null;
        const iti = window.intlTelInput(el, {
            separateDialCode: true,
            initialCountry: 'gn',
            preferredCountries: ['gn', 'sn', 'ci', 'ml'],
            utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js'
        });
        el.dataset.itiInitialized = '1';
        return iti;
    }

    // init add form
    const telEl = document.getElementById('tel');
    const waEl = document.getElementById('whatsapp');
    const itiTel = initIntl(telEl);
    const itiWhatsapp = initIntl(waEl);
    const formAdd = document.getElementById('formAjoutClient');
    if (formAdd) {
        formAdd.addEventListener('submit', () => {
            try {
                if (itiTel && itiTel.isValidNumber()) {
                    telEl.value = itiTel.getNumber();
                    const h = document.getElementById('tel_e164'); if (h) h.value = itiTel.getNumber();
                }
                if (itiWhatsapp && itiWhatsapp.isValidNumber()) {
                    waEl.value = itiWhatsapp.getNumber();
                    const h2 = document.getElementById('whatsapp_e164'); if (h2) h2.value = itiWhatsapp.getNumber();
                }
            } catch (e) { console.warn(e); }
        });
    }

    function showClient(id) {
        if (!voirModalBody) return;
        voirModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        fetch(`/clients/${id}/ajax-show`).then(r => r.json()).then(client => {
            if (client.error) { voirModalBody.innerHTML = `<p class="text-danger">${client.error}</p>`; return; }
            const statut = client.statut === 'actif' ? '<span class="text-success">Actif</span>' : '<span class="text-danger">Inactif</span>';
            voirModalBody.innerHTML = `
        <div class="row g-3">
          <div class="col-md-4 text-center"><img src="${client.image || 'https://via.placeholder.com/150'}" class="img-fluid rounded"/></div>
          <div class="col-md-8">
            <p><strong>Nom :</strong> ${client.nom}</p>
            <p><strong>Téléphone :</strong> ${client.tel}</p>
            <p><strong>WhatsApp :</strong> ${client.whatsapp || '-'}</p>
            <p><strong>Adresse :</strong> ${client.adresse || '-'}</p>
            ${client.latitude && client.longitude ? `<p><strong>Coordonnées :</strong> ${client.latitude}, ${client.longitude}</p><p><a href="https://www.google.com/maps?q=${client.latitude},${client.longitude}" target="_blank">Voir sur Google Maps</a></p>` : ''}
            <p><strong>Statut :</strong> ${statut}</p>
            <p>${client.description || ''}</p>
          </div>
        </div>`;
            voirModal.show();
        }).catch(err => { console.error(err); voirModalBody.innerHTML = '<p class="text-danger">Erreur</p>'; });
    }

    function editClient(id) {
        if (!editModalBody) return;
        editModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        fetch(`/clients/${id}/ajax-edit`).then(r => r.json()).then(client => {
            if (client.error) { editModalBody.innerHTML = `<p class="text-danger">${client.error}</p>`; return; }
            editModalBody.innerHTML = `
        <form id="editClientForm" enctype="multipart/form-data">
          <div class="row g-3">
            <div class="col-md-4 text-center">
              <label class="form-label fw-bold">Photo</label>
              <div id="imageContainer" style="width:130px;height:130px;background:#f8f9fa;border-radius:50%;overflow:hidden;margin:auto;position:relative;">
                <img id="currentImage" src="${client.image || 'https://via.placeholder.com/130'}" class="w-100 h-100" style="object-fit:cover;"/>
                <input type="file" id="editImage" name="image" accept="image/*" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;"/>
              </div>
            </div>
            <div class="col-md-8">
              <label class="form-label fw-bold">Nom</label>
              <input type="text" id="editNom" value="${client.nom}" class="form-control" />
              <label class="form-label fw-bold mt-2">Téléphone</label>
              <input type="tel" id="editTel" value="${client.tel}" class="form-control" />
              <label class="form-label fw-bold mt-2">WhatsApp</label>
              <input type="tel" id="editWhatsapp" value="${client.whatsapp || ''}" class="form-control" />
              <label class="form-label fw-bold mt-2">Adresse</label>
              <div class="input-group">
                <input type="text" id="editAdresse" value="${client.adresse || ''}" class="form-control"/>
                <button id="editDetectPositionBtn" class="btn btn-outline-secondary" type="button"><i class="fa fa-map-marker-alt"></i></button>
              </div>
              <input type="hidden" id="editLatitude" value="${client.latitude || ''}" />
              <input type="hidden" id="editLongitude" value="${client.longitude || ''}" />
              <div class="mt-3 text-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" id="saveClientBtn" class="btn btn-success">Enregistrer</button>
              </div>
            </div>
          </div>
        </form>`;

            // image preview
            const inputImage = document.getElementById('editImage');
            const currentImage = document.getElementById('currentImage');
            if (inputImage && currentImage) {
                inputImage.addEventListener('change', (e) => {
                    const f = e.target.files[0]; if (!f) return; const r = new FileReader(); r.onload = ev => currentImage.src = ev.target.result; r.readAsDataURL(f);
                });
            }

            // init intl for edit inputs
            const elEditTel = document.getElementById('editTel');
            const elEditWhatsapp = document.getElementById('editWhatsapp');
            const itiEditTel = initIntl(elEditTel);
            const itiEditWhatsapp = initIntl(elEditWhatsapp);

            // geolocation
            const editDetectBtn = document.getElementById('editDetectPositionBtn');
            if (editDetectBtn) {
                editDetectBtn.addEventListener('click', () => {
                    if (!navigator.geolocation) { alert('Géolocalisation non supportée'); return; }
                    editDetectBtn.disabled = true; editDetectBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                    navigator.geolocation.getCurrentPosition(async (pos) => {
                        const lat = pos.coords.latitude, lon = pos.coords.longitude;
                        document.getElementById('editLatitude').value = lat; document.getElementById('editLongitude').value = lon;
                        try { const r = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`); const d = await r.json(); if (d && d.display_name) document.getElementById('editAdresse').value = d.display_name; } catch (e) { console.warn(e); }
                        editDetectBtn.disabled = false; editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>';
                    }, (err) => { editDetectBtn.disabled = false; editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>'; alert('Impossible de détecter la position'); }, { enableHighAccuracy: true, timeout: 10000 });
                });
            }

            // Save
            document.getElementById('saveClientBtn').addEventListener('click', () => {
                const f = new FormData(); f.append('_method', 'PUT'); f.append('nom', document.getElementById('editNom').value || '');
                if (itiEditTel && itiEditTel.isValidNumber()) { f.append('tel', itiEditTel.getNumber()); f.append('tel_e164', itiEditTel.getNumber()); } else { f.append('tel', document.getElementById('editTel').value || ''); }
                if (itiEditWhatsapp && itiEditWhatsapp.isValidNumber()) { f.append('whatsapp', itiEditWhatsapp.getNumber()); f.append('whatsapp_e164', itiEditWhatsapp.getNumber()); } else { f.append('whatsapp', document.getElementById('editWhatsapp').value || ''); }
                f.append('adresse', document.getElementById('editAdresse').value || ''); f.append('latitude', document.getElementById('editLatitude').value || ''); f.append('longitude', document.getElementById('editLongitude').value || '');
                if (inputImage && inputImage.files && inputImage.files[0]) f.append('image', inputImage.files[0]);
                fetch(`/clients/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: f })
                    .then(r => r.json()).then(resp => { if (resp.success) location.reload(); else alert(resp.message || 'Erreur'); }).catch(e => { console.error(e); alert('Erreur'); });
            });

            editModal.show();
        }).catch(e => { console.error(e); editModalBody.innerHTML = '<p class="text-danger">Erreur</p>'; });
    }

    document.querySelectorAll('.btn-view').forEach(btn => btn.addEventListener('click', () => showClient(btn.dataset.id)));
    document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', () => editClient(btn.dataset.id)));
});
// client.js - Manage client modals, geolocation and phone formatting with intl-tel-input
document.addEventListener('DOMContentLoaded', function () {
    const voirModal = new bootstrap.Modal(document.getElementById('voirClientModal'));
    const voirModalBody = document.getElementById('voirClientContent');
    const editModal = new bootstrap.Modal(document.getElementById('editClientModal'));
    const editModalBody = document.getElementById('editClientContent');

    // Helper to safely init intlTelInput on an element only once
    function initIntl(el) {
        if (!el || !window.intlTelInput) return null;
        if (el.hasAttribute('data-iti-initialized')) return null;
        const iti = window.intlTelInput(el, {
            separateDialCode: true,
            initialCountry: 'gn',
            preferredCountries: ['gn', 'sn', 'ci', 'ml'],
            utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js'
        });
        el.setAttribute('data-iti-initialized', '1');
        return iti;
    }

    // init add form phones if present
    const telEl = document.getElementById('tel');
    const waEl = document.getElementById('whatsapp');
    const itiTel = initIntl(telEl);
    const itiWhatsapp = initIntl(waEl);

    // On add form submit: set hidden E.164 fields (if any) before submit
    const formAdd = document.getElementById('formAjoutClient');
    if (formAdd) {
        formAdd.addEventListener('submit', function () {
            try {
                if (itiTel && itiTel.isValidNumber()) {
                    telEl.value = itiTel.getNumber();
                    const h = document.getElementById('tel_e164'); if (h) h.value = itiTel.getNumber();
                }
                if (itiWhatsapp && itiWhatsapp.isValidNumber()) {
                    waEl.value = itiWhatsapp.getNumber();
                    const h2 = document.getElementById('whatsapp_e164'); if (h2) h2.value = itiWhatsapp.getNumber();
                }
            } catch (err) { console.warn('phone init error', err); }
        });
    }

    // Show client details
    function showClient(id) {
        voirModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        fetch(`/clients/${id}/ajax-show`).then(r => r.json()).then(client => {
            if (client.error) {
                voirModalBody.innerHTML = `<p class="text-danger">${client.error}</p>`;
                return;
            }
            const statut = client.statut === 'actif' ? '<span class="text-success">Actif</span>' : '<span class="text-danger">Inactif</span>';
            voirModalBody.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-4 text-center">
                        <img src="${client.image || 'https://via.placeholder.com/150'}" class="img-fluid rounded" />
                    </div>
                    <div class="col-md-8">
                        <p><strong>Nom :</strong> ${client.nom}</p>
                        <p><strong>Téléphone :</strong> ${client.tel}</p>
                        <p><strong>WhatsApp :</strong> ${client.whatsapp || '-'}</p>
                        <p><strong>Adresse :</strong> ${client.adresse || '-'}</p>
                        ${client.latitude && client.longitude ? `<p><strong>Coordonnées :</strong> ${client.latitude}, ${client.longitude}</p><p><a href="https://www.google.com/maps?q=${client.latitude},${client.longitude}" target="_blank">Voir sur Google Maps</a></p>` : ''}
                        <p><strong>Statut :</strong> ${statut}</p>
                        <p>${client.description || ''}</p>
                    </div>
                </div>`;
            voirModal.show();
        }).catch(err => { console.error(err); voirModalBody.innerHTML = '<p class="text-danger">Erreur</p>'; });
    }

    // Edit client (displays modal, init intl inputs and save via AJAX)
    function editClient(id) {
        editModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        fetch(`/clients/${id}/ajax-edit`).then(r => r.json()).then(client => {
            if (client.error) {
                editModalBody.innerHTML = `<p class="text-danger">${client.error}</p>`; return;
            }

            // Build a richer edit form (includes image preview and geolocation button)
            editModalBody.innerHTML = `
                <form id="editClientForm" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-4 text-center">
                            <label class="form-label fw-bold">Photo</label>
                            <div id="imageContainer" style="width:130px;height:130px;background:#f8f9fa;border-radius:50%;overflow:hidden;cursor:pointer;margin:auto;position:relative;">
                                <img id="currentImage" src="${client.image || 'https://via.placeholder.com/130'}" class="w-100 h-100" style="object-fit:cover;" />
                                <input type="file" id="editImage" name="image" accept="image/*" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Nom</label>
                            <input type="text" id="editNom" value="${client.nom}" class="form-control" />
                            <label class="form-label fw-bold mt-2">Téléphone</label>
                            <input type="tel" id="editTel" value="${client.tel}" class="form-control" />
                            <label class="form-label fw-bold mt-2">WhatsApp</label>
                            <input type="tel" id="editWhatsapp" value="${client.whatsapp || ''}" class="form-control" />
                            <label class="form-label fw-bold mt-2">Adresse</label>
                            <div class="input-group">
                                <input type="text" id="editAdresse" value="${client.adresse || ''}" class="form-control" />
                                <button id="editDetectPositionBtn" class="btn btn-outline-secondary" type="button"><i class="fa fa-map-marker-alt"></i></button>
                            </div>
                            <input type="hidden" id="editLatitude" value="${client.latitude || ''}" />
                            <input type="hidden" id="editLongitude" value="${client.longitude || ''}" />
                            <div class="mt-3 text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="button" id="saveClientBtn" class="btn btn-success">Enregistrer</button>
                            </div>
                        </div>
                    </div>
                </form>`;

            // Image preview
            const inputImage = document.getElementById('editImage');
            const currentImage = document.getElementById('currentImage');
            if (inputImage && currentImage) {
                inputImage.addEventListener('change', function (e) {
                    const f = e.target.files[0]; if (!f) return;
                    const reader = new FileReader(); reader.onload = (ev) => currentImage.src = ev.target.result; reader.readAsDataURL(f);
                });
            }

            // init intl on edit fields
            const elEditTel = document.getElementById('editTel');
            const elEditWhatsapp = document.getElementById('editWhatsapp');
            const itiEditTel = initIntl(elEditTel);
            const itiEditWhatsapp = initIntl(elEditWhatsapp);

            // geolocation
            const editDetectBtn = document.getElementById('editDetectPositionBtn');
            if (editDetectBtn) {
                editDetectBtn.addEventListener('click', function () {
                    if (!navigator.geolocation) { alert('Géolocalisation non supportée'); return; }
                    editDetectBtn.disabled = true; editDetectBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                    navigator.geolocation.getCurrentPosition(async function (pos) {
                        const lat = pos.coords.latitude; const lon = pos.coords.longitude;
                        document.getElementById('editLatitude').value = lat; document.getElementById('editLongitude').value = lon;
                        try { const r = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`); const d = await r.json(); if (d && d.display_name) document.getElementById('editAdresse').value = d.display_name; } catch (e) { console.warn(e); }
                        editDetectBtn.disabled = false; editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>';
                    }, function (err) { editDetectBtn.disabled = false; editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>'; alert('Impossible de détecter la position'); }, { enableHighAccuracy: true, timeout: 10000 });
                });
            }

            // Save via AJAX and refresh page if success for simplicity
            document.getElementById('saveClientBtn').addEventListener('click', function () {
                const f = new FormData(); f.append('_method', 'PUT'); f.append('nom', document.getElementById('editNom').value || '');
                if (itiEditTel && itiEditTel.isValidNumber()) { f.append('tel', itiEditTel.getNumber()); f.append('tel_e164', itiEditTel.getNumber()); } else { f.append('tel', document.getElementById('editTel').value || ''); }
                if (itiEditWhatsapp && itiEditWhatsapp.isValidNumber()) { f.append('whatsapp', itiEditWhatsapp.getNumber()); f.append('whatsapp_e164', itiEditWhatsapp.getNumber()); } else { f.append('whatsapp', document.getElementById('editWhatsapp').value || ''); }
                f.append('adresse', document.getElementById('editAdresse').value || ''); f.append('latitude', document.getElementById('editLatitude').value || ''); f.append('longitude', document.getElementById('editLongitude').value || '');
                if (inputImage && inputImage.files && inputImage.files[0]) f.append('image', inputImage.files[0]);
                fetch(`/clients/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: f })
                    .then(r => r.json()).then(resp => { if (resp.success) { location.reload(); } else { alert(resp.message || 'Erreur'); } }).catch(e => { console.error(e); alert('Erreur'); });
            });

            editModal.show();
        }).catch(err => { editModalBody.innerHTML = '<p class="text-danger">Erreur</p>'; console.error(err); });
    }

    // Attach handlers to view/edit buttons in lists
    document.querySelectorAll('.btn-view').forEach(btn => btn.addEventListener('click', () => showClient(btn.dataset.id)));
    document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', () => editClient(btn.dataset.id)));
});
document.addEventListener('DOMContentLoaded', function () {
    const voirModal = new bootstrap.Modal(document.getElementById('voirClientModal'));
    const voirModalBody = document.getElementById('voirClientContent');
    const editModal = new bootstrap.Modal(document.getElementById('editClientModal'));
    const editModalBody = document.getElementById('editClientContent');

    // Initialize intl-tel-input for add form (if present)
    const telEl = document.getElementById('tel');
    const waEl = document.getElementById('whatsapp');
    let itiTel = null, itiWhatsapp = null;
    if (window.intlTelInput) {
        if (telEl && !telEl.hasAttribute('data-iti-initialized')) {
            itiTel = window.intlTelInput(telEl, { separateDialCode: true, initialCountry: 'gn', utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js' });
            telEl.setAttribute('data-iti-initialized', '1');
        }
        if (waEl && !waEl.hasAttribute('data-iti-initialized')) {
            itiWhatsapp = window.intlTelInput(waEl, { separateDialCode: true, initialCountry: 'gn', utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js' });
            waEl.setAttribute('data-iti-initialized', '1');
        }
    }

    // On add form submit: set tel in E.164 in hidden fields
    const formAdd = document.getElementById('formAjoutClient');
    if (formAdd) {
        formAdd.addEventListener('submit', function () {
            try {
                if (itiTel && itiTel.isValidNumber()) {
                    const v = itiTel.getNumber();
                    telEl.value = v;
                    const el = document.getElementById('tel_e164'); if (el) el.value = v;
                }
                if (itiWhatsapp && itiWhatsapp.isValidNumber()) {
                    const v2 = itiWhatsapp.getNumber();
                    waEl.value = v2;
                    const el2 = document.getElementById('whatsapp_e164'); if (el2) el2.value = v2;
                }
            } catch (e) { console.warn(e); }
        });
    }

    function showClient(id) {
        voirModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        fetch(`/clients/${id}/ajax-show`).then(r => r.json()).then(client => {
            if (client.error) { voirModalBody.innerHTML = `<p class="text-danger">${client.error}</p>`; return; }
            let statutHtml = client.statut === 'actif' ? '<span class="text-success">Actif</span>' : '<span class="text-danger">Inactif</span>';
            voirModalBody.innerHTML = `<div class="row g-3"><div class="col-md-4 text-center"><img src="${client.image || 'https://via.placeholder.com/150'}" class="img-fluid rounded"/></div><div class="col-md-8"><p><strong>Nom:</strong> ${client.nom}</p><p><strong>Téléphone:</strong> ${client.tel}</p><p><strong>WhatsApp:</strong> ${client.whatsapp || '-'}</p><p><strong>Adresse:</strong> ${client.adresse || '-'}</p>${client.latitude && client.longitude ? `<p>Coordonnées: ${client.latitude}, ${client.longitude}</p><p><a href="https://www.google.com/maps?q=${client.latitude},${client.longitude}" target="_blank">Voir sur Google Maps</a></p>` : ''}<p>${statutHtml}</p><p>${client.description || ''}</p></div></div>`;
            voirModal.show();
        }).catch(err => { voirModalBody.innerHTML = '<p class="text-danger">Erreur</p>'; console.error(err); });
    }

    function editClient(id) {
        editModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        fetch(`/clients/${id}/ajax-edit`).then(r => r.json()).then(client => {
            if (client.error) { editModalBody.innerHTML = `<p class="text-danger">${client.error}</p>`; return; }
            editModalBody.innerHTML = `
                <form id="editClientForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Nom</label>
                            <input id="editNom" value="${client.nom}" class="form-control"/>
                            <label class="mt-2">Téléphone</label>
                            <input id="editTel" value="${client.tel}" class="form-control"/>
                        </div>
                        <div class="col-md-6">
                            <label>WhatsApp</label>
                            <input id="editWhatsapp" value="${client.whatsapp || ''}" class="form-control"/>
                            <label class="mt-2">Adresse</label>
                            <div class="input-group">
                                <input id="editAdresse" value="${client.adresse || ''}" class="form-control"/>
                                <button id="editDetectPositionBtn" class="btn btn-outline-secondary" type="button"><i class="fa fa-map-marker-alt"></i></button>
                            </div>
                            <input type="hidden" id="editLatitude" name="latitude" value="${client.latitude || ''}" />
                            <input type="hidden" id="editLongitude" name="longitude" value="${client.longitude || ''}" />
                            <input type="hidden" id="editTelE164" name="tel_e164" value="" />
                            <input type="hidden" id="editWhatsappE164" name="whatsapp_e164" value="" />
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" id="saveClientBtn" class="btn btn-success">Enregistrer</button>
                        </div>
                    </div>
                </form>`;
            const elEditTel = document.getElementById('editTel');
            const elEditWhatsapp = document.getElementById('editWhatsapp');
            let itiEditTel = null, itiEditWhatsapp = null;
            if (window.intlTelInput) {
                if (elEditTel && !elEditTel.hasAttribute('data-iti-initialized')) { itiEditTel = window.intlTelInput(elEditTel, { separateDialCode: true, initialCountry: 'gn', utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js' }); elEditTel.setAttribute('data-iti-initialized', '1'); }
                if (elEditWhatsapp && !elEditWhatsapp.hasAttribute('data-iti-initialized')) { itiEditWhatsapp = window.intlTelInput(elEditWhatsapp, { separateDialCode: true, initialCountry: 'gn', utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js' }); elEditWhatsapp.setAttribute('data-iti-initialized', '1'); }
            }
            const editDetectBtn = document.getElementById('editDetectPositionBtn');
            if (editDetectBtn) {
                editDetectBtn.addEventListener('click', function () {
                    if (!navigator.geolocation) { alert('Géolocalisation non supportée'); return; }
                    editDetectBtn.disabled = true; editDetectBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                    navigator.geolocation.getCurrentPosition(async function (pos) {
                        const lat = pos.coords.latitude; const lon = pos.coords.longitude;
                        document.getElementById('editLatitude').value = lat; document.getElementById('editLongitude').value = lon;
                        try { const r = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`); const data = await r.json(); if (data && data.display_name) document.getElementById('editAdresse').value = data.display_name; } catch (e) { console.warn(e); }
                        editDetectBtn.disabled = false; editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>';
                    }, function (err) { editDetectBtn.disabled = false; editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>'; alert('Impossible d\'obtenir la position'); }, { enableHighAccuracy: true, timeout: 10000 });
                });
            }
            document.getElementById('saveClientBtn').addEventListener('click', function () {
                const f = new FormData(); f.append('_method', 'PUT'); f.append('nom', document.getElementById('editNom').value || '');
                if (itiEditTel && itiEditTel.isValidNumber()) { f.append('tel', itiEditTel.getNumber()); f.append('tel_e164', itiEditTel.getNumber()); } else { f.append('tel', document.getElementById('editTel').value || ''); }
                if (itiEditWhatsapp && itiEditWhatsapp.isValidNumber()) { f.append('whatsapp', itiEditWhatsapp.getNumber()); f.append('whatsapp_e164', itiEditWhatsapp.getNumber()); } else { f.append('whatsapp', document.getElementById('editWhatsapp').value || ''); }
                f.append('adresse', document.getElementById('editAdresse').value || ''); f.append('latitude', document.getElementById('editLatitude').value || ''); f.append('longitude', document.getElementById('editLongitude').value || '');
                fetch(`/clients/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: f })
                    .then(r => r.json()).then(resp => { if (resp.success) { location.reload(); } else { alert(resp.message || 'Erreur'); } }).catch(e => { console.error(e); alert('Erreur'); });
            });
            editModal.show();
        }).catch(err => { editModalBody.innerHTML = '<p class="text-danger">Erreur</p>'; console.error(err); });
    }
    document.querySelectorAll('.btn-view').forEach(btn => btn.addEventListener('click', () => showClient(btn.dataset.id)));
    document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', () => editClient(btn.dataset.id)));
});
document.addEventListener('DOMContentLoaded', function () {
    const voirModal = new bootstrap.Modal(document.getElementById('voirClientModal'));
    const voirModalBody = document.getElementById('voirClientContent');
    const editModal = new bootstrap.Modal(document.getElementById('editClientModal'));
    const editModalBody = document.getElementById('editClientContent');

    // Initialize intl-tel-input for add form (if present)
    const telEl = document.getElementById('tel');
    const waEl = document.getElementById('whatsapp');
    let itiTel = null, itiWhatsapp = null;
    if (window.intlTelInput) {
        if (telEl && !telEl.hasAttribute('data-iti-initialized')) {
            itiTel = window.intlTelInput(telEl, { separateDialCode: true, initialCountry: 'gn', utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js' });
            telEl.setAttribute('data-iti-initialized', '1');
        }
        if (waEl && !waEl.hasAttribute('data-iti-initialized')) {
            itiWhatsapp = window.intlTelInput(waEl, { separateDialCode: true, initialCountry: 'gn', utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js' });
            waEl.setAttribute('data-iti-initialized', '1');
        }
    }

    // On add form submit: set tel in E.164 in hidden fields
    const formAdd = document.getElementById('formAjoutClient');
    if (formAdd) {
        formAdd.addEventListener('submit', function () {
            try {
                if (itiTel && itiTel.isValidNumber()) {
                    const v = itiTel.getNumber();
                    telEl.value = v;
                    const el = document.getElementById('tel_e164'); if (el) el.value = v;
                }
                if (itiWhatsapp && itiWhatsapp.isValidNumber()) {
                    const v2 = itiWhatsapp.getNumber();
                    waEl.value = v2;
                    const el2 = document.getElementById('whatsapp_e164'); if (el2) el2.value = v2;
                }
            } catch (e) { console.warn(e); }
        });
    }

    // Show client
    function showClient(id) {
        voirModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        fetch(`/clients/${id}/ajax-show`).then(r => r.json()).then(client => {
            if (client.error) { voirModalBody.innerHTML = `<p class="text-danger">${client.error}</p>`; return; }
            let statutHtml = client.statut === 'actif' ? '<span class="text-success">Actif</span>' : '<span class="text-danger">Inactif</span>';
            voirModalBody.innerHTML = `<div class="row g-3"><div class="col-md-4 text-center"><img src="${client.image || 'https://via.placeholder.com/150'}" class="img-fluid rounded"/></div><div class="col-md-8"><p><strong>Nom:</strong> ${client.nom}</p><p><strong>Téléphone:</strong> ${client.tel}</p><p><strong>WhatsApp:</strong> ${client.whatsapp || '-'}</p><p><strong>Adresse:</strong> ${client.adresse || '-'}</p>${client.latitude && client.longitude ? `<p>Coordonnées: ${client.latitude}, ${client.longitude}</p><p><a href="https://www.google.com/maps?q=${client.latitude},${client.longitude}" target="_blank">Voir sur Google Maps</a></p>` : ''}<p>${statutHtml}</p><p>${client.description || ''}</p></div></div>`;
            voirModal.show();
        }).catch(err => { voirModalBody.innerHTML = '<p class="text-danger">Erreur</p>'; console.error(err); });
    }

    // Edit client
    function editClient(id) {
        editModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        fetch(`/clients/${id}/ajax-edit`).then(r => r.json()).then(client => {
            if (client.error) { editModalBody.innerHTML = `<p class="text-danger">${client.error}</p>`; return; }
            // simple markup
            editModalBody.innerHTML = `
                <form id="editClientForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Nom</label>
                            <input id="editNom" value="${client.nom}" class="form-control"/>
                            <label class="mt-2">Téléphone</label>
                            <input id="editTel" value="${client.tel}" class="form-control"/>
                        </div>
                        <div class="col-md-6">
                            <label>WhatsApp</label>
                            <input id="editWhatsapp" value="${client.whatsapp || ''}" class="form-control"/>
                            <label class="mt-2">Adresse</label>
                            <div class="input-group">
                                <input id="editAdresse" value="${client.adresse || ''}" class="form-control"/>
                                <button id="editDetectPositionBtn" class="btn btn-outline-secondary" type="button"><i class="fa fa-map-marker-alt"></i></button>
                            </div>
                            <input type="hidden" id="editLatitude" name="latitude" value="${client.latitude || ''}" />
                            <input type="hidden" id="editLongitude" name="longitude" value="${client.longitude || ''}" />
                            <input type="hidden" id="editTelE164" name="tel_e164" value="" />
                            <input type="hidden" id="editWhatsappE164" name="whatsapp_e164" value="" />
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" id="saveClientBtn" class="btn btn-success">Enregistrer</button>
                        </div>
                    </div>
                </form>`;

            // init intl on edit fields
            const elEditTel = document.getElementById('editTel');
            const elEditWhatsapp = document.getElementById('editWhatsapp');
            let itiEditTel = null, itiEditWhatsapp = null;
            if (window.intlTelInput) {
                if (elEditTel && !elEditTel.hasAttribute('data-iti-initialized')) { itiEditTel = window.intlTelInput(elEditTel, { separateDialCode: true, initialCountry: 'gn', utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js' }); elEditTel.setAttribute('data-iti-initialized', '1'); }
                if (elEditWhatsapp && !elEditWhatsapp.hasAttribute('data-iti-initialized')) { itiEditWhatsapp = window.intlTelInput(elEditWhatsapp, { separateDialCode: true, initialCountry: 'gn', utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js' }); elEditWhatsapp.setAttribute('data-iti-initialized', '1'); }
            }

            // Detect position button
            const editDetectBtn = document.getElementById('editDetectPositionBtn');
            if (editDetectBtn) {
                editDetectBtn.addEventListener('click', function () {
                    if (!navigator.geolocation) { alert('Géolocalisation non supportée'); return; }
                    editDetectBtn.disabled = true;
                    editDetectBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                    navigator.geolocation.getCurrentPosition(async function (pos) {
                        const lat = pos.coords.latitude; const lon = pos.coords.longitude;
                        document.getElementById('editLatitude').value = lat; document.getElementById('editLongitude').value = lon;
                        try { const r = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`); const data = await r.json(); if (data && data.display_name) document.getElementById('editAdresse').value = data.display_name; } catch (e) { console.warn(e); }
                        editDetectBtn.disabled = false; editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>';
                    }, function (err) { editDetectBtn.disabled = false; editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>'; alert('Impossible d\'obtenir la position'); }, { enableHighAccuracy: true, timeout: 10000 });
                });
            }

            // Save button
            document.getElementById('saveClientBtn').addEventListener('click', function () {
                const f = new FormData(); f.append('_method', 'PUT'); f.append('nom', document.getElementById('editNom').value || '');
                if (itiEditTel && itiEditTel.isValidNumber()) { f.append('tel', itiEditTel.getNumber()); f.append('tel_e164', itiEditTel.getNumber()); } else { f.append('tel', document.getElementById('editTel').value || ''); }
                if (itiEditWhatsapp && itiEditWhatsapp.isValidNumber()) { f.append('whatsapp', itiEditWhatsapp.getNumber()); f.append('whatsapp_e164', itiEditWhatsapp.getNumber()); } else { f.append('whatsapp', document.getElementById('editWhatsapp').value || ''); }
                f.append('adresse', document.getElementById('editAdresse').value || ''); f.append('latitude', document.getElementById('editLatitude').value || ''); f.append('longitude', document.getElementById('editLongitude').value || '');
                fetch(`/clients/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: f })
                    .then(r => r.json()).then(resp => { if (resp.success) { location.reload(); } else { alert(resp.message || 'Erreur'); } }).catch(e => { console.error(e); alert('Erreur'); });
            });

            editModal.show();
        }).catch(err => { editModalBody.innerHTML = '<p class="text-danger">Erreur</p>'; console.error(err); });
    }

    // Attach table buttons
    document.querySelectorAll('.btn-view').forEach(btn => btn.addEventListener('click', () => showClient(btn.dataset.id)));
    document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', () => editClient(btn.dataset.id)));
});
document.addEventListener('DOMContentLoaded', function () {
    // Modal references
    const voirModal = new bootstrap.Modal(document.getElementById('voirClientModal'));
    const voirModalBody = document.getElementById('voirClientContent');
    const editModal = new bootstrap.Modal(document.getElementById('editClientModal'));
    const editModalBody = document.getElementById('editClientContent');

    // Initialize intl-tel-input for add form (if present)
    let itiTel = null, itiWhatsapp = null;
    const telEl = document.getElementById('tel');
    const waEl = document.getElementById('whatsapp');
    if (telEl && window.intlTelInput) {
        if (!telEl.hasAttribute('data-iti-initialized')) {
            itiTel = window.intlTelInput(telEl, {
                separateDialCode: true,
                utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js',
                initialCountry: 'gn',
                preferredCountries: ['gn', 'sn', 'ci', 'ml'],
            });
            telEl.setAttribute('data-iti-initialized', '1');
        }
    }
    if (waEl && window.intlTelInput) {
        if (!waEl.hasAttribute('data-iti-initialized')) {
            itiWhatsapp = window.intlTelInput(waEl, {
                separateDialCode: true,
                utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js',
                initialCountry: 'gn',
                preferredCountries: ['gn', 'sn', 'ci', 'ml'],
            });
            waEl.setAttribute('data-iti-initialized', '1');
        }
    }

    // On add form submit, set E.164 values into hidden fields
    const formAdd = document.getElementById('formAjoutClient');
    if (formAdd) {
        formAdd.addEventListener('submit', function () {
            try {
                if (itiTel && itiTel.isValidNumber()) {
                    telEl.value = itiTel.getNumber();
                    const telHidden = document.getElementById('tel_e164');
                    if (telHidden) telHidden.value = itiTel.getNumber();
                }
                if (itiWhatsapp && itiWhatsapp.isValidNumber()) {
                    waEl.value = itiWhatsapp.getNumber();
                    const waHidden = document.getElementById('whatsapp_e164');
                    if (waHidden) waHidden.value = itiWhatsapp.getNumber();
                }
            } catch (err) { console.warn('intl-tel-input submit handler error', err); }
        });
    }

    // -------------------------
    // Voir client modal
    // -------------------------
    function showClient(id) {
        voirModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        fetch(`/clients/${id}/ajax-show`)
            .then(res => res.json())
            .then(client => {
                if (client.error) {
                    voirModalBody.innerHTML = `<p class="text-danger text-center">${client.error}</p>`;
                    return;
                }
                let statutHtml = client.statut === 'actif'
                    ? '<span class="fw-bold text-success">Actif</span>'
                    : '<span class="fw-bold text-danger">Inactif</span>';
                voirModalBody.innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-4 text-center">
                            <img src="${client.image ? client.image : 'https://via.placeholder.com/150'}" class="rounded w-100" style="object-fit:cover;">
                        </div>
                        <div class="col-md-8">
                            <p><strong>Nom :</strong> ${client.nom}</p>
                            <p><strong>Téléphone :</strong> ${client.tel}</p>
                            <p><strong>WhatsApp :</strong> ${client.whatsapp || '-'}</p>
                            <p><strong>Adresse :</strong> ${client.adresse || '-'}</p>
                            ${client.latitude && client.longitude ? `<p><strong>Coordonnées :</strong> ${client.latitude}, ${client.longitude}</p><p><a href="https://www.google.com/maps?q=${client.latitude},${client.longitude}" target="_blank" rel="noopener">Voir sur Google Maps</a></p>` : ''}
                            <p><strong>Statut :</strong> ${statutHtml}</p>
                            <p><strong>Description :</strong> ${client.description || '-'}</p>
                        </div>
                    </div>`;
                voirModal.show();
            })
            .catch(err => {
                voirModalBody.innerHTML = '<p class="text-danger text-center">Erreur lors du chargement du client.</p>';
                console.error(err);
            });
    }

    // -------------------------
    // Edit client modal
    // -------------------------
    function editClient(id) {
        editModalBody.innerHTML = '<p class="text-center">Chargement...</p>';
        fetch(`/clients/${id}/ajax-edit`)
            .then(res => res.json())
            .then(client => {
                if (client.error) {
                    editModalBody.innerHTML = `<p class="text-danger text-center">${client.error}</p>`;
                    return;
                }

                // Build modal form HTML
                editModalBody.innerHTML = `
                    <form id="editClientForm">
                        <div class="row g-3">
                            <div class="col-md-6 text-center">
                                <label class="form-label fw-bold">Photo</label>
                                <div id="imageContainer" style="width:130px;height:130px;background:#f8f9fa;border-radius:50%;overflow:hidden;cursor:pointer;margin:auto;position:relative;">
                                    <img id="currentImage" src="${client.image ? client.image : 'https://via.placeholder.com/130'}" class="w-100 h-100" style="object-fit:cover;">
                                    <input type="file" id="editImage" name="image" accept="image/*" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nom</label>
                                <input type="text" id="editNom" class="form-control" value="${client.nom}" required>
                                <label class="form-label fw-bold mt-2">Téléphone</label>
                                <input type="tel" id="editTel" class="form-control" value="${client.tel}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold mt-2">WhatsApp</label>
                                <input type="tel" id="editWhatsapp" class="form-control" value="${client.whatsapp || ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold mt-2">Adresse</label>
                                <div class="input-group">
                                    <input type="text" id="editAdresse" class="form-control" value="${client.adresse || ''}">
                                    <button id="editDetectPositionBtn" type="button" class="btn btn-outline-secondary" title="Détecter position"><i class="fa fa-map-marker-alt"></i></button>
                                </div>
                                <input type="hidden" id="editLatitude" name="latitude" value="${client.latitude || ''}">
                                <input type="hidden" id="editLongitude" name="longitude" value="${client.longitude || ''}">
                                <input type="hidden" id="editTelE164" name="tel_e164" value="">
                                <input type="hidden" id="editWhatsappE164" name="whatsapp_e164" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold mt-2">Statut</label>
                                <select id="editStatut" class="form-select">
                                    <option value="actif" ${client.statut === 'actif' ? 'selected' : ''}>Actif</option>
                                    <option value="inactif" ${client.statut === 'inactif' ? 'selected' : ''}>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold mt-2">Description</label>
                                <textarea id="editDescription" class="form-control">${client.description || ''}</textarea>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" id="saveClientBtn" class="btn btn-success">Enregistrer</button>
                        </div>
                    </form>`;

                // Image preview
                const inputImage = document.getElementById('editImage');
                const currentImage = document.getElementById('currentImage');
                if (inputImage) {
                    inputImage.addEventListener('change', e => {
                        if (e.target.files[0]) {
                            const reader = new FileReader();
                            reader.onload = ev => currentImage.src = ev.target.result;
                            reader.readAsDataURL(e.target.files[0]);
                        }
                    });
                }

                // Initialize intl-tel-input for edit fields (if not already)
                const elEditTel = document.getElementById('editTel');
                const elEditWhatsapp = document.getElementById('editWhatsapp');
                let itiEditTel = null;
                let itiEditWhatsapp = null;
                if (elEditTel && window.intlTelInput && !elEditTel.hasAttribute('data-iti-initialized')) {
                    itiEditTel = window.intlTelInput(elEditTel, {
                        separateDialCode: true,
                        utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js',
                        initialCountry: 'gn',
                        preferredCountries: ['gn', 'sn', 'ci', 'ml'],
                    });
                    elEditTel.setAttribute('data-iti-initialized', '1');
                }
                if (elEditWhatsapp && window.intlTelInput && !elEditWhatsapp.hasAttribute('data-iti-initialized')) {
                    itiEditWhatsapp = window.intlTelInput(elEditWhatsapp, {
                        separateDialCode: true,
                        utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js',
                        initialCountry: 'gn',
                        preferredCountries: ['gn', 'sn', 'ci', 'ml'],
                    });
                    elEditWhatsapp.setAttribute('data-iti-initialized', '1');
                }

                // Detect position in edit modal
                const editDetectBtn = document.getElementById('editDetectPositionBtn');
                if (editDetectBtn) {
                    editDetectBtn.addEventListener('click', function () {
                        if (!navigator.geolocation) {
                            alert('Géolocalisation non supportée par votre navigateur');
                            return;
                        }
                        editDetectBtn.disabled = true;
                        editDetectBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                        /* Removed duplicate event listener and code */
                                    <input type="text" id="editAdresse" class="form-control" value="${client.adresse || ''}">
                                    <button id="editDetectPositionBtn" type="button" class="btn btn-outline-secondary" title="Détecter position"><i class="fa fa-map-marker-alt"></i></button>
                                </div>
                                <input type="hidden" id="editLatitude" name="latitude" value="${client.latitude || ''}">
                                <input type="hidden" id="editLongitude" name="longitude" value="${client.longitude || ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold mt-2">Statut</label>
                                <select id="editStatut" class="form-select">
                                    <option value="actif" ${client.statut === 'actif' ? 'selected' : ''}>Actif</option>
                                    <option value="inactif" ${client.statut === 'inactif' ? 'selected' : ''}>Inactif</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold mt-2">Description</label>
                                <textarea id="editDescription" class="form-control">${client.description || ''}</textarea>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" id="saveClientBtn" class="btn btn-success">Enregistrer</button>
                        </div>
                    </form > `;

                // Prévisualisation image
                const inputImage = document.getElementById('editImage');
                const currentImage = document.getElementById('currentImage');
                inputImage.addEventListener('change', e => {
                    if (e.target.files[0]) {
                        const reader = new FileReader();
                        reader.onload = ev => currentImage.src = ev.target.result;
                        reader.readAsDataURL(e.target.files[0]);
                    }
                });

                // Sauvegarde
                document.getElementById('saveClientBtn').onclick = function () {
                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    formData.append('nom', document.getElementById('editNom').value);
                    formData.append('tel', document.getElementById('editTel').value);
                    formData.append('whatsapp', document.getElementById('editWhatsapp').value);
                    formData.append('adresse', document.getElementById('editAdresse').value);
                    formData.append('statut', document.getElementById('editStatut').value);
                    // include coords if present
                    const latVal = document.getElementById('editLatitude') ? document.getElementById('editLatitude').value : '';
                    const lonVal = document.getElementById('editLongitude') ? document.getElementById('editLongitude').value : '';
                    if (latVal) formData.append('latitude', latVal);
                    if (lonVal) formData.append('longitude', lonVal);
                    formData.append('description', document.getElementById('editDescription').value);
                    if (inputImage.files[0]) formData.append('image', inputImage.files[0]);

                    fetch(`/ clients / ${ id } `, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                        body: formData
                    })
                        .then(res => res.json())
                        .then(resp => {
                            if (resp.success) {
                                const row = document.getElementById('clientRow' + id);
                                if (row) {
                                    row.querySelector('td:nth-child(3)').textContent = document.getElementById('editNom').value;
                                    row.querySelector('td:nth-child(4)').textContent = document.getElementById('editTel').value;
                                    row.querySelector('td:nth-child(5)').textContent = document.getElementById('editWhatsapp').value;
                                    row.querySelector('td:nth-child(6)').textContent = document.getElementById('editAdresse').value;
                                    let statutHtml = document.getElementById('editStatut').value === 'actif'
                                        ? '<span class="fw-bold text-success">Actif</span>'
                                        : '<span class="fw-bold text-danger">Inactif</span>';
                                    row.querySelector('td:nth-child(7)').innerHTML = statutHtml;
                                    row.querySelector('td:nth-child(8)').textContent = document.getElementById('editDescription').value;
                                }
                                editModal.hide();
                                alert(resp.message || 'Client mis à jour avec succès.');
                            } else {
                                alert(resp.message || 'Erreur lors de la mise à jour');
                            }
                                        // === intl-tel-input initialization for 'add' form (static) ===
                                        let itiTel = null, itiWhatsapp = null;
                                        if (document.getElementById('tel')) {
                                            itiTel = window.intlTelInput(document.getElementById('tel'), {
                                                separateDialCode: true,
                                                utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js',
                                                initialCountry: 'gn',
                                            });
                                        }
                                        if (document.getElementById('whatsapp')) {
                                            itiWhatsapp = window.intlTelInput(document.getElementById('whatsapp'), {
                                                separateDialCode: true,
                                                utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js',
                                                initialCountry: 'gn',
                                            });
                                        }

                                        // On add form submit, set E.164 values
                                        const formAdd = document.getElementById('formAjoutClient');
                                        if (formAdd) {
                                            formAdd.addEventListener('submit', function (ev) {
                                                if (itiTel && itiTel.isValidNumber()) {
                                                    document.getElementById('tel').value = itiTel.getNumber();
                                                    document.getElementById('tel_e164').value = itiTel.getNumber();
                                                }
                                                if (itiWhatsapp && itiWhatsapp.isValidNumber()) {
                                                    document.getElementById('whatsapp').value = itiWhatsapp.getNumber();
                                                    document.getElementById('whatsapp_e164').value = itiWhatsapp.getNumber();
                                                }
                                                // allow submit to continue
                                            });
                                        }

                        .catch(err => {
                            console.error(err);
                            alert('Erreur lors de la mise à jour');
                        });
                };

                editModal.show();
            })
            .catch(err => {
                editModalBody.innerHTML = '<p class="text-danger text-center">Erreur lors du chargement du client.</p>';
                console.error(err);
            });
    }

    // =========================
    // Événements boutons Voir / Edit
    // =========================
    document.querySelectorAll('.btn-view').forEach(btn => btn.addEventListener('click', () => showClient(btn.dataset.id)));
    document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', () => editClient(btn.dataset.id)));

    // =========================
    // Recherche en direct
    // =========================
    const searchInput = document.querySelector('input[name="search"]');
    const tableRows = document.querySelectorAll('#clientTable tbody tr');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const val = this.value.toLowerCase();
            tableRows.forEach(row => {
                row.style.display = Array.from(row.querySelectorAll('td')).some(td => td.textContent.toLowerCase().includes(val)) ? '' : 'none';
            });
        });
    }
});

                // Hook detect btn for edit modal
                const editDetectBtn = document.getElementById('editDetectPositionBtn');
                if (editDetectBtn) {
                    editDetectBtn.addEventListener('click', function() {
                        if (!navigator.geolocation) {
                            alert('Géolocalisation non supportée par votre navigateur');
                            return;
                        }
                        editDetectBtn.disabled = true;
                        editDetectBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                        navigator.geolocation.getCurrentPosition(async function(pos) {
                            const lat = pos.coords.latitude;
                            const lon = pos.coords.longitude;
                            if (document.getElementById('editLatitude')) document.getElementById('editLatitude').value = lat;
                            if (document.getElementById('editLongitude')) document.getElementById('editLongitude').value = lon;
                            try {
                                                        <input type="tel" id="editTel" class="form-control" value="${client.tel}" required>
                                const d = await r.json();
                                if (d && d.display_name) document.getElementById('editAdresse').value = d.display_name;
                            } catch (err) { console.warn('Reverse geocode failed', err); }
                                                        <input type="tel" id="editWhatsapp" class="form-control" value="${client.whatsapp || ''}">
                            editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>';
                        }, function(err) {
                            editDetectBtn.disabled = false;
                            editDetectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>';
                            alert('Impossible d\'obtenir votre position: ' + (err.message || 'Erreur'));
                        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
                    });
                }
