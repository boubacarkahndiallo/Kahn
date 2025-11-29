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
                                    <div class="input-group">
                                        <span class="input-group-text phone-flag" aria-hidden="true"></span>
                                        <input type="tel" class="form-control phone-input" id="tel" name="tel"
                                            required value="{{ old('tel') }}" aria-describedby="telFeedback">
                                    </div>
                                    <input type="hidden" id="tel_e164" name="tel_e164" value="{{ old('tel_e164') }}">
                                    <input type="hidden" id="tel_country" name="tel_country"
                                        value="{{ old('tel_country') }}">
                                    <input type="hidden" id="tel_dialcode" name="tel_dialcode"
                                        value="{{ old('tel_dialcode') }}">
                                    <div class="invalid-feedback" id="telFeedback">Veuillez entrer un numéro de téléphone
                                        valide.</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="whatsapp">WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text phone-flag" aria-hidden="true"></span>
                                    <input type="tel" class="form-control phone-input" id="whatsapp"
                                        name="whatsapp" value="{{ old('whatsapp') }}"
                                        aria-describedby="whatsappFeedback">
                                </div>
                                <input type="hidden" id="whatsapp_e164" name="whatsapp_e164"
                                    value="{{ old('whatsapp_e164') }}">
                                <input type="hidden" id="whatsapp_country" name="whatsapp_country"
                                    value="{{ old('whatsapp_country') }}">
                                <input type="hidden" id="whatsapp_dialcode" name="whatsapp_dialcode"
                                    value="{{ old('whatsapp_dialcode') }}">
                                <div class="invalid-feedback" id="whatsappFeedback">Veuillez entrer un numéro WhatsApp
                                    valide.</div>
                                <input type="hidden" id="whatsapp_e164" name="whatsapp_e164"
                                    value="{{ old('whatsapp_e164') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="adresse">Adresse</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="adresse" name="adresse"
                                        value="{{ old('adresse') }}" aria-label="Adresse">
                                    <button id="detectPositionBtn" class="btn btn-outline-secondary" type="button"
                                        title="Détecter ma position">
                                        <i class="fa fa-map-marker-alt"></i>
                                    </button>
                                </div>
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
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
                                    <img src="{{ $client->image ? Storage::url($client->image) : asset('images/logo1.png') }}"
                                        alt="Photo {{ $client->nom }}"
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
                            <td>
                                {{ $client->adresse }}
                                @if ($client->latitude && $client->longitude)
                                    <br>
                                    <a href="https://www.google.com/maps?q={{ $client->latitude }},{{ $client->longitude }}"
                                        target="_blank" rel="noopener" class="small">Voir sur Google Maps</a>
                                @endif
                            </td>
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
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const detectBtn = document.getElementById('detectPositionBtn');
            if (detectBtn) {
                detectBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Use HTML5 geolocation
                    if (!navigator.geolocation) {
                        alert('Géolocalisation non supportée par votre navigateur');
                        return;
                    }
                    detectBtn.disabled = true;
                    detectBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                    navigator.geolocation.getCurrentPosition(async function(position) {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;
                            document.getElementById('latitude').value = lat;
                            document.getElementById('longitude').value = lon;
                            // Reverse geocode with Nominatim for readable address
                            try {
                                const resp = await fetch(
                                    `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`
                                );
                                const data = await resp.json();
                                if (data && data.display_name) {
                                    document.getElementById('adresse').value = data.display_name;
                                }
                            } catch (err) {
                                console.warn('Reverse geocoding failed', err);
                            } finally {
                                detectBtn.disabled = false;
                                detectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>';
                            }
                        },
                        function(err) {
                            detectBtn.disabled = false;
                            detectBtn.innerHTML = '<i class="fa fa-map-marker-alt"></i>';
                            alert('Impossible d\'obtenir votre position: ' + (err.message ||
                                'Erreur'));
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        });
                });
            }

            // When opening voir modal, inject coordinates and map link if present
            document.querySelectorAll('.btn-view').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const id = this.dataset.id;
                    const modalContent = document.getElementById('voirClientContent');
                    modalContent.innerHTML = '<p class="text-muted">Chargement...</p>';
                    try {
                        const resp = await fetch(`/clients/${id}/ajax-show`);
                        const data = await resp.json();
                        if (data && data.nom) {
                            let mapHtml = '';
                            if (data.latitude && data.longitude) {
                                mapHtml =
                                    `\n                                    <p>Coordonnées: <strong>${data.latitude}, ${data.longitude}</strong></p>\n                                    <p><a href="https://www.google.com/maps?q=${data.latitude},${data.longitude}" target="_blank" rel="noopener">Voir sur Google Maps</a></p>`;
                            }
                            modalContent.innerHTML =
                                `\n                                <div class="row g-3">\n                                    <div class="col-md-4 text-center">\n                                        ${data.image ? `<img src="${data.image}" class="img-fluid rounded" alt="${data.nom}">` : ''}\n                                    </div>\n                                    <div class="col-md-8">\n                                        <h5>${data.nom}</h5>\n                                        <p>Téléphone: ${data.tel}</p>\n                                        <p>WhatsApp: ${data.whatsapp || 'N/A'}</p>\n                                        <p>Adresse: ${data.adresse || 'N/A'}</p>\n                                        ${mapHtml}\n                                        <p>Statut: ${data.statut}</p>\n                                        <p>${data.description || ''}</p>\n                                    </div>\n                                </div>\n                            `;
                        }
                    } catch (err) {
                        modalContent.innerHTML =
                            '<p class="text-danger">Impossible de charger les détails du client.</p>';
                    }
                });
            });
        });
    </script>
@endpush
