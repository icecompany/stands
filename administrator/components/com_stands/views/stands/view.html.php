<?php
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

class StandsViewStands extends HtmlView
{
    protected $sidebar = '';
    public $items, $pagination, $uid, $state, $filterForm, $activeFilters;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        $this->filterForm->addFieldPath(JPATH_ADMINISTRATOR . "/components/com_prices/models/fields");

        // Show the toolbar
        $this->toolbar();

        // Show the sidebar
        StandsHelper::addSubmenu('stands');
        $this->sidebar = JHtmlSidebar::render();

        // Display it all
        return parent::display($tpl);
    }

    private function toolbar()
    {
        JToolBarHelper::title(JText::sprintf('COM_STANDS_MENU_STANDS'), 'cube');

        if (StandsHelper::canDo('core.add'))
        {
            JToolbarHelper::addNew('stand.add');
        }
        if (StandsHelper::canDo('core.edit'))
        {
            JToolbarHelper::editList('stand.edit');
        }
        if (StandsHelper::canDo('core.delete'))
        {
            JToolbarHelper::deleteList('COM_STANDS_CONFIRM_REMOVE_STAND', 'stands.delete');
        }
        if (StandsHelper::canDo('core.admin'))
        {
            JToolBarHelper::preferences('com_stands');
        }
    }
}
