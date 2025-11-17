// JS helper for admin user modals (cleaned)
document.addEventListener('DOMContentLoaded', () => {
    const get = id => document.getElementById(id);

    const voirModalEl = get('voirUserModal');
    const editModalEl = get('editUserModal');
    const ajoutModalEl = get('ajoutUserModal');

    const voirModal = voirModalEl ? new bootstrap.Modal(voirModalEl) : null;
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    const ajoutModal = ajoutModalEl ? new bootstrap.Modal(ajoutModalEl) : null;

    const voirModalBody = get('voirUserContent');
    const editModalContent = get('editUserContent');
    // cibler explicitement le tbody du tableau principal pour éviter d'insérer dans un mauvais tableau
    const tableBody = document.querySelector('.table-main table tbody');
    const ajoutForm = get('ajoutUserForm');

    function safeFetchJson(url, opts) {
        return fetch(url, opts).then(r => {
            if (!r.ok) return r.json().then(err => Promise.reject(err));
            return r.json();
        });
    }

    // Show user details in modal
    function showUser(id) {
        if (!voirModal || !voirModalBody) return;
        voirModalBody.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>';
        voirModal.show();

        safeFetchJson(`/utilisateurs/${id}/ajax-show`)
            .then(data => {
                // server may return a full url in data.photo or just a path; use it directly
                const photoHtml = data.photo ? `<img src="${data.photo}" class="rounded-circle img-fluid" style="max-width:150px">` : `<div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:150px;height:150px;margin:auto"><i class="fas fa-user fa-3x text-muted"></i></div>`;
                const whatsapp = data.whatsapp ? `<p class="mb-1"><i class="fab fa-whatsapp me-2"></i>${data.whatsapp}</p>` : '';
                const adresse = data.adresse ? `<p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>${data.adresse}</p>` : '';
                const roleClass = data.role === 'super_admin' ? 'text-danger' : data.role === 'admin' ? 'text-success' : 'text-warning';

                voirModalBody.innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-4 text-center">${photoHtml}</div>
                        <div class="col-md-8">
                            <h4>${data.prenom || ''} ${data.nom || ''}</h4>
                            <p class="mb-1"><i class="fas fa-phone-alt me-2"></i>${data.tel || ''}</p>
                            ${whatsapp}
                            <p class="mb-1"><i class="fas fa-envelope me-2"></i>${data.email || ''}</p>
                            ${adresse}
                            <p class="mb-1"><i class="fas fa-user-tag me-2"></i><span class="fw-bold ${roleClass}">${(data.role || '').toString().replace('_', ' ')}</span></p>
                        </div>
                    </div>`;
            })
            .catch(err => {
                console.error('showUser error', err);
                voirModalBody.innerHTML = `<div class="alert alert-danger">Erreur : ${err.message || JSON.stringify(err)}</div>`;
            });
    }

    // Edit user modal + submit
    function editUser(id) {
        if (!editModal || !editModalContent) return;
        editModalContent.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>';
        editModal.show();

        safeFetchJson(`/utilisateurs/${id}/ajax-edit`)
            .then(data => {
                // server may return a full url in data.photo or just a path; use it directly
                const photoSrc = data.photo ? data.photo : 'https://via.placeholder.com/130';

                editModalContent.innerHTML = `
                    <form id="editUserForm" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <div class="row g-3">

                            <!-- PHOTO (same layout as add form) -->
                            <div class="col-md-6 text-center">
                                <label class="form-label fw-bold">Photo de l'utilisateur</label>
                                <div id="imageContainerEdit" style="width:130px; height:130px; background:#f8f9fa; border-radius:50%; overflow:hidden; cursor:pointer; margin:auto; position:relative; display:flex; align-items:center; justify-content:center;">
                                    <img id="previewPhotoEdit" src="${photoSrc}" style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:${data.photo ? 'block' : 'none'};">
                                    <i class="fa fa-plus fa-2x text-muted" id="iconPlusEdit" style="display:${data.photo ? 'none' : 'block'}"></i>
                                    <input type="file" name="photo" id="photoEdit" accept="image/*" style="position:absolute; width:100%; height:100%; opacity:0; cursor:pointer;">
                                </div>
                            </div>

                            <!-- INFOS UTILISATEUR -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold mt-2">Prénom</label>
                                <input type="text" class="form-control" name="prenom" value="${data.prenom || ''}" required>
                                <label class="form-label fw-bold">Nom</label>
                                <input type="text" class="form-control" name="nom" value="${data.nom || ''}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Téléphone</label>
                                <input type="text" class="form-control" name="tel" value="${data.tel || ''}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">WhatsApp</label>
                                <input type="text" class="form-control" name="whatsapp" id="whatsappEdit" value="${data.whatsapp || ''}">
                                <small id="whatsappErrorEdit" class="text-danger d-none">Numéro WhatsApp invalide</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" name="email" value="${data.email || ''}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Adresse</label>
                                <input type="text" class="form-control" name="adresse" value="${data.adresse || ''}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Rôle</label>
                                <select class="form-select" name="role" required>
                                    <option value="super_admin" ${data.role === 'super_admin' ? 'selected' : ''}>Super Admin</option>
                                    <option value="admin" ${data.role === 'admin' ? 'selected' : ''}>Admin</option>
                                    <option value="user" ${data.role === 'user' ? 'selected' : ''}>Utilisateur</option>
                                </select>
                            </div>
                            <div class="col-12 text-end mt-3">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" id="saveEditBtn" class="btn btn-success">Enregistrer</button>
                            </div>
                        </div>
                    </form>`;

                // After rendering, adjust role options for non-super-admin users
                const roleSelect = editModalContent.querySelector('select[name="role"]');
                const currentUserRole = document.querySelector('meta[name="user-role"]')?.content || 'user';
                if (roleSelect && currentUserRole === 'admin') {
                    // Admin should not see or set 'super_admin' or 'admin' roles
                    const optSuper = roleSelect.querySelector('option[value="super_admin"]');
                    if (optSuper) optSuper.remove();
                    const optAdmin = roleSelect.querySelector('option[value="admin"]');
                    if (optAdmin) optAdmin.remove();

                    // If editing an existing admin/super_admin, lock the role select and show the current role as disabled
                    if (data.role === 'admin' || data.role === 'super_admin') {
                        roleSelect.disabled = true;
                        const opt = document.createElement('option');
                        opt.value = data.role;
                        opt.selected = true;
                        opt.disabled = true;
                        opt.textContent = data.role === 'super_admin' ? 'Super Admin (Non modifiable)' : 'Admin (Non modifiable)';
                        roleSelect.appendChild(opt);
                    }
                }

                // Photo preview behavior (same as add)
                const photoInput = document.getElementById('photoEdit');
                const preview = document.getElementById('previewPhotoEdit');
                const icon = document.getElementById('iconPlusEdit');

                photoInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (event) {
                            preview.src = event.target.result;
                            preview.style.display = 'block';
                            icon.style.display = 'none';
                        };
                        reader.readAsDataURL(file);
                    } else {
                        preview.style.display = 'none';
                        icon.style.display = 'block';
                        preview.src = data.photo || 'https://via.placeholder.com/130';
                    }
                });

                // WhatsApp validation (same as add)
                const whatsappInput = document.getElementById('whatsappEdit');
                const whatsappError = document.getElementById('whatsappErrorEdit');
                whatsappInput.addEventListener('input', () => {
                    const value = whatsappInput.value.trim();
                    const isValid = /^\+?\d{8,15}$/.test(value);
                    whatsappError.classList.toggle('d-none', isValid || value === '');
                });

                // submit
                const editForm = document.getElementById('editUserForm');
                editForm.addEventListener('submit', function (ev) {
                    ev.preventDefault();
                    // Nettoyer erreurs précédentes
                    this.querySelectorAll('.error-feedback').forEach(el => el.remove());
                    this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                    // Vérification client-side de la taille de la photo (19 Mo = 19456 KB)
                    const photoInputEdit = document.getElementById('photoEdit');
                    if (photoInputEdit && photoInputEdit.files && photoInputEdit.files[0]) {
                        const file = photoInputEdit.files[0];
                        const maxBytes = 19456 * 1024; // 19 Mo
                        if (file.size > maxBytes) {
                            photoInputEdit.classList.add('is-invalid');
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback error-feedback';
                            feedback.textContent = "La photo ne doit pas dépasser 19 Mo.";
                            photoInputEdit.parentNode.appendChild(feedback);
                            showAlert('danger', 'La photo sélectionnée dépasse 19 Mo.');
                            return; // stop submission
                        }
                    }

                    const fd = new FormData(this);
                    fd.append('_method', 'PUT');

                    safeFetchJson(`/utilisateurs/${id}`, { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                        .then(res => {
                            if (res.success) {
                                const row = document.getElementById('userRow' + id);
                                if (row) {
                                    // Update the row contents
                                    const photoCell = row.querySelector('td:nth-child(2)');
                                    const nomCell = row.querySelector('.user-nom');
                                    const telCell = row.querySelector('.user-tel');
                                    const whatsappCell = row.querySelector('.user-whatsapp');
                                    const emailCell = row.querySelector('.user-email');
                                    const adresseCell = row.querySelector('.user-adresse');
                                    const roleCell = row.querySelector('.user-role');

                                    if (photoCell) {
                                        if (res.user.photo) {
                                            photoCell.innerHTML = `<img src="${res.user.photo}" width="60" height="60" class="rounded">`;
                                        } else {
                                            photoCell.innerHTML = `<span class="text-muted">Aucune</span>`;
                                        }
                                    }

                                    if (nomCell) nomCell.textContent = `${res.user.prenom} ${res.user.nom}`;
                                    if (telCell) telCell.textContent = res.user.tel || '';
                                    if (whatsappCell) whatsappCell.textContent = res.user.whatsapp || '';
                                    if (emailCell) emailCell.textContent = res.user.email || '';
                                    if (adresseCell) adresseCell.textContent = res.user.adresse || '';

                                    if (roleCell) {
                                        let roleHtml = '';
                                        if (res.user.role === 'super_admin') {
                                            roleHtml = '<span class="fw-bold text-danger">Super Admin</span>';
                                        } else if (res.user.role === 'admin') {
                                            roleHtml = '<span class="fw-bold text-success">Admin</span>';
                                        } else {
                                            roleHtml = '<span class="fw-bold text-warning">Utilisateur</span>';
                                        }
                                        roleCell.innerHTML = roleHtml;
                                    }

                                    // Highlight updated row
                                    row.style.transition = 'background-color 0.3s';
                                    row.style.backgroundColor = '#e8f5e9';
                                    setTimeout(() => {
                                        row.style.backgroundColor = '';
                                    }, 1000);
                                }
                                editModal.hide();
                                showAlert('success', 'Utilisateur mis à jour');
                            } else {
                                showAlert('danger', res.message || 'Erreur');
                            }
                        })
                        .catch(err => {
                            console.error('edit submit', err);
                            // If validation errors, display under fields in the edit form
                            if (err && err.errors) {
                                Object.entries(err.errors).forEach(([field, messages]) => {
                                    const input = editForm.querySelector(`[name="${field}"]`);
                                    if (input) {
                                        input.classList.add('is-invalid');
                                        const feedback = document.createElement('div');
                                        feedback.className = 'invalid-feedback error-feedback';
                                        feedback.textContent = messages[0];
                                        input.parentNode.appendChild(feedback);
                                    }
                                });
                                const msgs = Object.values(err.errors).map(v => v[0]).join('<br>');
                                showAlert('danger', msgs);
                                return;
                            }

                            showAlert('danger', err.message || JSON.stringify(err));
                        });
                });
            })
            .catch(err => {
                console.error('editUser', err);
                editModalContent.innerHTML = `<div class="alert alert-danger">Erreur: ${err.message || JSON.stringify(err)}</div>`;
            });
    }

    // Delete user
    function deleteUser(id) {
        if (!confirm('Voulez-vous vraiment supprimer cet utilisateur ?')) return;
        safeFetchJson(`/utilisateurs/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
            .then(res => {
                if (res.success) {
                    const row = document.getElementById('userRow' + id);
                    if (row) row.remove();
                    showAlert('success', 'Utilisateur supprimé');
                } else showAlert('danger', res.message || 'Erreur');
            })
            .catch(err => {
                console.error('deleteUser', err);
                showAlert('danger', err.message || JSON.stringify(err));
            });
    }

    // Update row in table with animation
    function updateUserInTable(id, user) {
        const row = document.getElementById('userRow' + id);
        if (!row) return;

        // Prépare l'animation
        row.style.transition = 'background-color 0.3s';
        row.style.backgroundColor = '#e8f5e9';

        // Met à jour les cellules
        const photoCell = row.querySelector('td:nth-child(2)');
        const nomCell = row.querySelector('.user-nom');
        const telCell = row.querySelector('.user-tel');
        const whatsappCell = row.querySelector('.user-whatsapp');
        const emailCell = row.querySelector('.user-email');
        const adresseCell = row.querySelector('.user-adresse');
        const roleCell = row.querySelector('.user-role');

        if (photoCell) {
            if (user.photo) photoCell.innerHTML = `<img src="${user.photo}" width="60" height="60" class="rounded">`;
            else photoCell.innerHTML = '<span class="text-muted">Aucune</span>';
        }
        if (nomCell) nomCell.textContent = `${user.prenom || ''} ${user.nom || ''}`;
        if (telCell) telCell.textContent = user.tel || '';
        if (whatsappCell) whatsappCell.textContent = user.whatsapp || '';
        if (emailCell) emailCell.textContent = user.email || '';
        if (adresseCell) adresseCell.textContent = user.adresse || '';
        if (roleCell) roleCell.innerHTML = user.role === 'super_admin' ? '<span class="fw-bold text-danger">Super Admin</span>' : user.role === 'admin' ? '<span class="fw-bold text-success">Admin</span>' : '<span class="fw-bold text-warning">Utilisateur</span>';

        // Animation de retour à la normale
        setTimeout(() => {
            row.style.backgroundColor = '';
            setTimeout(() => row.style.transition = '', 300);
        }, 1000);
    }

    // Add new user row with animation
    function addUserToTable(user) {
        // Mettre à jour tous les numéros existants (décaler de +1)
        const existingRows = Array.from(tableBody.querySelectorAll('tr')).filter(r => !r.classList.contains('no-data'));
        existingRows.forEach((row, index) => {
            const numCell = row.querySelector('td:first-child');
            if (numCell) numCell.textContent = index + 2;
        });

        // Construire une ligne conforme à la structure du tableau (7 colonnes)
        const tr = document.createElement('tr');
        tr.id = 'userRow' + user.id;
        tr.style.opacity = '0';
        tr.style.transform = 'translateY(-20px)';
        tr.style.transition = 'all 0.3s ease-out';

        const photoHtml = user.photo ? `<img src="${user.photo}" width="55" height="55" class="rounded-circle border" style="border: 2px solid #1c911e; object-fit: cover;">` : `<div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 55px; height: 55px; background: #f0f0f0; border: 2px solid #ddd;"><i class="fa fa-user text-muted"></i></div>`;

        tr.innerHTML = `
            <td class="text-center fw-bold" style="color: #1c911e;">1</td>
            <td class="text-center">${photoHtml}</td>
            <td>
                <strong class="user-nom">${user.prenom || ''} ${user.nom || ''}</strong>
                <br>
                <small class="text-muted user-adresse">${user.adresse || '-'}</small>
            </td>
            <td class="user-tel"><i class="fas fa-phone text-success me-2"></i>${user.tel || ''}</td>
            <td class="user-email"><i class="fas fa-envelope text-info me-2"></i><small>${user.email || ''}</small></td>
            <td class="text-center user-role">${user.role === 'super_admin' ? '<span class="badge bg-danger" style="font-size: 12px;"><i class="fa fa-crown me-1"></i> Super Admin</span>' : user.role === 'admin' ? '<span class="badge bg-success" style="font-size: 12px;"><i class="fa fa-star me-1"></i> Admin</span>' : '<span class="badge bg-warning" style="font-size: 12px;"><i class="fa fa-user me-1"></i> Utilisateur</span>'}</td>
            <td class="text-center">
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-dark btn-view" data-id="${user.id}" title="Voir les détails"><i class="fa fa-eye"></i></button>
                    <button class="btn btn-outline-success btn-edit" data-id="${user.id}" title="Modifier"><i class="fa fa-edit"></i></button>
                    <button class="btn btn-outline-danger btn-delete" data-id="${user.id}" title="Supprimer"><i class="fa fa-trash"></i></button>
                </div>
            </td>`;

        // Insère la ligne en haut du tableau
        tableBody.insertBefore(tr, tableBody.firstChild);

        // Animation d'entrée
        requestAnimationFrame(() => {
            tr.style.opacity = '1';
            tr.style.transform = 'translateY(0)';
            tr.style.backgroundColor = '#e8f5e9';

            // Retour à la normale après l'animation
            setTimeout(() => {
                tr.style.backgroundColor = '';
                tr.style.transition = 'background-color 0.3s';
            }, 1000);
        });

        // Attachement des événements
        tr.querySelector('.btn-view').addEventListener('click', () => showUser(user.id));
        tr.querySelector('.btn-edit').addEventListener('click', () => editUser(user.id));
        tr.querySelector('.btn-delete').addEventListener('click', () => deleteUser(user.id));
    }

    // Submit add form
    if (ajoutForm) {
        ajoutForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Supprimer les messages d'erreur existants
            this.querySelectorAll('.error-feedback').forEach(el => el.remove());
            this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            // Vérifier s'il y a une ligne "Aucun utilisateur trouvé"
            const noDataRow = tableBody.querySelector('tr td[colspan="9"]');
            if (noDataRow) {
                noDataRow.parentElement.remove();
            }

            // Vérification client-side de la taille de la photo (19 Mo = 19456 KB)
            const photoInputAjout = document.getElementById('photoAjout');
            if (photoInputAjout && photoInputAjout.files && photoInputAjout.files[0]) {
                const file = photoInputAjout.files[0];
                const maxBytes = 19456 * 1024; // 19 Mo
                if (file.size > maxBytes) {
                    photoInputAjout.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback error-feedback';
                    feedback.textContent = "La photo ne doit pas dépasser 19 Mo.";
                    photoInputAjout.parentNode.appendChild(feedback);
                    showAlert('danger', 'La photo sélectionnée dépasse 19 Mo.');
                    return; // stop submission
                }
            }

            const fd = new FormData(this);
            safeFetchJson('/utilisateurs', { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(res => {
                    if (res.success) {
                        addUserToTable(res.user);
                        this.reset();
                        const preview = document.getElementById('previewPhotoAjout');
                        const icon = document.getElementById('iconPlusAjout');
                        if (preview) preview.style.display = 'none';
                        if (icon) icon.style.display = 'block';
                        if (ajoutModal) ajoutModal.hide();
                        showAlert('success', 'Utilisateur ajouté');
                    } else if (res.errors) {
                        // Afficher les erreurs sous chaque champ
                        Object.entries(res.errors).forEach(([field, messages]) => {
                            const input = this.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback error-feedback';
                                feedback.textContent = messages[0];
                                input.parentNode.appendChild(feedback);
                            }
                        });
                        // Afficher également une alerte générale
                        const msgs = Object.values(res.errors).map(v => v[0]).join('<br>');
                        showAlert('danger', msgs);
                    } else showAlert('danger', res.message || 'Erreur');
                })
                .catch(err => {
                    console.error('add user', err);
                    // Si erreurs de validation (422), afficher sous chaque champ
                    if (err && err.errors) {
                        Object.entries(err.errors).forEach(([field, messages]) => {
                            const input = ajoutForm.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback error-feedback';
                                feedback.textContent = messages[0];
                                input.parentNode.appendChild(feedback);
                            }
                        });
                        // Alerte générale non bloquante
                        const msgs = Object.values(err.errors).map(v => v[0]).join('<br>');
                        showAlert('danger', msgs);
                        return;
                    }

                    // Autres erreurs
                    showAlert('danger', err.message || JSON.stringify(err));
                });
        });
    }

    // Attach to existing buttons on page load
    document.querySelectorAll('.btn-view').forEach(b => b.addEventListener('click', () => showUser(b.dataset.id)));
    document.querySelectorAll('.btn-edit').forEach(b => b.addEventListener('click', () => editUser(b.dataset.id)));
    document.querySelectorAll('.btn-delete').forEach(b => b.addEventListener('click', () => deleteUser(b.dataset.id)));

    // Simple alert helper
    function showAlert(type, message) {
        const container = document.querySelector('.container') || document.body;
        const div = document.createElement('div');
        div.className = `alert alert-${type} alert-dismissible`;
        div.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        container.prepend(div);
        setTimeout(() => div.remove(), 4000);
    }
});
