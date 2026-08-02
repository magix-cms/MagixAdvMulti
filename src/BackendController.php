<?php
declare(strict_types=1);

namespace Plugins\MagixAdvMulti\src;

use App\Backend\Controller\BaseController;
use Plugins\MagixAdvMulti\db\AdvMultiAdminDb;
use Plugins\MagixAdvMulti\src\IconScanner;
use Magepattern\Component\HTTP\Request;
use Magepattern\Component\Tool\SmartyTool;
use Magepattern\Component\Tool\FormTool;
use App\Component\Cache\CacheManager;
use App\Backend\Db\RevisionsDb;

class BackendController extends BaseController
{
    public function run(): void
    {
        SmartyTool::addTemplateDir('advmulti', ROOT_DIR . 'plugins' . DS . 'MagixAdvMulti' . DS . 'views' . DS . 'admin');
        $action = $_GET['action'] ?? 'index';

        if ($action && method_exists($this, $action)) {
            $this->$action();
        } else {
            $this->index();
        }
    }

    private function index(): void
    {
        $db = new AdvMultiAdminDb();
        $icons = IconScanner::getAvailableIcons();

        $this->view->assign([
            'advmulti_module'    => 'home',
            'advmulti_id_module' => 0,
            'available_icons'    => $icons,
            'langs'              => $db->fetchLanguages(),
            'hashtoken'          => $this->session->getToken()
        ]);

        $this->view->display('config.tpl');
    }

    private function loadList(): void
    {
        if (ob_get_length()) ob_clean();

        $module = $_GET['module'] ?? '';
        $idModule = (int)($_GET['id_module'] ?? 0);
        $idLang = (int)($this->defaultLang['id_lang'] ?? 1);

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
            'icon_advmulti' => ['title' => 'Icône', 'type' => 'text', 'class' => 'text-center text-primary fs-5', 'width' => '80px'],
            'title_advmulti' => ['title' => 'Titre', 'type' => 'text', 'class' => 'fw-bold text-dark'],
            'published_advmulti' => ['title' => 'Statut', 'type' => 'status', 'class' => 'text-center', 'width' => '120px']
        ];

        $this->view->assign([
            'advmulti_items' => $items,
            'ajax_columns'   => $columns,
            'module'         => $module,
            'id_module'      => $idModule,
            'hashtoken'      => $this->session->getToken(),
            'langs'          => $db->fetchLanguages()
        ]);

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
        $revDb = new RevisionsDb(); // Instance pour les révisions

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
                $finalId = $idAdv;
            } else {
                $finalId = $idAdv;
                $db->updateStructure($idAdv, ['icon_advmulti' => $icon]);
            }

            if (isset($_POST['title_advmulti']) && is_array($_POST['title_advmulti'])) {
                foreach ($_POST['title_advmulti'] as $idLang => $title) {
                    $cleanTitle = FormTool::simpleClean($title);

                    if (!empty($cleanTitle)) {
                        $desc = $_POST['desc_advmulti'][$idLang] ?? '';

                        $db->saveContent($finalId, (int)$idLang, [
                            'title_advmulti'     => $cleanTitle,
                            'desc_advmulti'      => $desc,
                            'url_advmulti'       => FormTool::simpleClean($_POST['url_advmulti'][$idLang] ?? ''),
                            'blank_advmulti'     => (int)($_POST['blank_advmulti'][$idLang] ?? 0),
                            'published_advmulti' => (int)($_POST['published_advmulti'][$idLang] ?? 0)
                        ]);

                        // 🟢 ENREGISTREMENT DE LA RÉVISION
                        if (!empty($desc)) {
                            $revDb->saveRevision('magixadvmulti', $finalId, (int)$idLang, 'desc_advmulti', $desc);
                        }
                    }
                }
            }

            // 🟢 PURGE DU CACHE
            CacheManager::clearFrontend('magixadvmulti');

            // On indique au JS si c'était un ajout ou une mise à jour
            $isAdd = ((int)($_POST['id_advmulti'] ?? 0) === 0);
            $this->jsonResponse(true, 'Point fort enregistré avec succès.', ['type' => $isAdd ? 'add' : 'update']);

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
            if ($db->deleteItem($idAdv)) {
                CacheManager::clearFrontend('magixadvmulti');
                $this->jsonResponse(true, 'Supprimé avec succès.');
            }
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
            if ($db->updateOrder($orderedIds)) {
                CacheManager::clearFrontend('magixadvmulti');
                $this->jsonResponse(true, 'Ordre mis à jour.');
            }
        }
        $this->jsonResponse(false, 'Erreur lors du tri.');
    }
}