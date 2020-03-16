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
                'c.title',
                'p.title',
                't.title',
                'search',
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
            ->select("s.id, s.square, s.number")
            ->select("c.title as catalog")
            ->select("p.title as pavilion")
            ->select("t.title as type")
            ->from("`#__mkv_stands` s")
            ->leftJoin("#__mkv_stand_catalogs c on c.id = s.catalogID")
            ->leftJoin("#__mkv_stand_pavilions p on p.id = s.pavilionID")
            ->leftJoin("#__mkv_stand_square_types t on t.id = s.typeID");
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

        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        $this->setState('list.limit', $limit);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = array();
        foreach ($items as $item) {
            $arr = ['items' => []];
            $arr['id'] = $item->id;
            $arr['number'] = $item->number;
            $arr['pavilion'] = $item->pavilion;
            $arr['type'] = $item->type;
            $arr['catalog'] = $item->catalog;
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
        parent::populateState($ordering, $direction);
        StandsHelper::check_refresh();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.catalog');
        $id .= ':' . $this->getState('filter.pavilion');
        $id .= ':' . $this->getState('filter.type');
        return parent::getStoreId($id);
    }

    private $export;
}
