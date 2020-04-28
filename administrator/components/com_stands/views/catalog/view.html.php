<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;

class StandsViewCatalog extends HtmlView {
    protected $item, $form, $script;

    public function display($tmp = null) {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->script = $this->get('Script');

        $this->addToolbar();
        $this->setDocument();

        parent::display($tmp);
    }

    protected function addToolbar() {
	    JToolBarHelper::apply('catalog.apply', 'JTOOLBAR_APPLY');
        JToolbarHelper::save('catalog.save', 'JTOOLBAR_SAVE');
        JToolbarHelper::cancel('catalog.cancel', 'JTOOLBAR_CLOSE');
        JFactory::getApplication()->input->set('hidemainmenu', true);
    }

    protected function setDocument() {
        $title = ($this->item->id !== null) ? JText::sprintf('COM_STANDS_TITLE_CATALOG_EDIT', $this->item->title) : JText::sprintf('COM_STANDS_TITLE_CATALOG_ADD');
        JToolbarHelper::title($title, 'bookmark');
        JHtml::_('bootstrap.framework');
    }
}