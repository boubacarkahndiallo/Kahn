document.addEventListener('DOMContentLoaded', function () {
    // =========================
    // Variables modal
    // =========================
    const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
    const modalImage = document.getElementById('modal-produit-image');
    const modalNom = document.getElementById('modal-produit-nom');
    const modalPrix = document.getElementById('modal-produit-prix');
    const modalQuantite = document.getElementById('modal-quantite');
    const modalTotal = document.getElementById('modal-total');
    const btnValiderCommande = document.getElementById('btn-valider-commande');

    function updateTotal() {
        const prix = parseFloat(modalPrix.dataset.prix);
        const qty = parseInt(modalQuantite.value) || 1;
        modalTotal.textContent = (prix * qty).toLocaleString('fr-FR');
    }
    modalQuantite.addEventListener('input', updateTotal);

    function addToCart(produitId, quantite = 1) {
        fetch(`/panier/add`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ produit_id: produitId, quantite: quantite })
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                alert(resp.message || "Produit ajouté au panier !");
                const cartCount = document.getElementById('cart-count');
                if (cartCount && resp.total_items !== undefined) {
                    cartCount.textContent = resp.total_items;
                }
                cartModal.hide();
            } else {
                alert(resp.message || "Erreur lors de l'ajout au panier !");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Une erreur est survenue, réessayez.");
        });
    }

    // Lier les boutons "Ajouter au panier"
    document.querySelectorAll('.cart, .btn-ajout-panier').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const produitId = this.dataset.id;
            const produitSingle = this.closest('.products-single') || this.closest('.list-view-box');
            const produitNom = produitSingle.querySelector('h4, h3').textContent;
            const produitPrix = parseFloat(produitSingle.querySelector('h5').textContent.replace(/\s|GNF/g,'').replace(',', '.'));
            const produitImage = produitSingle.querySelector('img').src;

            modalImage.src = produitImage;
            modalNom.textContent = produitNom;
            modalPrix.textContent = produitPrix.toLocaleString('fr-FR');
            modalPrix.dataset.prix = produitPrix;
            modalQuantite.value = 1;
            updateTotal();

            cartModal.show();

            btnValiderCommande.onclick = function () {
                const qty = parseInt(modalQuantite.value) || 1;
                addToCart(produitId, qty);
            };
        });
    });

    // Filtre catégorie
    const filterBtns = document.querySelectorAll(".filter-btn");
    const products = document.querySelectorAll(".product-item");
    filterBtns.forEach(btn => {
        btn.addEventListener("click", e => {
            e.preventDefault();
            const filter = btn.dataset.filter;
            filterBtns.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            products.forEach(prod => {
                prod.style.display = (filter === 'all' || prod.dataset.category === filter) ? "block" : "none";
            });
        });
    });

    // Recherche par nom
    const searchBar = document.getElementById("search-bar");
    searchBar.addEventListener("input", () => {
        const searchText = searchBar.value.toLowerCase();
        products.forEach(prod => {
            const name = (prod.querySelector(".sale") || prod.querySelector("h3")).textContent.toLowerCase();
            prod.style.display = name.includes(searchText) ? "block" : "none";
        });
    });

    // Slider prix
    $(function () {
        const prices = Array.from(products).map(p => parseInt(p.dataset.price));
        const maxPrice = Math.max(...prices, 1000);
        $("#slider-range").slider({
            range: true,
            min: 0,
            max: maxPrice,
            values: [0, maxPrice],
            slide: function(event, ui) {
                $("#amount").val(ui.values[0] + " GNF - " + ui.values[1] + " GNF");
            }
        });
        $("#amount").val($("#slider-range").slider("values", 0) + " GNF - " + $("#slider-range").slider("values", 1) + " GNF");

        $("#filter-price-btn").click(function() {
            const minPrice = $("#slider-range").slider("values", 0);
            const maxPrice = $("#slider-range").slider("values", 1);
            products.forEach(prod => {
                const price = parseInt(prod.dataset.price);
                prod.style.display = (price >= minPrice && price <= maxPrice) ? "block" : "none";
            });
        });
    });
});
