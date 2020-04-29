<?php
use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

class StandsModelPavilions extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'p.id',
                'p.title',
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
            ->select("p.id, p.title")
            ->from("#__mkv_stand_pavilions p");
        $search = (!$this->export) ? $this->getState('filter.search') : JFactory::getApplication()->input->getString('search', '');
        if (!empty($search)) {
            if (stripos($search, 'id:') !== false) { //Поиск по ID
                $id = explode(':', $search);
                $id = $id[1];
                if (is_numeric($id)) {
                    $id = $db->q($id);
                    $query->where("p.id = {$id}");
                }
            }
            else {
                $text = $db->q("%{$search}%");
                $query->where("(p.title like {$text})");
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
                'filter_pavilion' => $item->id,
                'filter_type' => '',
                'filter_catalog' => '',
            ];
            $url = JRoute::_("index.php?" . http_build_query($query));
            $arr['edit_link'] = JHtml::link($url, $item->title);
            $result['items'][] = $arr;
        }
        return $result;
    }

    protected function populateState($ordering = 'p.id', $direction = 'desc')
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
