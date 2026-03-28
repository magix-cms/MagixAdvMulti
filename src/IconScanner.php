<?php
declare(strict_types=1);

namespace Plugins\MagixAdvMulti\src;

use App\Backend\Db\ThemeDb;

class IconScanner
{
    /**
     * Scanne le fichier d'icônes compilé du thème actif
     */
    public static function getAvailableIcons(): array
    {
        $icons = [];

        // 1. Récupération dynamique du thème courant
        try {
            $themeDb = new ThemeDb();
            $activeTheme = $themeDb->getCurrentTheme();
        } catch (\Throwable $e) {
            // Fallback de sécurité si la DB est indisponible
            $activeTheme = 'default';
        }

        // 2. Chemin vers votre fichier CSS dédié aux icônes
        $iconFile = ROOT_DIR . 'skin' . DS . $activeTheme . DS . 'css' . DS . 'icons.css';

        // 3. Scan du fichier s'il existe
        if (file_exists($iconFile)) {
            $content = file_get_contents($iconFile);

            // 🟢 REGEX : Cherche les classes .bi-xxx::before ou .ico-xxx::before (selon vos préfixes)
            // On remplace [a-zA-Z0-9\-] par [\w\-] qui inclut automatiquement les underscores
            if (preg_match_all('/\.((?:bi|ico|icon)-[\w\-]+)::?before/', $content, $matches)) {                foreach ($matches[1] as $iconClass) {
                    $icons[] = $iconClass;
                }
            }
        } else {
            // Mode Debug optionnel : vous pouvez logger ici si le fichier icons.css manque
            // error_log("MagixAdvMulti: Le fichier $iconFile est introuvable.");
        }

        // 4. Nettoyage et tri
        if (!empty($icons)) {
            $icons = array_unique($icons);
            sort($icons);
        }

        return $icons;
    }
}