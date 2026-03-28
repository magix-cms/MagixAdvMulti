<div class="tab-pane fade" id="magix-advmulti-pane" role="tabpanel" aria-labelledby="magix-advmulti-tab" tabindex="0">
    <div class="card shadow-sm border-0 mt-3">
        <div class="card-body">

            {* VUE 1 : LA ZONE AJAX (Liste) *}
            <div id="magix-advmulti-app" data-module="{$advmulti_module}" data-id="{$advmulti_id_module}">
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p>Chargement des points forts...</p>
                </div>
            </div>

            {* VUE 2 : LE FORMULAIRE *}
            <div id="adv_view_form" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h5 class="mb-0 text-primary" id="adv_form_title">
                        <i class="bi bi-pencil-square me-2"></i>Ajouter un point fort
                    </h5>
                    <div>
                        {if isset($langs)}{include file="components/dropdown-lang.tpl" prefix="adv_"}{/if}
                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="advApp.showList()">
                            <i class="bi bi-arrow-left"></i> Retour à la liste
                        </button>
                    </div>
                </div>

                <form id="adv_form_element">
                    <input type="hidden" id="adv_id_advmulti" name="id_advmulti" value="0">

                    {* 🟢 CHAMP GLOBAL : Le sélecteur d'icônes *}
                    <div class="bg-light p-4 rounded border mb-4">
                        <label class="form-label fw-bold"><i class="bi bi-magic me-2"></i>Icône du point fort</label>
                        <input type="hidden" name="icon_advmulti" id="adv_icon_input" value="">

                        <div class="dropdown custom-icon-select w-50">
                            <button class="btn btn-white border w-100 text-start d-flex align-items-center justify-content-between bg-white"
                                    type="button" id="iconDropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <span id="adv_icon_preview">Sélectionnez une icône...</span>
                                <i class="bi bi-chevron-down text-muted"></i>
                            </button>

                            <div class="dropdown-menu w-100 p-2 shadow" aria-labelledby="iconDropdownMenu">
                                <div class="mb-2 position-relative">
                                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                    <input type="text" class="form-control ps-5" id="icon_search_input" placeholder="Rechercher (ex: star, check...)">
                                </div>
                                <div class="icon-list-container" style="max-height: 250px; overflow-y: auto;">
                                    <div class="row g-1" id="icon_list">
                                        {if isset($available_icons)}
                                            {foreach $available_icons as $icon}
                                                <div class="col-3 col-md-2">
                                                    <button class="btn btn-light border-0 w-100 text-center py-2 icon-option"
                                                            type="button" data-value="{$icon}" title="{$icon}">
                                                        <i class="{if $icon|strpos:'bi-' === 0}bi{else}ico{/if} {$icon} fs-4 d-block mb-1"></i>
                                                    </button>
                                                </div>
                                            {/foreach}
                                        {/if}
                                    </div>
                                    <div id="icon_no_result" class="text-center text-muted p-3 d-none">Aucune icône trouvée.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {* 🟢 CHAMPS MULTILINGUES *}
                    <div class="tab-content">
                        {if isset($langs)}
                            {foreach $langs as $idLang => $iso}
                                <div class="tab-pane fade {if $iso@first}show active{/if}" id="adv_lang-{$idLang}" role="tabpanel">

                                    <div class="row g-4 mb-4">
                                        <div class="col-md-8">
                                            <label class="form-label fw-medium">Titre</label>
                                            <input type="text" class="form-control" name="title_advmulti[{$idLang}]">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Statut</label>
                                            <div class="form-check form-switch fs-5 mt-1">
                                                <input type="hidden" name="published_advmulti[{$idLang}]" value="0">
                                                <input class="form-check-input" type="checkbox" name="published_advmulti[{$idLang}]" value="1" checked>
                                                <label class="form-check-label fs-6 text-muted">En ligne</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <div class="col-md-8">
                                            <label class="form-label fw-medium">URL du lien (Optionnel)</label>
                                            <input type="url" class="form-control" name="url_advmulti[{$idLang}]" placeholder="https://...">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Ouverture du lien</label>
                                            <div class="form-check mt-2">
                                                <input type="hidden" name="blank_advmulti[{$idLang}]" value="0">
                                                <input class="form-check-input" type="checkbox" name="blank_advmulti[{$idLang}]" value="1" id="blank_{$idLang}">
                                                <label class="form-check-label text-muted" for="blank_{$idLang}">Ouvrir dans un nouvel onglet</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-medium">Description :</label>
                                        <textarea class="form-control mceEditor" name="desc_advmulti[{$idLang}]" id="adv_desc_{$idLang}" rows="6"></textarea>
                                    </div>

                                </div>
                            {/foreach}
                        {/if}
                    </div>
                </form>

                <hr class="my-4">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary me-2 px-4" onclick="advApp.showList()">Annuler</button>
                    <button type="button" class="btn btn-success px-5" onclick="advApp.save()">
                        <i class="bi bi-save me-2"></i> Enregistrer
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{block name="javascripts" append}
{literal}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Initialisation de l'App AJAX
            if (typeof MagixAjaxManager !== 'undefined') {
                window.advApp = new MagixAjaxManager(
                    'magix-advmulti-app',
                    'magix-advmulti-tab',
                    'MagixAdvMulti',
                    'adv',
                    'advmulti'
                );
            }

            // 2. Gestion du moteur de recherche d'icônes
            const searchInput = document.getElementById('icon_search_input');
            const iconOptions = document.querySelectorAll('.icon-option');
            const noResultMsg = document.getElementById('icon_no_result');
            const hiddenInput = document.getElementById('adv_icon_input');
            const previewSpan = document.getElementById('adv_icon_preview');

            if (searchInput && iconOptions.length > 0) {
                searchInput.addEventListener('input', function(e) {
                    const term = e.target.value.toLowerCase();
                    let hasResults = false;

                    iconOptions.forEach(btn => {
                        const iconName = btn.getAttribute('data-value').toLowerCase();
                        if (iconName.includes(term)) {
                            btn.parentElement.style.display = 'block';
                            hasResults = true;
                        } else {
                            btn.parentElement.style.display = 'none';
                        }
                    });
                    noResultMsg.classList.toggle('d-none', hasResults);
                });

                iconOptions.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const selectedIcon = this.getAttribute('data-value');
                        // 🟢 On détecte dynamiquement la classe de base
                        const baseClass = selectedIcon.startsWith('bi-') ? 'bi' : 'ico';

                        hiddenInput.value = selectedIcon;
                        previewSpan.innerHTML = `<i class="${baseClass} ${selectedIcon} fs-5 me-2 text-primary"></i> ${selectedIcon}`;
                    });
                });

                searchInput.addEventListener('click', e => e.stopPropagation());
            }

            // 3. 🟢 Surcharge de la méthode editItem pour gérer l'icône lors de l'édition
            if (window.advApp) {
                const originalEditItem = window.advApp.editItem.bind(window.advApp);
                window.advApp.editItem = function(item) {
                    if (typeof item === 'string') item = JSON.parse(item);

                    // On appelle le comportement normal (remplissage des champs traduits)
                    originalEditItem(item);

                    // On gère l'icône globale
                    if (item.icon_advmulti) {
                        // 🟢 Même correction ici pour l'édition
                        const baseClass = item.icon_advmulti.startsWith('bi-') ? 'bi' : 'ico';
                        hiddenInput.value = item.icon_advmulti;
                        previewSpan.innerHTML = `<i class="${baseClass} ${item.icon_advmulti} fs-5 me-2 text-primary"></i> ${item.icon_advmulti}`;
                    } else {
                        hiddenInput.value = '';
                        previewSpan.innerHTML = 'Sélectionnez une icône...';
                    }
                };

                // On surcharge aussi addItem pour réinitialiser l'icône
                const originalAddItem = window.advApp.addItem.bind(window.advApp);
                window.advApp.addItem = function() {
                    originalAddItem();
                    hiddenInput.value = '';
                    previewSpan.innerHTML = 'Sélectionnez une icône...';
                };
            }
        });
    </script>
{/literal}
{/block}