<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;

class StandsViewPavilion extends HtmlView {
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
	    JToolBarHelper::apply('pavilion.apply', 'JTOOLBAR_APPLY');
        JToolbarHelper::save('pavilion.save', 'JTOOLBAR_SAVE');
        JToolbarHelper::cancel('pavilion.cancel', 'JTOOLBAR_CLOSE');
        JFactory::getApplication()->input->set('hidemainmenu', true);
    }

    protected function setDocument() {
        $title = ($this->item->id !== null) ? JText::sprintf('COM_STANDS_TITLE_PAVILION_EDIT', $this->item->title) : JText::sprintf('COM_STANDS_TITLE_PAVILION_ADD');
        JToolbarHelper::title($title, 'bookmark');
        JHtml::_('bootstrap.framework');
    }
}