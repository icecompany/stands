<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\AdminModel;

class StandsModelStand extends AdminModel {

    public function getItem($pk = null)
    {
        return parent::getItem($pk);
    }

    public function save($data): bool
    {
        $s = parent::save($data);
        if ($s && $data['id'] !== null) $this->updateSquareItems($data['id'], $data['square']);
        return $s;
    }

    public function getTable($name = 'Stands', $prefix = 'TableStands', $options = array())
    {
        return JTable::getInstance($name, $prefix, $options);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm(
            $this->option.'.stand', 'stand', array('control' => 'jform', 'load_data' => $loadData)
        );
        $form->addFieldPath(JPATH_ADMINISTRATOR . "/components/com_prices/models/fields");
        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState($this->option.'.edit.stand.data', array());
        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Обновляет значение площади стенда в заказанных услугах, к которым этот стенд привязан
     * @param int $standID
     * @param float $square
     *
     * @since version 2.0.4
     */
    private function updateSquareItems(int $standID, float $square)
    {
        $ids = $this->getSquareItemIDs($standID);
        if (empty($ids)) return;
        $db = JFactory::getDbo();
        $db->setQuery("set @is_zero = 0")->execute();
        $query = $db->getQuery(true);
        $query
            ->update("#__mkv_contract_items")
            ->set($db->qn('value') . " = " .$db->q($square));
        if (count($ids) > 1) {
            $list = implode(', ', $ids);
            $query->where("id in ({$list})");
        }
        else {
            $query->where("id = {$db->q($ids[0])}");
        }
        $db->setQuery($query)->execute();
        $db->setQuery("set @is_zero = 1")->execute();
    }

    /**
     * Возвращает ID заказанных позиций экспомест, которые нужно поменять в сделках, в которых заказан этот стенд
     * @param int $standID
     *
     * @return array
     *
     * @since version 2.0.4
     */
    private function getSquareItemIDs(int $standID): array
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select("ci.id")
            ->from("#__mkv_contract_stands cs")
            ->leftJoin("#__mkv_contract_items ci on ci.contractStandID = cs.id")
            ->leftJoin("#__mkv_price_items pi on pi.id = ci.itemID")
            ->where("cs.standID = {$db->q($standID)}")
            ->where("pi.square_type in (1, 2, 3, 4, 5, 6)");
        return $db->setQuery($query)->loadColumn() ?? [];
    }

    protected function prepareTable($table)
    {
        $all = get_class_vars($table);
        unset($all['_errors']);
        $nulls = ['typeID']; //Поля, которые NULL
        foreach ($all as $field => $v) {
            if (empty($field)) continue;
            if (in_array($field, $nulls)) {
                if (!strlen($table->$field)) {
                    $table->$field = NULL;
                    continue;
                }
            }
            if (!empty($field)) $table->$field = trim($table->$field);
        }

        parent::prepareTable($table);
    }

    protected function canEditState($record)
    {
        $user = JFactory::getUser();

        if (!empty($record->id))
        {
            return $user->authorise('core.edit.state', $this->option . '.stand.' . (int) $record->id);
        }
        else
        {
            return parent::canEditState($record);
        }
    }

    public function getScript()
    {
        return 'administrator/components/' . $this->option . '/models/forms/stand.js';
    }
}