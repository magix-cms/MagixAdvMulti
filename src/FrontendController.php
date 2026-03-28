<?php
declare(strict_types=1);

namespace Plugins\MagixAdvMulti\src;

use Plugins\MagixAdvMulti\db\AdvMultiFrontDb;
use Magepattern\Component\Tool\SmartyTool;

class FrontendController
{
    public static function renderWidget(array $params, string $module, string $idKey): string
    {
        try {
            $view = SmartyTool::getInstance('front');

            $langData = $view->getTemplateVars('current_lang') ?: $view->getTemplateVars('lang') ?: ['id_lang' => 1];
            $idLang = (int)($langData['id_lang'] ?? 1);

            // Pour 'home', idKey sera 'id_home' et n'existera pas dans $params, ce qui renverra 0 (le comportement voulu !)
            $id = (int)($params[$idKey] ?? 0);

            $db = new AdvMultiFrontDb();
            $items = $db->getPublishedItems($module, $id, $idLang);

            if (empty($items)) return '';

            $template = ROOT_DIR . 'plugins' . DS . 'MagixAdvMulti' . DS . 'views' . DS . 'front' . DS . 'widget.tpl';

            if (!file_exists($template)) return '';

            return $view->fetch($template, [
                'magix_advmulti_data' => [
                    'module' => $module,
                    'items'  => $items
                ]
            ]);

        } catch (\Throwable $e) {
            return "";
        }
    }
}