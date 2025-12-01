@extends('base')

@section('title', 'Produits')

@section('content')
    <div class="all-title-box">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Gestion des Produits</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('app_accueil') }}" style="color:#070a23;">Accueil</a>
                        </li>
                        <li class="breadcrumb-item active" style="color:white;">Produits</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Header + Boutons -->
    <div class="container mt-4">
        <style>
            .product-thumbnail {
                width: 60px;
                height: 60px;
                object-fit: cover;
            }

            /* Grid card tweaks */
            .product-grid .card {
                transition: transform .15s ease;
            }

            .product-grid .card:hover {
                transform: translateY(-6px);
            }

            .toggle-area .btn.active {
                background: #1c911e !important;
                color: #fff !important;
            }

            #btnGridView.btn.active,
            #btnListView.btn.active {
                background: #1c911e !important;
                color: #fff !important;
            }
        </style>
        <div class="row align-items-center mb-4 p-3 rounded shadow-sm"
            style="background:white; border-left:5px solid #1c911e;">
            <div class="col-md-4 d-flex align-items-center gap-2">
                <i class="fa fa-box fa-2x" style="color:#070a23;"></i>
                <h2 class="mb-0 fw-bold" style="color:#070a23;">Liste des Produits</h2>
                <small class="text-muted ms-2">{{ $produits->count() }} produits</small>
            </div>
            <div class="col-md-4">
                <form method="GET" action="{{ route('produits.index') }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher..."
                            value="{{ request('search') }}">
                        <button class="btn" type="submit" style="background:#1c911e; color:white;">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
                @php
                    // Ensure categories are available even on filtered results
                    $categories = $produits->pluck('categorie')->filter()->unique()->sort()->values();
                @endphp
                <form method="GET" action="{{ route('produits.index') }}" class="mt-2">
                    <div class="input-group">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="categorie" class="form-select">
                            <option value="">Toutes les catégories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" @if (request('categorie') == $cat) selected @endif>
                                    {{ $cat }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-secondary" type="submit">Filtrer</button>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end d-flex justify-content-end align-items-center gap-2">
                <button type="button" class="btn fw-bold shadow-sm" style="background:#1c911e; color:white;"
                    data-bs-toggle="modal" data-bs-target="#ajoutProduitModal">
                    <i class="fa fa-plus me-1"></i> Ajouter un produit
                </button>
                <div class="btn-group ms-2" role="group" aria-label="View toggle">
                    <button id="btnListView" class="btn btn-sm btn-outline-secondary" title="Liste"><i
                            class="fa fa-list"></i></button>
                    <button id="btnGridView" class="btn btn-sm btn-outline-secondary" title="Grille"><i
                            class="fa fa-th"></i></button>
                </div>
            </div>
        </div>
        {{-- Flash messages like client index --}}
        @if (session('success'))
            <div class="row mt-2">
                <div class="col-12">
                    <div class="alert alert-success">{{ session('success') }}</div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Ajout Produit -->
    <div class="modal fade" id="ajoutProduitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background:#1c911e;">
                    <h5 class="modal-title text-white"><i class="fa fa-box-open me-2"></i> Ajouter un produit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('produits.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6 text-center">
                                <label for="photo" class="form-label fw-bold">Image du produit</label>
                                <div class="d-flex justify-content-center align-items-center"
                                    style="width:130px; height:130px; background:#f8f9fa; cursor:pointer; position:relative; border-radius:50%; overflow:hidden;"
                                    onclick="document.getElementById('photo').click()">
                                    <i class="fa fa-plus fa-2x text-muted" id="iconPlus"></i>
                                    <img id="previewPhoto" src="#" alt="Aperçu photo"
                                        style="display:none; width:100%; height:100%; object-fit:cover; border-radius:50%;">
                                </div>
                                <input type="file" name="image" id="photo" class="form-control d-none"
                                    accept="image/*" onchange="previewImage(event)">
                                <script>
                                    function previewImage(event) {
                                        const input = event.target;
                                        const preview = document.getElementById('previewPhoto');
                                        const iconPlus = document.getElementById('iconPlus');
                                        if (input.files && input.files[0]) {
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                preview.src = e.target.result;
                                                preview.style.display = 'block';
                                                iconPlus.style.display = 'none';
                                            }
                                            reader.readAsDataURL(input.files[0]);
                                        }
                                    }
                                </script>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="nom">Nom du produit</label>
                                <input type="text" class="form-control" id="nom" name="nom" required
                                    autocomplete="nom" autofocus value="{{ old('nom') }}">
                                <label class="form-label fw-bold mt-3" for="categorie">Catégorie</label>
                                <select class="form-select" id="categorie" name="categorie" required
                                    autocomplete="categorie" autofocus>
                                    <option value="" disabled selected>-- Sélectionner une catégorie --</option>
                                    <option value="Fruit">Fruit</option>
                                    <option value="Légumes">Légumes</option>
                                    <option value="Autres">Autres</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="prix">Prix</label>
                                <input type="number" class="form-control" id="prix" name="prix" required
                                    autocomplete="prix" autofocus value="{{ old('prix') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="stock">Stock</label>
                                <input type="number" class="form-control" id="stock" name="stock" value="0"
                                    autocomplete="stock" autofocus>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn" style="background:#070a23; color:white;"
                                data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn" style="background:#1c911e; color:white;">
                                <i class="fa fa-save me-1"></i> Enregistrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Voir Produit -->
    <div class="modal fade" id="voirProduitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background:#070a23;">
                    <h5 class="modal-title text-white"><i class="fa fa-eye me-2"></i> Détails du produit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="voirProduitContent">
                    <p class="text-muted">Chargement...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Éditer Produit -->
    <div class="modal fade" id="editProduitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background:#1c911e;">
                    <h5 class="modal-title text-white"><i class="fa fa-edit me-2"></i> Modifier le produit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editProduitContent">
                    <p class="text-muted">Chargement...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau Produits -->
    <div class="container-fluid px-4 mt-4">
        <div id="productListContainer">
            <div class="table-main table-responsive rounded shadow-sm" style="overflow-x:auto;" id="listView">
                <table id="produitTable" class="table align-middle mb-0 text-center w-100">
                    <thead>
                        <tr>
                            <th class="sortable">N°</th>
                            <th>Image</th>
                            <th class="sortable">Nom</th>
                            <th class="sortable">Catégorie</th>
                            <th class="sortable">Prix</th>
                            <th class="sortable">Stock</th>
                            <th>Statut</th>
                            <th>Description</th>
                            <th style="width:180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produits as $key => $produit)
                            <tr id="produitRow{{ $produit->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if ($produit->image)
                                        <img src="{{ asset('storage/' . $produit->image) }}"
                                            alt="Image {{ $produit->nom }}" class="product-thumbnail img-fluid rounded"
                                            loading="lazy"
                                            onerror="this.onerror=null;this.src='{{ asset('images/logo1.png') }}';">
                                    @else
                                        <img src="{{ asset('images/logo1.png') }}" alt="Pas d'image"
                                            class="product-thumbnail img-fluid rounded">
                                    @endif
                                </td>
                                <td>{{ $produit->nom }}</td>
                                <td>{{ $produit->categorie }}</td>
                                <td>{{ number_format($produit->prix, 2, ',', ' ') }} GNF</td>
                                <td>{{ $produit->stock }}</td>
                                <td>
                                    @if ($produit->stock >= 0 && $produit->stock <= 5)
                                        <span class="fw-bold text-danger">Rupture</span>
                                    @elseif ($produit->stock >= 6 && $produit->stock <= 10)
                                        <span class="fw-bold text-warning">Presque fini</span>
                                    @else
                                        <span class="fw-bold text-success">Disponible</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($produit->description, 50) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-view" data-id="{{ $produit->id }}"
                                        data-bs-toggle="modal" data-bs-target="#voirProduitModal" style="color:#070a23;">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-edit" data-id="{{ $produit->id }}"
                                        data-bs-toggle="modal" data-bs-target="#editProduitModal" style="color:#1c911e;">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('produits.destroy', $produit->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="color:red;"
                                            onclick="return confirm('Voulez-vous vraiment supprimer {{ $produit->nom }} ?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Aucun produit enregistré</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="gridView" class="row g-3" style="display:none;">
                @foreach ($produits as $produit)
                    <div class="col-6 col-sm-4 col-md-3" data-produit-id="{{ $produit->id }}">
                        <div class="card h-100 shadow-sm">
                            @if ($produit->image)
                                <img src="{{ asset('storage/' . $produit->image) }}" class="card-img-top"
                                    alt="{{ $produit->nom }}" style="object-fit:cover; height:160px;" loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ asset('images/logo1.png') }}';">
                            @else
                                <img src="{{ asset('images/logo1.png') }}" class="card-img-top"
                                    alt="{{ $produit->nom }}" style="object-fit:cover; height:160px;">
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title fw-bold">{{ $produit->nom }}</h6>
                                <p class="mb-1 small text-muted">{{ $produit->categorie }}</p>
                                <p class="mb-2 fw-bold">{{ number_format($produit->prix, 2, ',', ' ') }} GNF</p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <div>
                                        @if ($produit->stock >= 0 && $produit->stock <= 5)
                                            <span class="badge bg-danger">Rupture</span>
                                        @elseif ($produit->stock >= 6 && $produit->stock <= 10)
                                            <span class="badge bg-warning text-dark">Presque fini</span>
                                        @else
                                            <span class="badge bg-success">Disponible</span>
                                        @endif
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" data-id="{{ $produit->id }}"
                                            data-bs-toggle="modal" data-bs-target="#voirProduitModal"><i
                                                class="fa fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-success" data-id="{{ $produit->id }}"
                                            data-bs-toggle="modal" data-bs-target="#editProduitModal"><i
                                                class="fa fa-edit"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const table = document.getElementById("produitTable");
            const headers = table.querySelectorAll(".sortable");
            let sortDirection = {};

            headers.forEach((header, index) => {
                header.addEventListener("click", () => {
                    const tableBody = table.querySelector("tbody");
                    const rows = Array.from(tableBody.querySelectorAll("tr"));
                    const columnIndex = Array.from(header.parentNode.children).indexOf(header);

                    headers.forEach(h => h.classList.remove("sorted-asc", "sorted-desc"));

                    const isAscending = sortDirection[index] = !sortDirection[index];

                    rows.sort((a, b) => {
                        const aText = a.children[columnIndex].innerText.trim();
                        const bText = b.children[columnIndex].innerText.trim();

                        if (!isNaN(aText) && !isNaN(bText)) {
                            return isAscending ? aText - bText : bText - aText;
                        }

                        return isAscending ?
                            aText.localeCompare(bText) :
                            bText.localeCompare(aText);
                    });

                    header.classList.add(isAscending ? "sorted-asc" : "sorted-desc");

                    rows.forEach(row => tableBody.appendChild(row));
                });
            });

            // Toggle between grid and list view
            const btnGrid = document.getElementById('btnGridView');
            const btnList = document.getElementById('btnListView');
            const listView = document.getElementById('listView');
            const gridView = document.getElementById('gridView');

            function setView(view) {
                if (view === 'grid') {
                    listView.style.display = 'none';
                    gridView.style.display = 'flex';
                    localStorage.setItem('produitView', 'grid');
                } else {
                    listView.style.display = '';
                    gridView.style.display = 'none';
                    localStorage.setItem('produitView', 'list');
                }
            }

            btnGrid.addEventListener('click', function() {
                setView('grid');
                btnGrid.classList.add('active');
                btnList.classList.remove('active');
            });
            btnList.addEventListener('click', function() {
                setView('list');
                btnList.classList.add('active');
                btnGrid.classList.remove('active');
            });

            // Load saved view
            const savedView = localStorage.getItem('produitView') || 'list';
            setView(savedView);
            if (savedView === 'grid') {
                btnGrid.classList.add('active');
            } else {
                btnList.classList.add('active');
            }
        });
    </script>
@endsection
