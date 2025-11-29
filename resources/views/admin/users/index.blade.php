@extends('base')

@section('title', 'Utilisateurs')

@section('content')
    <div class="all-title-box">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Gestion des Utilisateurs</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('app_accueil') }}" style="color:#070a23;">Accueil</a>
                        </li>
                        <li class="breadcrumb-item active" style="color:white;">Utilisateurs</li>
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
                <i class="fa fa-users fa-2x" style="color:#070a23;"></i>
                <h2 class="mb-0 fw-bold" style="color:#070a23;">Liste des Utilisateurs</h2>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Rechercher..." id="searchInput">
            </div>
            <div class="col-md-4 text-end">
                @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'super_admin']))
                    <button type="button" class="btn fw-bold shadow-sm" style="background:#1c911e; color:white;"
                        data-bs-toggle="modal" data-bs-target="#ajoutUserModal">
                        <i class="fa fa-plus me-1"></i> Ajouter un utilisateur
                    </button>
                @else
                    <button type="button" class="btn fw-bold shadow-sm" style="background:#999; color:white;" disabled
                        title="Seul les Admin et Super Admin peuvent ajouter des utilisateurs">
                        <i class="fa fa-lock me-1"></i> Ajouter (Admin)
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Ajout Utilisateur -->
    <div class="modal fade" id="ajoutUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background:#1c911e;">
                    <h5 class="modal-title text-white"><i class="fa fa-user-plus me-2"></i> Ajouter un utilisateur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="ajoutUserContent">
                    <form id="ajoutUserForm" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="row g-3">

                            <!-- PHOTO -->
                            <div class="col-md-6 text-center">
                                <label class="form-label fw-bold">Photo de l'utilisateur</label>
                                <div id="imageContainerAjout"
                                    style="width:130px; height:130px; background:#f8f9fa; border-radius:50%;
                                               overflow:hidden; cursor:pointer; margin:auto; position:relative;
                                               display:flex; align-items:center; justify-content:center;">
                                    <img id="previewPhotoAjout" src="https://via.placeholder.com/130"
                                        style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:none;">
                                    <i class="fa fa-plus fa-2x text-muted" id="iconPlusAjout"></i>
                                    <input type="file" name="photo" id="photoAjout" accept="image/*"
                                        style="position:absolute; width:100%; height:100%; opacity:0; cursor:pointer;">
                                </div>
                            </div>

                            <!-- INFOS UTILISATEUR -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold mt-2">Prénom</label>
                                <input type="text" class="form-control" name="prenom" required>
                                <label class="form-label fw-bold">Nom</label>
                                <input type="text" class="form-control" name="nom" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Téléphone</label>
                                <input type="text" class="form-control" name="tel" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">WhatsApp</label>
                                <input type="text" class="form-control" name="whatsapp" id="whatsappAjout">
                                <small id="whatsappErrorAjout" class="text-danger d-none">Numéro WhatsApp invalide</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Adresse</label>
                                <input type="text" class="form-control" name="adresse">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Rôle</label>
                                <select class="form-select" name="role" required>
                                    <option value="" disabled selected>-- Sélectionner un rôle --</option>
                                    @if (auth()->check() && auth()->user()->role === 'super_admin')
                                        <option value="super_admin">Super Admin</option>
                                    @endif
                                    @if (auth()->check() && auth()->user()->role === 'super_admin')
                                        <option value="admin">Admin</option>
                                    @endif
                                    <option value="user">Utilisateur</option>
                                </select>
                            </div>
                            <div class="col-12 text-end mt-3">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" id="saveUserBtn" class="btn btn-success">Enregistrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Voir Utilisateur -->
    <div class="modal fade" id="voirUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background:#070a23;">
                    <h5 class="modal-title text-white"><i class="fa fa-eye me-2"></i> Détails utilisateur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="voirUserContent">
                    <!-- Contenu chargé via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Éditer Utilisateur -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background:#1c911e;">
                    <h5 class="modal-title text-white"><i class="fa fa-edit me-2"></i> Modifier l'utilisateur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editUserContent">
                    <!-- Contenu chargé via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau Utilisateurs -->
    <div class="container-fluid px-4 mt-4">
        <div class="table-main table-responsive rounded shadow-sm" style="overflow-x: auto;">
            <table class="table table-hover align-middle mb-0 w-100" style="border: none;">
                <thead style="background: linear-gradient(135deg, #1c911e 0%, #145a14 100%); color: white;">
                    <tr>
                        <th class="text-center" width="5%">N°</th>
                        <th class="text-center" width="10%">Photo</th>
                        <th width="20%">Nom complet</th>
                        <th width="15%">Téléphone</th>
                        <th width="15%">Email</th>
                        <th width="12%">Rôle</th>
                        <th class="text-center" width="13%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $key => $user)
                        <tr id="userRow{{ $user->id }}"
                            style="border-bottom: 1px solid #e9ecef; transition: background 0.2s;">
                            <td class="text-center fw-bold" style="color: #1c911e;">{{ $key + 1 }}</td>
                            <td class="text-center">
                                @if ($user->photo)
                                    <img src="{{ $user->photo ? Storage::url($user->photo) : asset('images/logo1.png') }}"
                                        width="55" height="55" class="rounded-circle border"
                                        style="border: 2px solid #1c911e; object-fit: cover;">
                                @else
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                        style="width: 55px; height: 55px; background: #f0f0f0; border: 2px solid #ddd;">
                                        <i class="fa fa-user text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong class="user-nom">{{ $user->prenom }} {{ $user->nom }}</strong>
                                <br>
                                <small class="text-muted user-adresse">{{ $user->adresse ?? '-' }}</small>
                            </td>
                            <td class="user-tel">
                                <i class="fas fa-phone text-success me-2"></i>{{ $user->tel }}
                            </td>
                            <td class="user-email">
                                <i class="fas fa-envelope text-success me-2"></i>
                                <small>{{ $user->email }}</small>
                            </td>
                            <td class="text-center">
                                @if ($user->role == 'super_admin')
                                    <span class="badge bg-danger" style="font-size: 12px;">
                                        <i class="fa fa-crown me-1"></i> Super Admin
                                    </span>
                                @elseif($user->role == 'admin')
                                    <span class="badge bg-success" style="font-size: 12px;">
                                        <i class="fa fa-star me-1"></i> Admin
                                    </span>
                                @else
                                    <span class="badge bg-warning" style="font-size: 12px;">
                                        <i class="fa fa-user me-1"></i> Utilisateur
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    {{-- Show button always visible --}}
                                    <button class="btn btn-outline-dark btn-view" data-id="{{ $user->id }}"
                                        title="Voir les détails">
                                        <i class="fa fa-eye"></i>
                                    </button>

                                    @php $currentRole = auth()->check() ? auth()->user()->role : null; @endphp

                                    {{-- If the current user is super_admin, allow edit/delete for everyone --}}
                                    @if ($currentRole === 'super_admin')
                                        <button class="btn btn-outline-success btn-edit" data-id="{{ $user->id }}"
                                            title="Modifier">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-delete" data-id="{{ $user->id }}"
                                            title="Supprimer">
                                            <i class="fa fa-trash"></i>
                                        </button>

                                        {{-- If current is admin, prevent edit/delete on super_admin rows --}}
                                    @elseif ($currentRole === 'admin')
                                        @if ($user->role !== 'super_admin')
                                            <button class="btn btn-outline-success btn-edit"
                                                data-id="{{ $user->id }}" title="Modifier">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-delete"
                                                data-id="{{ $user->id }}" title="Supprimer">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @else
                                            {{-- For super_admin rows, show only the view button to admins (no edit/delete) --}}
                                        @endif
                                    @else
                                        {{-- Non-admin users (role user) shouldn't see edit/delete at all --}}
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-muted">Aucun utilisateur trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- SCRIPT POUR IMAGE ET WHATSAPP -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Aperçu photo
            const photoInput = document.getElementById('photoAjout');
            const preview = document.getElementById('previewPhotoAjout');
            const icon = document.getElementById('iconPlusAjout');

            photoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        preview.src = event.target.result;
                        preview.style.display = 'block';
                        icon.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.style.display = 'none';
                    icon.style.display = 'block';
                }
            });

            // Vérification du numéro WhatsApp
            const whatsappInput = document.getElementById('whatsappAjout');
            const whatsappError = document.getElementById('whatsappErrorAjout');

            whatsappInput.addEventListener('input', () => {
                const value = whatsappInput.value.trim();
                const isValid = /^\+?\d{8,15}$/.test(value);
                whatsappError.classList.toggle('d-none', isValid || value === '');
            });
        });
    </script>
@endsection
