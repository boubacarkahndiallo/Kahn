document.addEventListener('DOMContentLoaded', function () {
    const voirModal = new bootstrap.Modal(document.getElementById('voirProduitModal'));
    const voirModalBody = document.getElementById('voirProduitContent');
    const editModal = new bootstrap.Modal(document.getElementById('editProduitModal'));
    const editModalContent = document.getElementById('editProduitContent');

    // Fonction pour afficher le modal "Voir Produit"
    function showProduit(id) {
        voirModalBody.innerHTML = '<p class="text-center">Chargement...</p>';

        fetch(`/produits/${id}/ajax-show`)
            .then(res => res.json())
            .then(data => {
                const statutHtml = data.statut === 'Disponible'
                    ? `<span class="text-success">Disponible</span>`
                    : `<span class="text-danger">Rupture</span>`;

                voirModalBody.innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-4 text-center">
                            ${data.image ? `<img src="/storage/${data.image}" class="rounded w-100">` : '<span class="text-muted">Pas d’image</span>'}
                        </div>
                        <div class="col-md-8">
                            <p><strong>Nom:</strong> ${data.nom}</p>
                            <p><strong>Catégorie:</strong> ${data.categorie}</p>
                            <p><strong>Prix:</strong> ${data.prix}</p>
                            <p><strong>Stock:</strong> ${data.stock}</p>
                            <p><strong>Statut:</strong> ${statutHtml}</p>
                            <p><strong>Description:</strong> ${data.description || 'Non fourni'}</p>
                        </div>
                    </div>`;
                voirModal.show();
            })
            .catch(err => {
                voirModalBody.innerHTML = `<p class="text-danger text-center">Erreur lors du chargement des données</p>`;
                console.error(err);
            });
    }

    // Fonction pour afficher le modal "Éditer Produit"
    function editProduit(id) {
        editModalContent.innerHTML = '<p class="text-center">Chargement...</p>';

        fetch(`/produits/${id}/ajax-edit`)
            .then(res => res.json())
            .then(data => {
                editModalContent.innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-6 text-center">
                            <label class="form-label fw-bold">Image du produit</label>
                            <div id="imageContainer" style="width:130px; height:130px; background:#f8f9fa; border-radius:50%; overflow:hidden; cursor:pointer; margin:auto; position:relative;">
                                <img id="currentImage" src="${data.image ? '/storage/' + data.image : 'https://via.placeholder.com/130'}" class="w-100 h-100" style="object-fit:cover;">
                                <input type="file" id="editImage" name="image" accept="image/*" style="position:absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer;">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nom</label>
                            <input type="text" id="editNom" class="form-control" value="${data.nom}" required>
                            <label class="form-label fw-bold mt-2">Catégorie</label>
                            <select id="editCategorie" class="form-select" required>
                                <option value="Fruit" ${data.categorie === 'Fruit' ? 'selected' : ''}>Fruit</option>
                                <option value="Légumes" ${data.categorie === 'Légumes' ? 'selected' : ''}>Légumes</option>
                                <option value="Autres" ${data.categorie === 'Autres' ? 'selected' : ''}>Autres</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Prix</label>
                            <input type="number" id="editPrix" class="form-control" value="${data.prix}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Stock</label>
                            <input type="number" id="editStock" class="form-control" value="${data.stock}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea id="editDescription" class="form-control">${data.description || ''}</textarea>
                        </div>

                        <div class="col-12 text-end mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" id="saveProduitBtn" class="btn btn-success">Enregistrer</button>
                        </div>
                    </div>`;

                const inputImage = document.getElementById('editImage');
                const currentImage = document.getElementById('currentImage');

                // Prévisualisation de l'image
                inputImage.addEventListener('change', e => {
                    if (e.target.files[0]) {
                        const reader = new FileReader();
                        reader.onload = ev => currentImage.src = ev.target.result;
                        reader.readAsDataURL(e.target.files[0]);
                    }
                });

                // Gestion du bouton Enregistrer
                document.getElementById('saveProduitBtn').addEventListener('click', function () {
                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    formData.append('nom', document.getElementById('editNom').value);
                    formData.append('categorie', document.getElementById('editCategorie').value);
                    formData.append('prix', document.getElementById('editPrix').value);
                    formData.append('stock', document.getElementById('editStock').value);
                    formData.append('description', document.getElementById('editDescription').value);
                    if (inputImage.files[0]) formData.append('image', inputImage.files[0]);

                    fetch(`/produits/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                        .then(res => res.json())
                        .then(resp => {
                            if (resp.success) {
                                const row = document.getElementById('produitRow' + id);
                                if (row) {
                                    row.querySelector('td:nth-child(3)').textContent = document.getElementById('editNom').value;
                                    row.querySelector('td:nth-child(4)').textContent = document.getElementById('editCategorie').value;
                                    row.querySelector('td:nth-child(5)').textContent = Number(document.getElementById('editPrix').value).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' GNF';
                                    row.querySelector('td:nth-child(6)').textContent = document.getElementById('editStock').value;
                                    row.querySelector('td:nth-child(7)').innerHTML = document.getElementById('editStock').value > 0
                                        ? `<span class="fw-bold text-success">Disponible</span>`
                                        : `<span class="fw-bold text-danger">Rupture</span>`;
                                }
                                editModal.hide();
                                alert(resp.message);
                            } else {
                                alert(resp.message || 'Erreur lors de la mise à jour');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Erreur lors de la mise à jour');
                        });
                });

                editModal.show();
            })
            .catch(err => {
                editModalContent.innerHTML = `<p class="text-danger text-center">Erreur lors du chargement des données</p>`;
                console.error(err);
            });
    }

    // Événements sur boutons
    document.querySelectorAll('.btn-view').forEach(btn => btn.addEventListener('click', () => showProduit(btn.dataset.id)));
    document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', () => editProduit(btn.dataset.id)));
});
document.addEventListener('DOMContentLoaded', function () {
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

