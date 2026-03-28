<input type="hidden" id="adv_hashtoken" value="{$hashtoken}">
<input type="hidden" id="adv_module" value="{$module}">
<input type="hidden" id="adv_id_module" value="{$id_module}">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 text-gray-800"><i class="bi bi-stars me-2 text-warning"></i>Liste des points forts</h5>
    <button type="button" class="btn btn-primary btn-sm" onclick="advApp.addItem()">
        <i class="bi bi-plus-lg me-1"></i> Ajouter un point fort
    </button>
</div>

{include file="components/ajax-table.tpl"
data=$advmulti_items
id_key="id_advmulti"
columns=$ajax_columns
sortable=true
edit_action="advApp.editItem"
delete_action="advApp.deleteItem"
empty_msg="Aucun point fort n'a été créé pour cet élément."}