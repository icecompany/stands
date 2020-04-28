<?php
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

class TableStandsCatalogs extends Table
{
    var $id = null;
    var $title = null;
	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__mkv_stand_catalogs', 'id', $db);
	}
}