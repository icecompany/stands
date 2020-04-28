<?php
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

class TableStandsStands extends Table
{
    var $id = null;
    var $catalogID = null;
    var $pavilionID = null;
    var $typeID = null;
    var $square = null;
    var $number = null;
    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__mkv_stands', 'id', $db);
    }
}