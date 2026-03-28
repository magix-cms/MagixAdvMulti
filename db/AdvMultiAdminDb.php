<?php
declare(strict_types=1);

namespace Plugins\MagixAdvMulti\db;

use App\Backend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class AdvMultiAdminDb extends BaseDb
{
    public function getItemsByModule(string $module, int $idModule, int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select([
            'a.id_advmulti',
            'a.icon_advmulti', // L'icône
            'a.module_advmulti',
            'a.id_module',
            'a.order_advmulti',
            'ac.title_advmulti',
            'ac.desc_advmulti',
            'ac.url_advmulti', // Le lien
            'ac.blank_advmulti', // Ouverture
            'ac.published_advmulti'
        ])
            ->from('mc_plug_advmulti', 'a')
            ->join('mc_plug_advmulti_content', 'ac', 'a.id_advmulti = ac.id_advmulti')
            ->where('a.module_advmulti = :module AND a.id_module = :id_module AND ac.id_lang = :id_lang', [
                'module'    => $module,
                'id_module' => $idModule,
                'id_lang'   => $idLang
            ])
            ->orderBy('a.order_advmulti', 'ASC');

        return $this->executeAll($qb) ?: [];
    }

    public function getItemById(int $idAdv): array|false
    {
        // 1. Structure
        $qb = new QueryBuilder();
        $qb->select('*')->from('mc_plug_advmulti')->where('id_advmulti = :id', ['id' => $idAdv]);
        $item = $this->executeRow($qb);

        if (!$item) return false;

        // 2. Traductions
        $qbContent = new QueryBuilder();
        $qbContent->select('*')->from('mc_plug_advmulti_content')->where('id_advmulti = :id', ['id' => $idAdv]);
        $contents = $this->executeAll($qbContent);

        $item['content'] = [];
        if ($contents) {
            foreach ($contents as $c) {
                $item['content'][$c['id_lang']] = $c;
            }
        }

        return $item;
    }

    public function insertStructure(array $data): int|false
    {
        $qbCount = new QueryBuilder();
        $qbCount->select(['COUNT(id_advmulti) as total'])
            ->from('mc_plug_advmulti')
            ->where('module_advmulti = :module AND id_module = :id', [
                'module' => $data['module_advmulti'],
                'id'     => $data['id_module']
            ]);

        $countResult = $this->executeRow($qbCount);
        $data['order_advmulti'] = (int)($countResult['total'] ?? 0);

        $qb = new QueryBuilder();
        $qb->insert('mc_plug_advmulti', $data);

        return $this->executeInsert($qb) ? (int)$this->getLastInsertId() : false;
    }

    // 🟢 MISE À JOUR DE LA STRUCTURE (Pour l'icône)
    public function updateStructure(int $id, array $data): bool
    {
        $qb = new QueryBuilder();
        $qb->update('mc_plug_advmulti', $data)->where('id_advmulti = :id', ['id' => $id]);
        return $this->executeUpdate($qb);
    }

    public function saveContent(int $idAdv, int $idLang, array $data): bool
    {
        $qbCheck = new QueryBuilder();
        $qbCheck->select(['id_advmulti'])->from('mc_plug_advmulti_content')
            ->where('id_advmulti = :adv AND id_lang = :lang', ['adv' => $idAdv, 'lang' => $idLang]);

        $exists = $this->executeRow($qbCheck);
        $qb = new QueryBuilder();

        if ($exists) {
            $qb->update('mc_plug_advmulti_content', $data)
                ->where('id_advmulti = :adv AND id_lang = :lang', ['adv' => $idAdv, 'lang' => $idLang]);
            return $this->executeUpdate($qb);
        } else {
            $data['id_advmulti'] = $idAdv;
            $data['id_lang']     = $idLang;
            $qb->insert('mc_plug_advmulti_content', $data);
            return $this->executeInsert($qb);
        }
    }

    public function deleteItem(int $idAdv): bool
    {
        $qb2 = new QueryBuilder();
        $qb2->delete('mc_plug_advmulti_content')->where('id_advmulti = :id', ['id' => $idAdv]);
        $res2 = $this->executeDelete($qb2);

        $qb1 = new QueryBuilder();
        $qb1->delete('mc_plug_advmulti')->where('id_advmulti = :id', ['id' => $idAdv]);
        $res1 = $this->executeDelete($qb1);

        return $res1 && $res2;
    }

    public function updateOrder(array $orderedIds): bool
    {
        $success = true;
        foreach ($orderedIds as $index => $id) {
            $qb = new QueryBuilder();
            $qb->update('mc_plug_advmulti', ['order_advmulti' => $index + 1])
                ->where('id_advmulti = :id', ['id' => (int)$id]);

            if (!$this->executeUpdate($qb)) {
                $success = false;
            }
        }
        return $success;
    }
}