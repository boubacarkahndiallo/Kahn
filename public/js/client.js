document.addEventListener('DOMContentLoaded', function () {
    const voirModal = new bootstrap.Modal(document.getElementById('voirClientModal'));
    const voirModalBody = document.getElementById('voirClientContent');
    const editModal = new bootstrap.Modal(document.getElementById('editClientModal'));
    const editModalBody = document.getElementById('editClientContent');

    // =========================
    // Voir Client
    // =========================
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

    // =========================
    // Edit Client
    // =========================
    function editClient(id) {
        editModalBody.innerHTML = '<p class="text-center">Chargement...</p>';

        fetch(`/clients/${id}/ajax-edit`)
            .then(res => res.json())
            .then(client => {
                if (client.error) {
                    editModalBody.innerHTML = `<p class="text-danger text-center">${client.error}</p>`;
                    return;
                }

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
                                <input type="text" id="editTel" class="form-control" value="${client.tel}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold mt-2">WhatsApp</label>
                                <input type="text" id="editWhatsapp" class="form-control" value="${client.whatsapp || ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold mt-2">Adresse</label>
                                <input type="text" id="editAdresse" class="form-control" value="${client.adresse || ''}">
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
                    formData.append('description', document.getElementById('editDescription').value);
                    if (inputImage.files[0]) formData.append('image', inputImage.files[0]);

                    fetch(`/clients/${id}`, {
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
                        })
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
