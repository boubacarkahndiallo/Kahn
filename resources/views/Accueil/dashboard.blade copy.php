@extends('base')
@section('title', 'Tableau de bord')
@section('content')

    <div class="container-fluid mb-4" style="background:#1c911e; color:white;">
        <div class="page-header ">
            <div class="container py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="mb-1 text-light" style="font-weight:600;">Tableau de Bord</h2>
                        <small class="text-white"><a href="{{ route('app_accueil') }}">Accueil</a> &nbsp;/&nbsp;
                            <strong>Dashboard</strong></small>
                    </div>
                    <div class=" text-white d-flex gap-4 justify-content-end">
                        <div style="font-size:12px">Date<br><strong
                                style="font-size:14px">{{ \Carbon\Carbon::now()->format('d M Y') }}</strong></div>
                        <div style="font-size:12px">Dernière mise à jour<br><strong
                                style="font-size:14px">{{ \Carbon\Carbon::now()->format('H:i') }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-2">

        <style>
            .stat-card {
                background: white;
                border: none;
                border-radius: 8px;
                padding: 24px;
                display: flex;
                gap: 16px;
                align-items: flex-start;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 56px;
                height: 56px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .stat-content h3 {
                font-size: 18px;
                font-weight: 600;
                margin: 0;
                line-height: 1.2;
            }

            .stat-label {
                font-size: 12px;
                color: #8899aa;
                margin-top: 4px;
            }

            .stat-subtext {
                font-size: 12px;
                color: #6c757d;
                margin-top: 8px;
            }
        </style>

        <div class="row g-3 mb-4">
            <!-- Produits -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #d4edda;">
                        <i class="fa fa-box fa-lg" style="color: #1c911e;"></i>
                    </div>
                    <div class="stat-content flex-grow-1">
                        <div class="stat-label">Produits</div>
                        <div style="display: flex; align-items: baseline; gap: 6px;">
                            <h3>{{ number_format($produitsCount) }}</h3>
                            <span class="stat-label">Total</span>
                        </div>
                        <div class="stat-subtext">Stock faible : <strong>0</strong></div>
                    </div>
                </div>
            </div>

            <!-- Commandes -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e7f3ff;">
                        <i class="fa fa-shopping-cart fa-lg" style="color: #0066cc;"></i>
                    </div>
                    <div class="stat-content flex-grow-1">
                        <div class="stat-label">Commandes</div>
                        <div style="display: flex; align-items: baseline; gap: 6px;">
                            <h3>{{ number_format($commandesCount) }}</h3>
                            <span class="stat-label">enregistrées</span>
                        </div>
                        <div class="stat-subtext">Aujourd'hui : <strong>{{ $commandesToday ?? 2 }}</strong></div>
                    </div>
                </div>
            </div>

            <!-- Clients -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f0f0f2;">
                        <i class="fa fa-users fa-lg" style="color: #666;"></i>
                    </div>
                    <div class="stat-content flex-grow-1">
                        <div class="stat-label">Clients</div>
                        <div style="display: flex; align-items: baseline; gap: 6px;">
                            <h3>{{ number_format($clientsCount) }}</h3>
                            <span class="stat-label">inscrits</span </div>
                        </div>
                        <div class="stat-subtext">Nouveaux aujourd'hui : <strong>{{ $newClientsToday ?? 1 }}</strong>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Ventes -->
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #d4edda;">
                        <i class="fa fa-money-bill-wave fa-lg" style="color: #1c911e;"></i>
                    </div>
                    <div class="stat-content flex-grow-1">
                        <div class="stat-label">Ventes</div>
                        <div style="display: flex; align-items: baseline; gap: 6px;">
                            <h3>{{ number_format($totalSales, 0, ',', ' ') }} GNF</h3>
                            <span class="stat-label">total</span>
                        </div>
                        <div class="stat-subtext">Aujourd'hui :
                            <strong>{{ number_format($salesToday ?? 70000, 0, ',', ' ') }} GNF</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom"
                        style="padding: 20px; border-color: #e9ecef !important;">
                        <h5 class="mb-0" style="color: #1c911e; font-weight: 600;">
                            <i class="fa fa-shopping-bag me-2" style="color: #1c911e;"></i>Derniers produits
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($recentProduits as $p)
                                <li class="list-group-item d-flex align-items-center" style="padding: 16px;">
                                    <img src="{{ $p->image ? asset('storage/' . $p->image) : asset('images/logo1.png') }}"
                                        alt="{{ $p->nom }}" style="width:56px; height:56px; object-fit:cover;"
                                        class="me-3 rounded">
                                    <div class="flex-grow-1 min-w-0">
                                        <strong style="color: #212529;">{{ $p->nom }}</strong>
                                        <div class="text-muted small">{{ $p->categorie ?? '—' }} ·
                                            {{ number_format($p->prix, 0, ',', ' ') }} GNF</div>
                                    </div>
                                    <a href="{{ route('produits.show', $p->id) }}"
                                        class="btn btn-sm btn-outline-success ms-2" style="flex-shrink: 0;">Voir</a>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-4">Aucun produit récent</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom"
                        style="padding: 20px; border-color: #e9ecef !important;">
                        <h5 class="mb-0" style="color: #1c911e; font-weight: 600;">
                            <i class="fa fa-list-check me-2" style="color: #1c911e;"></i>Dernières commandes
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0" style="font-size: 14px;">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th style="padding: 12px 16px; color: #6c757d; font-weight: 600;">Numéro</th>
                                        <th style="padding: 12px 16px; color: #6c757d; font-weight: 600;">Client</th>
                                        <th style="padding: 12px 16px; color: #6c757d; font-weight: 600;">Total</th>
                                        <th style="padding: 12px 16px; color: #6c757d; font-weight: 600;">Statut</th>
                                        <th style="padding: 12px 16px; color: #6c757d; font-weight: 600;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentCommandes as $c)
                                        <tr style="border-bottom: 1px solid #e9ecef;">
                                            <td style="padding: 12px 16px;">
                                                <strong>{{ $c->numero_commande }}</strong>
                                            </td>
                                            <td style="padding: 12px 16px;">{{ $c->client?->nom ?? '—' }}</td>
                                            <td style="padding: 12px 16px; color: #1c911e; font-weight: 600;">
                                                {{ $c->getTotalFormatAttribute() }}
                                            </td>
                                            <td style="padding: 12px 16px;">
                                                <span class="badge"
                                                    style="background: #1c911e;">{{ $c->statut }}</span>
                                            </td>
                                            <td style="padding: 12px 16px;">
                                                <a href="{{ route('commandes.show', $c->id) }}"
                                                    class="btn btn-sm btn-outline-success">Voir</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Aucune commande
                                                récente
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Graphiques et Analyses -->
            <div class="row mt-5">
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom"
                            style="padding: 20px; border-color: #e9ecef !important;">
                            <h5 class="mb-0" style="color: #1c911e; font-weight: 600;">
                                <i class="fa fa-chart-line me-2" style="color: #1c911e;"></i>Tendance des ventes (7
                                derniers jours)
                            </h5>
                        </div>
                        <div class="card-body" style="padding: 30px;">
                            <canvas id="chartVentes" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom"
                            style="padding: 20px; border-color: #e9ecef !important;">
                            <h5 class="mb-0" style="color: #1c911e; font-weight: 600;">
                                <i class="fa fa-pie-chart me-2" style="color: #1c911e;"></i>Distribution par statut
                            </h5>
                        </div>
                        <div class="card-body" style="padding: 30px;">
                            <canvas id="chartStatuts" style="max-height: 250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom"
                            style="padding: 20px; border-color: #e9ecef !important;">
                            <h5 class="mb-0" style="color: #1c911e; font-weight: 600;">
                                <i class="fa fa-tasks me-2" style="color: #1c911e;"></i>Commandes par jour
                            </h5>
                        </div>
                        <div class="card-body" style="padding: 30px;">
                            <canvas id="chartCommandes" style="max-height: 250px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom"
                            style="padding: 20px; border-color: #e9ecef !important;">
                            <h5 class="mb-0" style="color: #1c911e; font-weight: 600;">
                                <i class="fa fa-star me-2" style="color: #1c911e;"></i>Top 5 Produits
                            </h5>
                        </div>
                        <div class="card-body" style="padding: 30px;">
                            <canvas id="chartProduits" style="max-height: 250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @endsection
