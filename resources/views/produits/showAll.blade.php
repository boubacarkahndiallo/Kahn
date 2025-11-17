@extends('base')

@section('title', 'Nos Produits')

@section('content')
    <div class="all-title-box">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Nos Produits</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('app_accueil') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">Shopping</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">

        <!-- Titre -->
        <div class="mb-4 text-center fw-bold">
            <h1 class="fw-bold text-uppercase">Liste de nos produits</h1>
        </div>

        <!-- Description -->
        <div class="mb-4 text-center">
            <h3>Choisissez les produits que vous voulez commander.</h3>
        </div>

        <!-- Filtres et recherche améliorés -->
        <div class="row mb-4 g-3 align-items-center">
            <div class="col-md-4">
                <label for="categorie-filter" class="form-label fw-bold">Filtrer par catégorie :</label>
                <select id="categorie-filter" class="form-select">
                    <option value="">Toutes les catégories</option>
                    @foreach ($categories as $categorie)
                        <option value="{{ $categorie }}">{{ $categorie }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8">
                <label for="search-produit" class="form-label fw-bold">Recherche produit :</label>
                <input type="text" id="search-produit" class="form-control" placeholder="Tapez pour rechercher..."
                    autofocus>
            </div>
        </div>

        <!-- Tableau Produits -->
        <div class="table-responsive">
            <table class="table table-bordered" id="produits-table">
                <thead class="table-light">
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Produit</th>
                        <th>P.U</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produits as $produit)
                        <tr data-prix="{{ $produit->prix }}" data-categorie="{{ $produit->categorie }}">
                            <td><input type="checkbox" class="select-produit"></td>
                            <td>
                                {{ $produit->nom }}<br>
                                @if ($produit->image)
                                    <img src="{{ asset('storage/' . $produit->image) }}" width="60"
                                        alt="{{ $produit->nom }}">
                                @else
                                    <img src="https://via.placeholder.com/60" alt="Pas d'image">
                                @endif
                            </td>
                            <td>{{ number_format($produit->prix, 0, ',', ' ') }}</td>
                            <td>
                                <input type="number" class="form-control qty" min="1" value="1" disabled>
                            </td>
                            <td class="total">{{ number_format($produit->prix, 0, ',', ' ') }}</td>
                            <td>
                                <button class="btn btn-danger btn-annuler d-none" title="Annuler">&times;</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Résumé total et bouton Commander -->
        <div class="d-flex justify-content-end align-items-center mt-3 gap-3">
            <div class="fw-bold fs-5">
                Total sélectionné : <span id="grand-total">0</span> GNF
            </div>
            <button id="btn-commander" class="btn btn-primary btn-lg d-none">Commander</button>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/produit-auth.js') }}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const table = document.getElementById('produits-table');
                const grandTotal = document.getElementById('grand-total');
                const btnCommander = document.getElementById('btn-commander');

                // Cocher/Décocher tout
                document.getElementById('select-all').addEventListener('change', function() {
                    const checked = this.checked;
                    table.querySelectorAll('.select-produit').forEach(chk => {
                        chk.checked = checked;
                        chk.dispatchEvent(new Event('change'));
                    });
                });

                // Activer quantité et bouton annuler
                table.querySelectorAll('.select-produit').forEach(chk => {
                    chk.addEventListener('change', function() {
                        const tr = this.closest('tr');
                        const qtyInput = tr.querySelector('.qty');
                        const btnAnnuler = tr.querySelector('.btn-annuler');

                        qtyInput.disabled = !this.checked;

                        if (this.checked) {
                            btnAnnuler.classList.remove('d-none');
                        } else {
                            btnAnnuler.classList.add('d-none');
                            qtyInput.value = 1;
                        }

                        updateTotal(tr);
                        updateGrandTotal();
                        toggleCommanderButton();
                    });
                });

                // Calcul total ligne
                table.querySelectorAll('.qty').forEach(input => {
                    input.addEventListener('input', function() {
                        const tr = this.closest('tr');
                        updateTotal(tr);
                        updateGrandTotal();
                    });
                });

                function updateTotal(tr) {
                    const prix = parseFloat(tr.dataset.prix);
                    const qty = parseInt(tr.querySelector('.qty').value) || 0;
                    tr.querySelector('.total').textContent = new Intl.NumberFormat('fr-FR').format(prix * qty);
                }

                function updateGrandTotal() {
                    let total = 0;
                    table.querySelectorAll('tbody tr').forEach(tr => {
                        const chk = tr.querySelector('.select-produit');
                        if (chk.checked) {
                            const prix = parseFloat(tr.dataset.prix);
                            const qty = parseInt(tr.querySelector('.qty').value) || 0;
                            total += prix * qty;
                        }
                    });
                    grandTotal.textContent = new Intl.NumberFormat('fr-FR').format(total);
                }

                function toggleCommanderButton() {
                    const anyChecked = Array.from(table.querySelectorAll('.select-produit')).some(chk => chk.checked);
                    btnCommander.classList.toggle('d-none', !anyChecked);
                }

                // Bouton annuler
                table.querySelectorAll('.btn-annuler').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const tr = this.closest('tr');
                        const chk = tr.querySelector('.select-produit');
                        chk.checked = false;
                        tr.querySelector('.qty').value = 1;
                        tr.querySelector('.qty').disabled = true;
                        this.classList.add('d-none');
                        updateTotal(tr);
                        updateGrandTotal();
                        toggleCommanderButton();
                    });
                });

                // Filtre catégorie et recherche
                const filterCategorie = document.getElementById('categorie-filter');
                const searchInput = document.getElementById('search-produit');

                function applyFilters() {
                    const catVal = filterCategorie.value.toLowerCase();
                    const searchVal = searchInput.value.toLowerCase();

                    table.querySelectorAll('tbody tr').forEach(tr => {
                        const cat = tr.dataset.categorie.toLowerCase();
                        const nom = tr.querySelector('td:nth-child(2)').textContent.toLowerCase();

                        if ((catVal === "" || cat === catVal) && nom.includes(searchVal)) {
                            tr.classList.remove('hide-row');
                        } else {
                            tr.classList.add('hide-row');
                        }
                    });
                }

                filterCategorie.addEventListener('change', applyFilters);
                searchInput.addEventListener('input', applyFilters);

                // Bouton Commander
                btnCommander.addEventListener('click', function() {
                    const produits = [];
                    table.querySelectorAll('tbody tr').forEach(tr => {
                        const chk = tr.querySelector('.select-produit');
                        if (chk.checked) {
                            const nom = tr.querySelector('td:nth-child(2)').childNodes[0].textContent
                                .trim();
                            const qty = parseInt(tr.querySelector('.qty').value) || 1;
                            const prix = parseFloat(tr.dataset.prix);
                            const total = prix * qty;
                            produits.push({
                                nom,
                                qty,
                                prix,
                                total
                            });
                        }
                    });

                    if (produits.length === 0) return;

                    let message = "Vous avez passé une commande de :\n";
                    let totalGeneral = 0;
                    produits.forEach(p => {
                        message +=
                            `${p.nom} : ${p.qty} x ${new Intl.NumberFormat('fr-FR').format(p.prix)} GNF = ${new Intl.NumberFormat('fr-FR').format(p.total)} GNF\n`;
                        totalGeneral += p.total;
                    });
                    message +=
                        `Total à payer : ${new Intl.NumberFormat('fr-FR').format(totalGeneral)} GNF\nConfirmez-vous !`;

                    alert(message);
                });
            });
        </script>

        <style>
            /* Transition douce pour cacher/afficher les lignes */
            #produits-table tbody tr {
                transition: all 0.3s ease;
            }

            #produits-table tbody tr.hide-row {
                display: none;
            }

            /* Légère ombre sur hover pour dynamiser */
            #produits-table tbody tr:hover {
                background-color: #f8f9fa;
                transition: background-color 0.2s ease;
            }
        </style>
    @endpush
@endsection
