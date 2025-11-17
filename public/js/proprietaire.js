document.addEventListener('DOMContentLoaded', function () {

    /**
     * MODAL VOIR PROPRIETAIRE
     */
    document.querySelectorAll('.btn-view-proprietaire').forEach(button => {
        button.addEventListener('click', function () {
            const proprietaireId = this.getAttribute('data-id');
            const modalBody = document.getElementById('voirProprietaireContent');
            modalBody.innerHTML = '<p class="text-center">Chargement...</p>';

            fetch(`/proprietaire/${proprietaireId}/ajax-show`)
                .then(response => response.json())
                .then(data => {
                    let html = `
                        <div class="row g-3">
                            <div class="col-md-4 text-center">
                                ${data.photo
                            ? `<img src="/storage/${data.photo}" class="rounded-circle w-100">`
                            : '<span class="text-muted">Pas de photo</span>'
                        }
                            </div>
                            <div class="col-md-8">
                                <p><strong>Nom complet:</strong> ${data.prenom} ${data.nom.toUpperCase()}</p>
                                <p><strong>Téléphone:</strong> ${data.telephone}</p>
                                <p><strong>Email:</strong> ${data.email || 'Non fourni'}</p>
                                <p><strong>Adresse:</strong> ${data.adresse || 'Non fourni'}</p>
                                <p><strong>Date de naissance:</strong> ${data.date_naissance || 'Non fourni'}</p>
                                <p><strong>Profession:</strong> ${data.profession || 'Non fourni'}</p>
                                <p><strong>Pièce d'identité:</strong> ${data.piece_identite
                            ? (/\.(jpg|jpeg|png|gif)$/i.test(data.piece_identite)
                                ? `<img src="/storage/${data.piece_identite}" class="img-fluid border rounded">`
                                : `<a href="/storage/${data.piece_identite}" target="_blank" class="btn btn-sm btn-outline-primary">Voir le fichier</a>`)
                            : 'Non fourni'
                        }</p>
                                <p><strong>Contrat:</strong> ${data.contrat_file
                            ? `<a href="/storage/${data.contrat_file}" target="_blank" class="btn btn-sm btn-outline-success">Voir le contrat</a>`
                            : 'Non fourni'
                        }</p>
                                <p><strong>Type de contrat:</strong> ${data.type_contrat || 'Non renseigné'}</p>
                                <p><strong>Date début:</strong> ${data.date_debut_contrat || 'Non renseignée'}</p>
                                <p><strong>Date fin:</strong> ${data.date_fin_contrat || 'Non renseignée'}</p>
                                <p><strong>Commission:</strong> ${data.commission ? data.commission + '%' : 'Non renseignée'}</p>
                            </div>
                        </div>`;
                    modalBody.innerHTML = html;
                })
                .catch(err => {
                    modalBody.innerHTML = `<p class="text-danger text-center">Erreur lors du chargement des données</p>`;
                    console.error(err);
                });
        });
    });

    /**
     * MODAL EDITER PROPRIETAIRE
     */
    document.querySelectorAll('.btn-edit-proprietaire').forEach(button => {
        button.addEventListener('click', function () {
            const proprietaireId = this.getAttribute('data-id');
            const modalContent = document.getElementById('editProprietaireContent');
            const form = document.getElementById('editProprietaireForm');

            modalContent.innerHTML = '<p class="text-center">Chargement...</p>';
            form.setAttribute('action', `/proprietaire/${proprietaireId}`);

            fetch(`/proprietaire/${proprietaireId}/ajax-edit`)
                .then(response => response.json())
                .then(data => {
                    let html = `
                        <div class="row g-3">
                            <div class="col-md-6 text-center">
                                <label class="form-label fw-bold">Photo du propriétaire</label>
                                <div id="photoContainer"
                                    style="width:130px; height:130px; background:#f8f9fa; border-radius:50%; overflow:hidden; cursor:pointer; position:relative; margin:auto;">
                                    <img id="currentPhoto"
                                        src="${data.photo ? '/storage/' + data.photo : 'https://via.placeholder.com/130'}"
                                        class="w-100 h-100"
                                        style="object-fit:cover;">
                                    <input type="file" id="editPhoto" name="photo"
                                        accept="image/*"
                                        style="position:absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="prenom" class="form-control" value="${data.prenom}" required>
                                <label class="form-label mt-2">Nom</label>
                                <input type="text" name="nom" class="form-control" value="${data.nom}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="telephone" class="form-control" value="${data.telephone}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="${data.email || ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Adresse</label>
                                <input type="text" name="adresse" class="form-control" value="${data.adresse || ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de naissance</label>
                                <input type="date" name="date_naissance" class="form-control" value="${data.date_naissance || ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profession</label>
                                <input type="text" name="profession" class="form-control" value="${data.profession || ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pièce d'identité</label>
                                <input type="file" name="piece_identite" class="form-control">
                                ${data.piece_identite ? `<a href="/storage/${data.piece_identite}" target="_blank" class="d-block mt-2">Voir le fichier actuel</a>` : ''}
                            </div>

                            <div class="col-12 mt-3">
                                <button class="btn btn-outline-warning w-100 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#editContratSection" aria-expanded="false" aria-controls="editContratSection">
                                    <i class="fa fa-file-contract me-2"></i> Informations du contrat
                                </button>
                                <div class="collapse mt-2" id="editContratSection">
                                    <div class="card card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Type de contrat</label>
                                                <select name="type_contrat" class="form-control">
                                                    <option value="">-- Sélectionner --</option>
                                                    <option value="Bail" ${data.type_contrat === 'Bail' ? 'selected' : ''}>Bail</option>
                                                    <option value="Mandat de gestion" ${data.type_contrat === 'Mandat de gestion' ? 'selected' : ''}>Mandat de gestion</option>
                                                    <option value="Autre" ${data.type_contrat === 'Autre' ? 'selected' : ''}>Autre</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Date début</label>
                                                <input type="date" name="date_debut_contrat" class="form-control" value="${data.date_debut_contrat || ''}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Date fin</label>
                                                <input type="date" name="date_fin_contrat" class="form-control" value="${data.date_fin_contrat || ''}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Commission (%)</label>
                                                <input type="number" name="commission" class="form-control" min="0" max="100" step="0.5" value="${data.commission || ''}">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold">Télécharger le contrat</label>
                                                <input type="file" name="contrat_file" class="form-control" accept=".pdf,.doc,.docx,.jpeg,.jpg,.png">
                                                ${data.contrat_file ? `<a href="/storage/${data.contrat_file}" target="_blank" class="d-block mt-2">Voir le contrat actuel</a>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    modalContent.innerHTML = html;

                    // Aperçu direct de la photo choisie
                    const inputPhoto = document.getElementById('editPhoto');
                    const currentPhoto = document.getElementById('currentPhoto');
                    if (inputPhoto) {
                        inputPhoto.addEventListener('change', function (event) {
                            if (event.target.files && event.target.files[0]) {
                                const reader = new FileReader();
                                reader.onload = function (e) {
                                    currentPhoto.src = e.target.result;
                                }
                                reader.readAsDataURL(event.target.files[0]);
                            }
                        });
                    }
                })
                .catch(err => {
                    modalContent.innerHTML = `<p class="text-danger text-center">Erreur lors du chargement des données</p>`;
                    console.error(err);
                });
        });
    });

    /**
     * RECHERCHE DANS TABLEAU PROPRIETAIRE
     */
    const searchInput = document.querySelector('input[name="search"]');
    const tableRows = document.querySelectorAll('table tbody tr');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();

            tableRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let match = false;

                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchValue)) {
                        match = true;
                    }
                });

                row.style.display = match ? '' : 'none';
            });
        });
    }
});
