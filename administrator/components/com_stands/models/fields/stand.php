<?php
defined('_JEXEC') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldStand extends JFormFieldList
{
    protected $type = 'Stand';
    protected $loadExternally = 0;

    protected function getOptions()
    {
        JFactory::getLanguage()->load('com_stands', JPATH_ADMINISTRATOR . "/components/com_stands", 'ru-RU', true);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select("s.id, s.number, s.square")
            ->select("p.title as pavilion")
            ->from("`#__mkv_stands` s")
            ->leftJoin("#__mkv_stand_pavilions p on p.id = s.pavilionID")
            ->order("s.number");
        //Получаем ID стенда или сразу сделки
        $input = JFactory::getApplication()->input;
        $contractStandID = $input->getInt('id', 0);
        JTable::addIncludePath(JPATH_ADMINISTRATOR . "/components/com_contracts/tables");
        if ($contractStandID > 0) {
            $table = JTable::getInstance('Stands', 'TableContracts');
            $table->load($contractStandID);
            $contractID = $table->contractID;
            $standID = $table->standID;
        }
        else {
            $contractID = JFactory::getApplication()->getUserState("com_stands.stand.contractID");
        }
        //Получаем ID проекта
        $table = JTable::getInstance('Contracts', 'TableContracts');
        $table->load($contractID);
        $projectID = $table->projectID;
        //Получаем ID каталога стендов для проекта
        JTable::addIncludePath(JPATH_ADMINISTRATOR . "/components/com_prj/tables");
        $table = JTable::getInstance('Projects', 'TablePrj');
        $table->load($projectID);
        $catalogID = $table->catalogID;
        $query->where("s.catalogID = {$catalogID}");

        //Отсекаем занятые стенды
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . "/components/com_contracts/models", "ContractsModel");
        $model = JModelLegacy::getInstance("StandsLight", "ContractsModel", ['projectID' => $projectID, 'byProjectID' => true]);
        $items = $model->getItems();
        $ids = array_keys($items);
        $ids = implode(', ', $ids);
        if (!empty($ids)) $query->where("s.id not in ({$ids})");
        if ($contractStandID > 0) $query->orWhere("s.id = {$db->q($standID)}");

        $result = $db->setQuery($query)->loadObjectList();

        $options = array();

        foreach ($result as $item) {
            $title = JText::sprintf('COM_STANDS_FIELD_STAND_NUM_SQUARE_PAV', $item->number, $item->square, $item->pavilion);
            $options[] = JHtml::_('select.option', $item->id, $title);
        }

        if (!$this->loadExternally) {
            $options = array_merge(parent::getOptions(), $options);
        }

        return $options;
    }

    public function getOptionsExternally()
    {
        $this->loadExternally = 1;
        return $this->getOptions();
    }
}