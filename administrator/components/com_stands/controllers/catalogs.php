<?php
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

class StandsControllerCatalogs extends AdminController
{
    public function getModel($name = 'Catalog', $prefix = 'StandsModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}
