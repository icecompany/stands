<?php
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

class StandsControllerPavilions extends AdminController
{
    public function getModel($name = 'Pavilion', $prefix = 'StandsModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}
