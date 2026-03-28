{if isset($magix_advmulti_data) && !empty($magix_advmulti_data.items)}

{* 🟢 1. DICTIONNAIRE DYNAMIQUE DES LAYOUTS
   On associe chaque module de votre manifest.json à un style précis ('top' ou 'left').
*}
{assign var="layoutMap" value=[
    'home'     => 'top',
    'product'  => 'left',
    'pages'    => 'left',
    'category' => 'top'
]}

{* 🟢 2. RÉCUPÉRATION DU BON LAYOUT
   On cherche dans le tableau la valeur correspondante au module actuel.
   Si le module n'est pas dans le tableau, on applique 'left' par défaut.
*}
{assign var="layout" value=$layoutMap[$magix_advmulti_data.module]|default:'left'}
{* ==========================================================
🎨 AFFICHAGE DE LA GRILLE
========================================================== *}
<div id="advmulti-{$magix_advmulti_data.module}" class="magix-advmulti-widget py-5">
    <div class="container">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
            {foreach $magix_advmulti_data.items as $item}
                {assign var="isBi" value=$item.icon_advmulti|strpos:'bi-' === 0}
                {assign var="baseIconClass" value="{if $isBi}bi{else}ico{/if}"}

                <div class="col d-flex">
                    {* 🟢 LES CLASSES MAGIQUES SONT ICI :
                    - `advmulti-card` : active les animations au survol
                    - `layout-icon-{$layout}` : applique le design (top ou left)
                    - `position-relative` : obligatoire pour rendre toute la carte cliquable
                    *}
                    {* La classe layout-icon-{$layout} s'adaptera toute seule ! *}
                    <div class="card advmulti-card layout-icon-{$layout} position-relative h-100 w-100 border-0 shadow-sm">

                        {if !empty($item.icon_advmulti)}
                            <div class="card-icon">
                                <i class="{$baseIconClass} {$item.icon_advmulti}{* text-primary*}"></i>
                            </div>
                        {/if}

                        <div class="card-body">
                            <h3 class="h5 card-title fw-bold mb-2">{$item.title_advmulti}</h3>

                            {if !empty($item.desc_advmulti)}
                                <div class="card-text text-muted small">
                                    {$item.desc_advmulti|default:'' nofilter}
                                </div>
                            {/if}

                            {if !empty($item.url_advmulti)}
                                <a href="{$item.url_advmulti}"
                                   class="stretched-link"
                                   {if $item.blank_advmulti}target="_blank" rel="noopener noreferrer"{/if}>
                                    <span class="visually-hidden">En savoir plus sur {$item.title_advmulti}</span>
                                </a>
                            {/if}
                        </div>

                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>
{/if}