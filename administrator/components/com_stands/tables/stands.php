<?php
/**
 * @package    stands
 *
 * @author     Антон <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Stands table.
 *
 * @package   stands
 * @since     1.0.0
 */
class TableStands extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database driver object.
	 *
	 * @since   1.0.0
	 */
	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__stands_items', 'item_id', $db);
	}
}