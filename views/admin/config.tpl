{extends file="layout.tpl"}

{block name='head:title'}Points Forts - Accueil{/block}

{block name='article'}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-stars me-2 text-warning"></i> Gestion des Points Forts
        </h1>
    </div>

    <div class="alert alert-info border-0 shadow-sm mb-4">
        <i class="bi bi-info-circle-fill me-2"></i>
        Les éléments que vous gérez ici s'afficheront de manière globale sur la page d'accueil de votre site web.
    </div>

    {*  ASTUCE : On crée un "faux" onglet caché.
       Cela empêche le constructeur MagixAjaxManager de planter s'il cherche l'ID de l'onglet par défaut. *}
    <div id="magix-advmulti-tab" class="d-none"></div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            {* Inclusion de notre formulaire AJAX universel *}
            {include file="hooks/tab_content.tpl"}

        </div>
    </div>
{/block}

{block name="javascripts" append}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Nettoyage de l'UI (on retire l'aspect onglet)
            const innerPane = document.getElementById('magix-advmulti-pane');
            if (innerPane) {
                innerPane.classList.remove('tab-pane', 'fade', 'mt-3');
                innerPane.classList.add('show', 'active');

                const innerCard = innerPane.querySelector('.card');
                if (innerCard) {
                    innerCard.classList.remove('card', 'shadow-sm', 'border-0');
                }
            }

            // 2.  DÉCLENCHEMENT MANUEL DE L'AJAX
            // Au lieu d'attendre un clic sur un onglet, on force le chargement de la liste
            if (typeof window.advApp !== 'undefined') {
                // Selon la version de votre MagixAjaxManager, la méthode pour charger la liste
                // s'appelle généralement loadList() ou showList()
                if (typeof window.advApp.loadList === 'function') {
                    window.advApp.loadList();
                } else if (typeof window.advApp.showList === 'function') {
                    window.advApp.showList();
                }
            }
        });
    </script>
{/block}