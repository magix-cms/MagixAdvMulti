<?php
declare(strict_types=1);

namespace Plugins\MagixAdvMulti;

use App\Component\Hook\HookManager;
use App\Component\Db\PluginDb;
use Magepattern\Component\Tool\SmartyTool;
use Plugins\MagixAdvMulti\src\IconScanner;

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
        // 1. Récupération des cibles via la classe globale du CMS
        $pluginDb = new PluginDb();
        $activeTargets = $pluginDb->getPluginTargets('MagixAdvMulti');

        // Si le plugin n'est pas trouvé ou inactif, on coupe tout immédiatement
        if (empty($activeTargets)) {
            return;
        }

        // ==========================================
        // 2. HOOKS BACKEND (Onglets d'Administration)
        // ==========================================
        foreach ($this->targetModules as $module => $idKey) {

            if (isset($activeTargets[$module]) && $activeTargets[$module] == 1) {

                HookManager::register("{$module}_edit_tab", 'MagixAdvMulti', function(array $params) use ($module) {
                    $smarty = SmartyTool::getInstance('admin');
                    $file = ROOT_DIR . 'plugins' . DS . 'MagixAdvMulti' . DS . 'views' . DS . 'admin' . DS . 'hooks' . DS . 'tab_button.tpl';
                    return $smarty->templateExists($file) ? $smarty->fetch($file) : '';
                });

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
        }

        // ==========================================
        // 3. HOOKS FRONTEND (Côté public)
        // Note : Commentés car gérés nativement par le Layout Manager du CMS
        // via le manifest.json (type: hybrid/widget) et interceptés par le FrontendController
        // ==========================================

        /*
        if (isset($activeTargets['home']) && $activeTargets['home'] == 1) {
            HookManager::register('displayHomeBottom', 'MagixAdvMulti', function(array $params) {
                return FrontendController::renderWidget($params, 'home', 'id_home');
            });
        }

        if (isset($activeTargets['pages']) && $activeTargets['pages'] == 1) {
            HookManager::register('displayPageBottom', 'MagixAdvMulti', function(array $params) {
                return FrontendController::renderWidget($params, 'pages', 'id_pages');
            });
        }

        if (isset($activeTargets['product']) && $activeTargets['product'] == 1) {
            HookManager::register('displayProductExtraContent', 'MagixAdvMulti', function(array $params) {
                return FrontendController::renderWidget($params, 'product', 'id_product');
            });
        }

        if (isset($activeTargets['category']) && $activeTargets['category'] == 1) {
            HookManager::register('displayCategoryBottom', 'MagixAdvMulti', function(array $params) {
                return FrontendController::renderWidget($params, 'category', 'id_cat');
            });
        }


        // ==========================================
        // ANCIENS HOOKS FRONTEND (Côté public)
        // ==========================================
        HookManager::register('displayPageBottom', 'MagixAdvMulti', function(array $params) {
            return FrontendController::renderWidget($params, 'pages', 'id_pages');
        });

        HookManager::register('displayProductExtraContent', 'MagixAdvMulti', function(array $params) {
            return FrontendController::renderWidget($params, 'product', 'id_product');
        });

        //  Ajout du Hook spécifique pour la page d'accueil
        HookManager::register('displayHomeBottom', 'MagixAdvMulti', function(array $params) {
            return FrontendController::renderWidget($params, 'home', 'id_home'); // id_home sera 0
        });
        */
    }
}