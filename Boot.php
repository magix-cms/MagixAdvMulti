<?php
declare(strict_types=1);

namespace Plugins\MagixAdvMulti;

use App\Component\Hook\HookManager;
use Magepattern\Component\Tool\SmartyTool;
use Plugins\MagixAdvMulti\src\FrontendController;
use Plugins\MagixAdvMulti\src\IconScanner; //  N'oubliez pas d'importer le scanner

class Boot
{
    private array $targetModules = [
        'product'  => 'id_product',
        'pages'    => 'id_pages',
        'category' => 'id_cat',
        'news'     => 'id_news',
        'about'    => 'id_about'
    ];

    public function register(): void
    {
        // ==========================================
        // 1. HOOKS BACKEND (Administration) - ON GARDE !
        // ==========================================
        foreach ($this->targetModules as $module => $idKey) {

            // Le bouton d'onglet
            HookManager::register("{$module}_edit_tab", 'MagixAdvMulti', function(array $params) use ($module) {
                $smarty = SmartyTool::getInstance('admin');
                $file = ROOT_DIR . 'plugins' . DS . 'MagixAdvMulti' . DS . 'views' . DS . 'admin' . DS . 'hooks' . DS . 'tab_button.tpl';
                return $smarty->templateExists($file) ? $smarty->fetch($file) : '';
            });

            // Le contenu de l'onglet
            HookManager::register("{$module}_edit_content", 'MagixAdvMulti', function(array $params) use ($module, $idKey) {
                $smarty = SmartyTool::getInstance('admin');
                $idModule = (int)($params[$idKey] ?? 0);

                $icons = IconScanner::getAvailableIcons();

                $smarty->assign([
                    'advmulti_module'    => $module,
                    'advmulti_id_module' => $idModule,
                    'available_icons'    => $icons
                ]);

                $file = ROOT_DIR . 'plugins' . DS . 'MagixAdvMulti' . DS . 'views' . DS . 'admin' . DS . 'hooks' . DS . 'tab_content.tpl';
                return $smarty->templateExists($file) ? $smarty->fetch($file) : '';
            });
        }

        // ==========================================
        // 2. HOOKS FRONTEND (Côté public)
        // ==========================================
        /*HookManager::register('displayPageBottom', 'MagixAdvMulti', function(array $params) {
            return FrontendController::renderWidget($params, 'pages', 'id_pages');
        });

        HookManager::register('displayProductExtraContent', 'MagixAdvMulti', function(array $params) {
            return FrontendController::renderWidget($params, 'product', 'id_product');
        });

        //  Ajout du Hook spécifique pour la page d'accueil
        HookManager::register('displayHomeBottom', 'MagixAdvMulti', function(array $params) {
            return FrontendController::renderWidget($params, 'home', 'id_home'); // id_home sera 0
        });*/
    }
}