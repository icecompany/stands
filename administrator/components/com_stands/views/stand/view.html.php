<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;

class StandsViewStand extends HtmlView {
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
	    JToolBarHelper::apply('stand.apply', 'JTOOLBAR_APPLY');
        JToolbarHelper::save('stand.save', 'JTOOLBAR_SAVE');
        JToolbarHelper::cancel('stand.cancel', 'JTOOLBAR_CLOSE');
        JFactory::getApplication()->input->set('hidemainmenu', true);
    }

    protected function setDocument() {
        $title = ($this->item->id !== null) ? JText::sprintf('COM_STANDS_TITLE_STAND_EDIT', $this->item->title) : JText::sprintf('COM_STANDS_TITLE_STAND_ADD');
        JToolbarHelper::title($title, 'cube');
        JHtml::_('bootstrap.framework');
    }
}