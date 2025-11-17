@extends('base')

@section('title', 'Clients')

@section('content')
    <div class="all-title-box">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Gestion des Clients</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('app_accueil') }}" style="color:#070a23;">Accueil</a>
                        </li>
                        <li class="breadcrumb-item active" style="color:white;">Clients</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Header + Boutons -->
    <div class="container mt-4">
        <div class="row align-items-center mb-4 p-3 rounded shadow-sm"
            style="background:white; border-left:5px solid #1c911e;">
            <div class="col-md-4 d-flex align-items-center gap-2">
                <i class="fa fa-user fa-2x" style="color:#070a23;"></i>
                <h2 class="mb-0 fw-bold" style="color:#070a23;">Liste des Clients</h2>
            </div>
            <div class="col-md-4">
                <form method="GET" action="{{ route('clients.index') }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher..."
                            value="{{ request('search') }}">
                        <button class="btn" type="submit" style="background:#1c911e; color:white;">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn fw-bold shadow-sm" style="background:#1c911e; color:white;"
                    data-bs-toggle="modal" data-bs-target="#ajoutClientModal">
                    <i class="fa fa-plus me-1"></i> Ajouter un client
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Ajout Client -->
    <div class="modal fade" id="ajoutClientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background:#1c911e;">
                    <h5 class="modal-title text-white"><i class="fa fa-user-plus me-2"></i> Ajouter un client</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data"
                    id="formAjoutClient">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6 text-center">
                                <label for="image" class="form-label fw-bold">Photo du client</label>
                                <div id="previewContainerClient" class="d-flex justify-content-center align-items-center"
                                    style="width:130px; height:130px; background:#f8f9fa; cursor:pointer; position:relative; border-radius:50%; overflow:hidden; margin:auto;"
                                    title="Cliquer pour choisir une image">
                                    <i class="fa fa-plus fa-2x text-muted" id="iconPlusClient"></i>
                                    <img id="previewImageClient" src="" alt="Aperçu photo"
                                        style="display:none; width:100%; height:100%; object-fit:cover; border-radius:50%;">
                                </div>
                                <input type="file" name="image" id="image" class="form-control d-none"
                                    accept="image/*">
                            </div>

                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold" for="nom">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required
                                        value="{{ old('nom') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold" for="tel">Téléphone</label>
                                    <input type="text" class="form-control" id="tel" name="tel" required
                                        value="{{ old('tel') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="whatsapp">WhatsApp</label>
                                <input type="text" class="form-control" id="whatsapp" name="whatsapp"
                                    value="{{ old('whatsapp') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="adresse">Adresse</label>
                                <input type="text" class="form-control" id="adresse" name="adresse"
                                    value="{{ old('adresse') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="statut">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="" disabled selected>-- Sélectionner le statut --</option>
                                    <option value="actif">Actif</option>
                                    <option value="inactif">Inactif</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
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

    <!-- Modal Voir Client -->
    <div class="modal fade" id="voirClientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background:#070a23;">
                    <h5 class="modal-title text-white"><i class="fa fa-eye me-2"></i> Détails du client</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="voirClientContent">
                    <p class="text-muted">Chargement...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Éditer Client -->
    <div class="modal fade" id="editClientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background:#1c911e;">
                    <h5 class="modal-title text-white"><i class="fa fa-edit me-2"></i> Modifier le client</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editClientContent">
                    <p class="text-muted">Chargement...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau Clients -->
    <div class="container-fluid px-4 mt-4">
        <div class="table-main table-responsive rounded shadow-sm" style="overflow-x:auto;">
            <table id="clientTable" class="table align-middle mb-0 text-center w-100">
                <thead style="background:#1c911e; color:white;">
                    <tr>
                        <th class="sortable">N°</th>
                        <th>Photo</th>
                        <th class="sortable">Nom</th>
                        <th class="sortable">Téléphone</th>
                        <th class="sortable">WhatsApp</th>
                        <th>Adresse</th>
                        <th class="sortable">Statut</th>
                        <th>Description</th>
                        <th style="width:180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $key => $client)
                        <tr id="clientRow{{ $client->id }}">
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if ($client->image)
                                    <img src="{{ asset('storage/' . $client->image) }}" alt="Photo {{ $client->nom }}"
                                        style="width:60px; height:60px; border-radius:50%;">
                                @else
                                    @php
                                        $initiales = collect(explode(' ', trim($client->nom)))
                                            ->map(fn($mot) => mb_substr($mot, 0, 1))
                                            ->take(2)
                                            ->implode('');
                                    @endphp
                                    <div
                                        style="
                                    width:60px;
                                    height:60px;
                                    border-radius:50%;
                                    background:#1c911e;
                                    color:white;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-weight:bold;
                                    font-size:18px;
                                    margin:auto;
                                ">
                                        {{ strtoupper($initiales) }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $client->nom }}</td>
                            <td>{{ $client->tel }}</td>
                            <td>{{ $client->whatsapp }}</td>
                            <td>{{ $client->adresse }}</td>
                            <td>
                                @if ($client->statut == 'actif')
                                    <span class="fw-bold text-success">Actif</span>
                                @else
                                    <span class="fw-bold text-danger">Inactif</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($client->description, 50) }}</td>
                            <td>
                                <button class="btn btn-sm btn-view" data-id="{{ $client->id }}" data-bs-toggle="modal"
                                    data-bs-target="#voirClientModal" style="color:#070a23;">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-edit" data-id="{{ $client->id }}" data-bs-toggle="modal"
                                    data-bs-target="#editClientModal" style="color:#1c911e;">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form action="{{ route('clients.destroy', $client->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="color:red;"
                                        onclick="return confirm('Voulez-vous vraiment supprimer {{ $client->nom }} ?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Aucun client enregistré</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
