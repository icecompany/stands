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

        JTable::addIncludePath(JPATH_ADMINISTRATOR . "/components/com_prj/tables");
        $table = JTable::getInstance('Projects', 'TablePrj');
        $project = PrjHelper::getActiveProject();
        if (is_numeric($project)) {
            $table->load($project);
            if (is_numeric($table->catalogID)) {
                $query->where("s.catalogID = {$table->catalogID}");
            }
        }

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