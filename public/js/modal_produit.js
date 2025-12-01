document.addEventListener('DOMContentLoaded', function () {
    // =========================
    // Déclarations générales
    // =========================
    const voirModal = new bootstrap.Modal(document.getElementById('voirProduitModal'));
    const voirModalBody = document.getElementById('voirProduitContent');
    const editModal = new bootstrap.Modal(document.getElementById('editProduitModal'));
    const editModalContent = document.getElementById('editProduitContent');

    let panier = [];
    let produitEnCours = null;

    // =========================
    // Afficher le modal "Voir Produit"
    // =========================
    function showProduit(id) {
        voirModalBody.innerHTML = '<p class="text-center">Chargement...</p>';

        fetch(`/produits/${id}/ajax-show`)
            .then(res => res.json())
            .then(data => {
                let statutHtml = '';
                if (data.statut === 'Disponible') {
                    statutHtml = `<span class="fw-bold text-success">Disponible</span>`;
                } else if (data.statut === 'Rupture') {
                    statutHtml = `<span class="fw-bold text-danger">Rupture</span>`;
                } else if (data.statut === 'Presque Fini') {
                    statutHtml = `<span class="fw-bold text-warning">Presque Fini</span>`;
                } else {
                    statutHtml = `<span class="fw-bold text-muted">${data.statut}</span>`;
                }

                const imageSrc = data.image || '/images/logo1.png';
                voirModalBody.innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-4 text-center">
                            <img src="${imageSrc}" class="rounded w-100" onerror="this.onerror=null;this.src='/images/logo1.png'">
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

    // =========================
    // Afficher le modal "Éditer Produit"
    // =========================
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
                                <img id="currentImage" src="${data.image || '/images/logo1.png'}" class="w-100 h-100" style="object-fit:cover;" onerror="this.onerror=null;this.src='/images/logo1.png'">
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

                // Prévisualisation image
                inputImage.addEventListener('change', e => {
                    if (e.target.files[0]) {
                        const reader = new FileReader();
                        reader.onload = ev => currentImage.src = ev.target.result;
                        reader.readAsDataURL(e.target.files[0]);
                    }
                });

                // Enregistrement AJAX
                document.getElementById('saveProduitBtn').onclick = function () {
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

                                    let newStock = parseInt(document.getElementById('editStock').value);
                                    let newStatut = '';
                                    if (newStock >= 0 && newStock <= 5) {
                                        newStatut = `<span class="fw-bold text-danger">Rupture</span>`;
                                    } else if (newStock > 5 && newStock <= 10) {
                                        newStatut = `<span class="fw-bold text-warning">Presque Fini</span>`;
                                    } else {
                                        newStatut = `<span class="fw-bold text-success">Disponible</span>`;
                                    }
                                    row.querySelector('td:nth-child(7)').innerHTML = newStatut;
                                }
                                // If back-end returned an updated product object, update the displayed image too
                                if (resp.produit) {
                                    const updated = resp.produit;
                                    if (row) {
                                        try {
                                            const imgEl = row.querySelector('td:nth-child(2) img');
                                            if (imgEl && updated.image) imgEl.src = updated.image;
                                        } catch (e) { }
                                    }
                                    // Also update any grid card images
                                    try {
                                        const gridCardImg = document.querySelector('[data-produit-id="' + id + '"] img.card-img-top');
                                        if (gridCardImg && updated.image) gridCardImg.src = updated.image;
                                    } catch (e) { }
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
                };

                editModal.show();
            })
            .catch(err => {
                editModalContent.innerHTML = `<p class="text-danger text-center">Erreur lors du chargement des données</p>`;
                console.error(err);
            });
    }

    // =========================
    // Événements boutons Voir / Éditer
    // =========================
    document.querySelectorAll('.btn-view').forEach(btn => btn.addEventListener('click', () => showProduit(btn.dataset.id)));
    document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', () => editProduit(btn.dataset.id)));

    // =========================
    // Recherche en direct
    // =========================
    const searchInput = document.querySelector('input[name="search"]');
    const tableRows = document.querySelectorAll('table tbody tr');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            tableRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let match = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(searchValue));
                row.style.display = match ? '' : 'none';
            });
        });
    }

    // =========================
    // Gestion Panier
    // =========================
    document.querySelectorAll(".btn-ajout-panier").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();

            produitEnCours = {
                id: this.dataset.id,
                nom: this.dataset.nom,
                prix: parseFloat(this.dataset.prix),
                image: this.dataset.image
            };

            document.getElementById("modal-produit-image").src = produitEnCours.image;
            document.getElementById("modal-produit-nom").innerText = produitEnCours.nom;
            document.getElementById("modal-produit-prix").innerText = produitEnCours.prix.toLocaleString();
            document.getElementById("modal-quantite").value = 1;
            document.getElementById("modal-total").innerText = produitEnCours.prix.toLocaleString();

            new bootstrap.Modal(document.getElementById("cartModal")).show();
        });
    });

    document.getElementById("modal-quantite").addEventListener("input", function () {
        let quantite = parseInt(this.value) || 1;
        document.getElementById("modal-total").innerText = (produitEnCours.prix * quantite).toLocaleString();
    });

    document.getElementById("btn-valider-commande").addEventListener("click", function () {
        let quantite = parseInt(document.getElementById("modal-quantite").value) || 1;

        let existant = panier.find(p => p.id === produitEnCours.id);
        if (existant) {
            existant.quantite += quantite;
        } else {
            panier.push({ ...produitEnCours, quantite: quantite });
        }

        majPanier();
        bootstrap.Modal.getInstance(document.getElementById("cartModal")).hide();
    });

    function majPanier() {
        let tbody = document.querySelector("#panier-table tbody");
        tbody.innerHTML = "";
        let totalGeneral = 0;

        panier.forEach((p, index) => {
            let total = p.prix * p.quantite;
            totalGeneral += total;

            let row = `
                <tr>
                    <td>${p.nom}</td>
                    <td>${p.quantite}</td>
                    <td>${p.prix.toLocaleString()} GNF</td>
                    <td>${total.toLocaleString()} GNF</td>
                    <td><button class="btn btn-danger btn-sm" data-index="${index}">X</button></td>
                </tr>`;
            tbody.innerHTML += row;
        });

        document.getElementById("panier-total").innerText = totalGeneral.toLocaleString() + " GNF";

        document.querySelectorAll("#panier-table .btn-danger").forEach(btn => {
            btn.addEventListener("click", function () {
                panier.splice(this.dataset.index, 1);
                majPanier();
            });
        });
    }
});
