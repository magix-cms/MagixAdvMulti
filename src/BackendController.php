<?php
declare(strict_types=1);

namespace Plugins\MagixAdvMulti\src;

use App\Backend\Controller\BaseController;
use Plugins\MagixAdvMulti\db\AdvMultiAdminDb;
use Plugins\MagixAdvMulti\src\IconScanner;
use Magepattern\Component\HTTP\Request;
use Magepattern\Component\Tool\SmartyTool;
use Magepattern\Component\Tool\FormTool;

class BackendController extends BaseController
{
    public function run(): void
    {
        // 🟢 Déclaration du dossier de vues pour Smarty
        SmartyTool::addTemplateDir('advmulti', ROOT_DIR . 'plugins' . DS . 'MagixAdvMulti' . DS . 'views' . DS . 'admin');

        $action = $_GET['action'] ?? 'index';

        // 🟢 ROUTAGE
        if ($action && method_exists($this, $action)) {
            $this->$action();
        } else {
            $this->index();
        }
    }

    /**
     * ========================================================================
     * 1. MODE STANDALONE : Affichage de la page de configuration du plugin
     * ========================================================================
     */
    private function index(): void
    {
        $db = new AdvMultiAdminDb();

        // 1. On scanne les icônes pour le formulaire
        $icons = IconScanner::getAvailableIcons();

        // 2. On prépare les variables spécifiques pour la page d'accueil (module: home, id: 0)
        $this->view->assign([
            'advmulti_module'    => 'home',
            'advmulti_id_module' => 0,
            'available_icons'    => $icons,
            'langs'              => $db->fetchLanguages(),
            'hashtoken'          => $this->session->getToken()
        ]);

        // 3. Affichage de la vue globale qui inclura le tab_content.tpl
        // Attention : On utilise la méthode classique de votre CMS pour afficher une page de plugin complète
        $this->view->display('config.tpl');
    }

    /**
     * ========================================================================
     * 2. MODE AJAX : Gestion des points forts (utilisé par l'index ET les onglets)
     * ========================================================================
     */
    private function loadList(): void
    {
        if (ob_get_length()) ob_clean();

        $module = $_GET['module'] ?? '';
        $idModule = (int)($_GET['id_module'] ?? 0);
        $idLang = (int)($this->defaultLang['id_lang'] ?? 1);

        // Si on est sur l'accueil, $module = 'home' et $idModule = 0.
        if (empty($module)) {
            echo '<div class="alert alert-warning">Paramètres manquants pour charger les points forts.</div>';
            return;
        }

        $db = new AdvMultiAdminDb();
        $items = $db->getItemsByModule($module, $idModule, $idLang);

        foreach ($items as &$item) {
            $fullData = $db->getItemById((int)$item['id_advmulti']);
            $item['content'] = $fullData['content'] ?? [];
        }

        $columns = [
            'icon_advmulti' => [
                'title' => 'Icône',
                'type'  => 'text',
                'class' => 'text-center text-primary fs-5',
                'width' => '80px'
            ],
            'title_advmulti' => [
                'title' => 'Titre',
                'type'  => 'text',
                'class' => 'fw-bold text-dark'
            ],
            'published_advmulti' => [
                'title' => 'Statut',
                'type'  => 'status',
                'class' => 'text-center',
                'width' => '120px'
            ]
        ];

        $this->view->assign([
            'advmulti_items' => $items,
            'ajax_columns'   => $columns,
            'module'         => $module,
            'id_module'      => $idModule,
            'hashtoken'      => $this->session->getToken(),
            'langs'          => $db->fetchLanguages()
        ]);

        // On affiche le petit tableau AJAX
        $this->view->display('ajax/manager.tpl');
    }

    private function save(): void
    {
        if (ob_get_length()) ob_clean();

        $token = Request::isPost('hashtoken') ? $_POST['hashtoken'] : '';
        if (!$this->session->validateToken($token)) {
            $this->jsonResponse(false, 'Session expirée ou jeton invalide.');
        }

        $idAdv    = (int)($_POST['id_advmulti'] ?? 0);
        $itemType = FormTool::simpleClean($_POST['module_advmulti'] ?? 'home');
        $itemId   = (int)($_POST['id_module'] ?? 0);
        $icon     = FormTool::simpleClean($_POST['icon_advmulti'] ?? '');

        if (empty($itemType)) {
            $this->jsonResponse(false, 'Les références du module sont obligatoires.');
        }

        $db = new AdvMultiAdminDb();

        try {
            if ($idAdv === 0) {
                $idAdv = $db->insertStructure([
                    'module_advmulti' => $itemType,
                    'id_module'       => $itemId,
                    'icon_advmulti'   => $icon
                ]);

                if (!$idAdv) {
                    $this->jsonResponse(false, 'Erreur lors de la création de la structure.');
                }
            } else {
                // Mise à jour de l'icône de la structure globale existante
                $db->updateStructure($idAdv, ['icon_advmulti' => $icon]);
            }

            // Sauvegarde des traductions (lien, titre, etc.)
            if (isset($_POST['title_advmulti']) && is_array($_POST['title_advmulti'])) {
                foreach ($_POST['title_advmulti'] as $idLang => $title) {
                    $cleanTitle = FormTool::simpleClean($title);

                    if (!empty($cleanTitle)) {
                        $db->saveContent($idAdv, (int)$idLang, [
                            'title_advmulti'     => $cleanTitle,
                            'desc_advmulti'      => $_POST['desc_advmulti'][$idLang] ?? '',
                            'url_advmulti'       => FormTool::simpleClean($_POST['url_advmulti'][$idLang] ?? ''),
                            'blank_advmulti'     => isset($_POST['blank_advmulti'][$idLang]) ? 1 : 0,
                            'published_advmulti' => isset($_POST['published_advmulti'][$idLang]) ? 1 : 0
                        ]);
                    }
                }
            }

            $this->jsonResponse(true, 'Point fort enregistré avec succès.');

        } catch (\Exception $e) {
            $this->jsonResponse(false, 'Erreur serveur : ' . $e->getMessage());
        }
    }

    private function delete(): void
    {
        if (ob_get_length()) ob_clean();
        $token = Request::isPost('hashtoken') ? $_POST['hashtoken'] : '';
        if (!$this->session->validateToken($token)) $this->jsonResponse(false, 'Session expirée.');

        $idAdv = (int)($_POST['id_advmulti'] ?? 0);
        if ($idAdv > 0) {
            $db = new AdvMultiAdminDb();
            if ($db->deleteItem($idAdv)) $this->jsonResponse(true, 'Supprimé avec succès.');
        }
        $this->jsonResponse(false, 'Impossible de supprimer.');
    }

    private function reorder(): void
    {
        if (ob_get_length()) ob_clean();
        $token = Request::isPost('hashtoken') ? $_POST['hashtoken'] : '';
        if (!$this->session->validateToken($token)) $this->jsonResponse(false, 'Session expirée.');

        $orderedIds = $_POST['ids'] ?? [];
        if (!empty($orderedIds) && is_array($orderedIds)) {
            $db = new AdvMultiAdminDb();
            if ($db->updateOrder($orderedIds)) $this->jsonResponse(true, 'Ordre mis à jour.');
        }
        $this->jsonResponse(false, 'Erreur lors du tri.');
    }
}