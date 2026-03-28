<?php
declare(strict_types=1);

namespace Plugins\MagixAdvMulti\db;

use App\Frontend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class AdvMultiFrontDb extends BaseDb
{
    public function getPublishedItems(string $module, int $idModule, int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select([
            'a.id_advmulti',
            'a.icon_advmulti',
            'ac.title_advmulti',
            'ac.desc_advmulti',
            'ac.url_advmulti',
            'ac.blank_advmulti'
        ])
            ->from('mc_plug_advmulti', 'a')
            ->join('mc_plug_advmulti_content', 'ac', 'a.id_advmulti = ac.id_advmulti')
            ->where('a.module_advmulti = :module', ['module' => $module])
            ->where('a.id_module = :id_module', ['id_module' => $idModule])
            ->where('ac.id_lang = :id_lang', ['id_lang' => $idLang])
            ->where('ac.published_advmulti = 1')
            ->orderBy('a.order_advmulti', 'ASC');

        return $this->executeAll($qb) ?: [];
    }
}