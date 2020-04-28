<?php
use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

class StandsModelCatalogs extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'c.id',
                'c.title',
                'search'
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
            ->select("c.id, c.title")
            ->from("#__mkv_stand_catalogs c");
        $search = (!$this->export) ? $this->getState('filter.search') : JFactory::getApplication()->input->getString('search', '');
        if (!empty($search)) {
            if (stripos($search, 'id:') !== false) { //Поиск по ID
                $id = explode(':', $search);
                $id = $id[1];
                if (is_numeric($id)) {
                    $id = $db->q($id);
                    $query->where("c.id = {$id}");
                }
            }
            else {
                $text = $db->q("%{$search}%");
                $query->where("(c.title like {$text})");
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
            $arr['title'] = $item->title;
            $query = [
                'option' => $this->option,
                'view' => 'stands',
                'filter_search' => '',
                'filter_pavilion' => '',
                'filter_type' => '',
                'filter_catalog' => $item->id,
            ];
            $url = JRoute::_("index.php?" . http_build_query($query));
            $arr['edit_link'] = JHtml::link($url, $item->title);
            $result['items'][] = $arr;
        }
        return $result;
    }

    protected function populateState($ordering = 'c.id', $direction = 'desc')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        parent::populateState($ordering, $direction);
        StandsHelper::check_refresh();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        return parent::getStoreId($id);
    }

    private $export;
}
