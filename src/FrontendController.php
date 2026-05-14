<?php
declare(strict_types=1);

namespace Plugins\MagixAdvMulti\src;

use Plugins\MagixAdvMulti\db\AdvMultiFrontDb;
use Magepattern\Component\Tool\SmartyTool;
use App\Component\Db\PluginDb;

class FrontendController
{
    // Mini-cache statique pour ne faire la requête SQL qu'une seule fois par page
    private static array $targetsCache = [];

    public static function renderWidget(array $params = []): string
    {
        // 1. Initialisation du coupe-circuit
        if (empty(self::$targetsCache)) {
            $pluginDb = new PluginDb();
            self::$targetsCache = $pluginDb->getPluginTargets('MagixAdvMulti');
        }

        // Si le plugin est totalement introuvable ou désactivé, on ne rend rien
        if (empty(self::$targetsCache)) {
            return '';
        }

        $hookName = $params['name'] ?? '';

        // 2. Contexte Accueil ou Footer
        if (str_starts_with($hookName, 'displayHome') || str_starts_with($hookName, 'displayFooter')) {
            // Si 'home' = 0, on bloque !
            if (empty(self::$targetsCache['home'])) return '';

            return self::processRender($params, 'home', 'id_home');
        }

        // 3. Contexte Produit
        if ($hookName === 'displayProductExtraContent') {
            if (empty(self::$targetsCache['product'])) return '';
            return self::processRender($params, 'product', 'id_product');
        }

        // 4. Contexte Page CMS
        if ($hookName === 'displayPageBottom') {
            if (empty(self::$targetsCache['pages'])) return '';
            return self::processRender($params, 'pages', 'id_pages');
        }

        // 5. Contexte Catégorie
        if ($hookName === 'displayCategoryBottom') {
            if (empty(self::$targetsCache['category'])) return '';
            return self::processRender($params, 'category', 'id_cat');
        }

        return '';
    }

    /**
     * Votre méthode métier (inchangée)
     */
    private static function processRender(array $params, string $module, string $idKey): string
    {
        try {
            $view = SmartyTool::getInstance('front');

            $langData = $view->getTemplateVars('current_lang') ?: $view->getTemplateVars('lang') ?: ['id_lang' => 1];
            $idLang = (int)($langData['id_lang'] ?? 1);

            $id = (int)($params[$idKey] ?? 0);

            $db = new AdvMultiFrontDb();
            $items = $db->getPublishedItems($module, $id, $idLang);

            if (empty($items)) return '';

            $template = ROOT_DIR . 'plugins' . DS . 'MagixAdvMulti' . DS . 'views' . DS . 'front' . DS . 'widget.tpl';

            if (!file_exists($template)) return '';

            return $view->fetch($template, [
                'magix_advmulti_data' => [
                    'items'  => $items,
                    'module' => $module,
                    'id'     => $id
                ]
            ]);
        } catch (\Throwable $e) {
            return '';
        }
    }
}