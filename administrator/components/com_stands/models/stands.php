<?php
use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

class StandsModelStands extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                's.id',
                's.number',
                's.square',
                's.open',
                'c.title',
                'p.title',
                't.title',
                'search',
                'open',
                'item',
                'catalog',
                'pavilion',
                'type',
            );
        }
        parent::__construct($config);
        $input = JFactory::getApplication()->input;
        $this->export = ($input->getString('format', 'html') === 'html') ? false : true;
    }

    protected function _getListQuery()
    {
        $db = $this->_db;
        $query = $db->getQuery(true);

        /* Сортировка */
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');

        //Ограничение длины списка
        $limit = (!$this->export) ? $this->getState('list.limit') : 0;

        $query
            ->select("s.id, s.square, s.number, s.open")
            ->select("c.title as catalog")
            ->select("p.title as pavilion")
            ->select("t.title as type")
            ->select("pi.title as item")
            ->from("`#__mkv_stands` s")
            ->leftJoin("#__mkv_stand_catalogs c on c.id = s.catalogID")
            ->leftJoin("#__mkv_projects prj on prj.catalogID = c.id")
            ->leftJoin("#__mkv_stand_pavilions p on p.id = s.pavilionID")
            ->leftJoin("#__mkv_stand_square_types t on t.id = s.typeID")
            ->leftJoin("#__mkv_price_items pi on pi.id = s.itemID");
        $search = (!$this->export) ? $this->getState('filter.search') : JFactory::getApplication()->input->getString('search', '');
        if (!empty($search)) {
            if (stripos($search, 'id:') !== false) { //Поиск по ID
                $id = explode(':', $search);
                $id = $id[1];
                if (is_numeric($id)) {
                    $id = $db->q($id);
                    $query->where("s.id = {$id}");
                }
            }
            else {
                $text = $db->q("%{$search}%");
                $query->where("(s.number like {$text})");
            }
        }
        $project = PrjHelper::getActiveProject();
        if (is_numeric($project)) {
            $query->where("prj.id = {$this->_db->q($project)}");
        }
        $catalog = $this->getState('filter.catalog');
        if (is_numeric($catalog)) {
            $query->where("s.catalogID = {$this->_db->q($catalog)}");
        }
        $pavilion = $this->getState('filter.pavilion');
        if (is_numeric($pavilion)) {
            $query->where("s.pavilionID = {$this->_db->q($pavilion)}");
        }
        $type = $this->getState('filter.type');
        if (is_numeric($type)) {
            $query->where("s.typeID = {$this->_db->q($type)}");
        }
        $open = $this->getState('filter.open');
        if (is_numeric($open)) {
            if ($open != -1) {
                $query->where("s.open = {$this->_db->q($open)}");
            }
            else {
                $query->where("s.open is null");
            }
        }
        $item = $this->getState('filter.item');
        if (is_numeric($item)) {
            if ($item != -1) {
                $query->where("s.itemID = {$this->_db->q($item)}");
            }
            else {
                $query->where("s.itemID is null");
            }
        }

        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        $this->setState('list.limit', $limit);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = array();
        foreach ($items as $item) {
            $arr = [];
            $arr['id'] = $item->id;
            $arr['number'] = $item->number;
            $arr['pavilion'] = $item->pavilion;
            $arr['type'] = $item->type;
            $arr['catalog'] = $item->catalog;
            $arr['open'] = $item->open;
            $arr['item'] = $item->item;
            $arr['square'] = JText::sprintf("COM_STANDS_SQUARE_SQM", $item->square);
            $url = JRoute::_("index.php?option={$this->option}&amp;task=stand.edit&amp;id={$item->id}");
            $arr['edit_link'] = JHtml::link($url, $item->number);
            $result['items'][] = $arr;
        }
        return $result;
    }

    protected function populateState($ordering = 's.number', $direction = 'asc')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $catalog = $this->getUserStateFromRequest($this->context . '.filter.catalog', 'filter_catalog');
        $this->setState('filter.catalog', $catalog);
        $pavilion = $this->getUserStateFromRequest($this->context . '.filter.pavilion', 'filter_pavilion');
        $this->setState('filter.pavilion', $pavilion);
        $type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type');
        $this->setState('filter.type', $type);
        $open = $this->getUserStateFromRequest($this->context . '.filter.open', 'filter_open');
        $this->setState('filter.open', $open);
        $item = $this->getUserStateFromRequest($this->context . '.filter.item', 'filter_item');
        $this->setState('filter.item', $item);
        parent::populateState($ordering, $direction);
        StandsHelper::check_refresh();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.catalog');
        $id .= ':' . $this->getState('filter.pavilion');
        $id .= ':' . $this->getState('filter.type');
        $id .= ':' . $this->getState('filter.open');
        $id .= ':' . $this->getState('filter.item');
        return parent::getStoreId($id);
    }

    private $export;
}
