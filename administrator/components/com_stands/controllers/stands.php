<?php
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

class StandsControllerStands extends AdminController
{
    public function getModel($name = 'Stand', $prefix = 'StandsModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}
