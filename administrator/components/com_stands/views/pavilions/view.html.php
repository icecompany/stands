<?php
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

class StandsViewPavilions extends HtmlView
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

        // Show the toolbar
        $this->toolbar();

        // Show the sidebar
        StandsHelper::addSubmenu('pavilions');
        $this->sidebar = JHtmlSidebar::render();

        // Display it all
        return parent::display($tpl);
    }

    private function toolbar()
    {
        JToolBarHelper::title(JText::sprintf('COM_STANDS_MENU_CATALOGS'), 'compass');

        if (StandsHelper::canDo('core.create'))
        {
            JToolbarHelper::addNew('pavilion.add');
        }
        if (StandsHelper::canDo('core.edit'))
        {
            JToolbarHelper::editList('pavilion.edit');
        }
        if (StandsHelper::canDo('core.delete'))
        {
            JToolbarHelper::deleteList('COM_STANDS_CONFIRM_REMOVE_CATALOG', 'pavilions.delete');
        }
        if (StandsHelper::canDo('core.admin'))
        {
            JToolBarHelper::preferences('com_stands');
        }
    }
}
