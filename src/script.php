<?php
/**
 * script
 *
 * @version       1.0.0
 * @package       script
 * @author        Alexandre ELISÉ <contact@alexandre-elise.fr>
 * @copyright (c) 2009-2021 . Alexandre ELISÉ . Tous droits réservés.
 * @license       GPL-2.0-and-later GNU General Public License v2.0 or later
 * @link          https://coderparlerpartager.fr
 */

use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Installer\Adapter\PluginAdapter;

defined('_JEXEC') or die;

/**
 * ContentCustomFilter script file.
 *
 * @package   contentcustomfilter
 * @since     1.0.0
 */
class PlgSystemContentcustomfilterInstallerScript extends InstallerScript
{
	protected $minimumJoomla = '3.9.26';
	protected $minimumPhp = '7.2.5';
	
	/**
	 * Constructor
	 *
	 * @param   PluginAdapter $adapter  The object responsible for running this script
	 */
	public function __construct(PluginAdapter $adapter) {}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
	 * @param   PluginAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($route, PluginAdapter $adapter) {}

	/**
	 * Called after any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
	 * @param   PluginAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, PluginAdapter $adapter) {}

	/**
	 * Called on installation
	 *
	 * @param   PluginAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(PluginAdapter $adapter) {}

	/**
	 * Called on update
	 *
	 * @param   PluginAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(PluginAdapter $adapter) {}

	/**
	 * Called on uninstallation
	 *
	 * @param   PluginAdapter  $adapter  The object responsible for running this script
	 */
	public function uninstall(PluginAdapter $adapter) {}
}
