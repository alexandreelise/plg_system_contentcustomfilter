<?php
/**
 * Content Custom Filter
 * Custom fields filter plugin using Joomla! core features.
 *
 * @version       1.0.0
 * @package       contentcustomfilter
 * @author        Alexandre ELISÉ <contact@alexandre-elise.fr>
 * @copyright (c) 2009-2021 . Alexandre ELISÉ . Tous droits réservés.
 * @license       GPL-2.0-and-later GNU General Public License v2.0 or later
 * @link          https://coderparlerpartager.fr
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Version;
use Joomla\Registry\Registry;

JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
JLoader::register('ContentCustomFilterHelper', __DIR__ . '/helper.php');

/**
 * Class PlgSystemContentCustomFilter
 */
class PlgSystemContentcustomfilter extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.7.0
	 */
	protected $autoloadLanguage = true;
	
	/**
	 * @var \Joomla\CMS\Application\CMSApplication $app
	 */
	protected $app;
	
	/**
	 * @var \JDatabaseDriver $db
	 */
	protected $db;
	
	/**
	 * List of "allowed" contexts to filter by revelant custom fields
	 *
	 * @var string[] $allowedContexts
	 */
	private static $allowedContexts = [
		'com_content.archive',
		'com_content.article',
		'com_content.articles',
		'com_content.categories',
		'com_content.category',
		'com_content.featured',
	];
	
	
	/**
	 * @param $context
	 * @param $item
	 * @param $params
	 * @param $page
	 *
	 * @return bool
	 */
	public function onContentPrepare($context, &$item, &$params, $page)
	{
		if (!$this->app->isClient('site'))
		{
			return true;
		}
		
		if (!in_array($context, self::$allowedContexts, true))
		{
			return true;
		}
		
		switch ($context)
		{
			case 'com_content.categories':
			case 'com_content.category':
				$customFields = FieldsHelper::getFields('com_content.category', $item, false);
				break;
			case 'com_content.featured':
			case 'com_content.archive':
			case 'com_content.article':
			case 'com_content.articles':
				$customFields = FieldsHelper::getFields('com_content.article', $item, false);
				break;
			default:
				$customFields = [];
				break;
		}
		
		// if no custom fields found stop here
		if (empty($customFields))
		{
			return true;
		}
		
		// get user defined custom filters to filter custom fields by name
		// usage: ?filterfield[article-test-field]=hello&filterfield[is-done]=1
		$suggestedFilters = $this->app->input->get('filterfield', [], 'ARRAY');
		
		// special uri var to include or exclude items based on the filters above
		$isIncluded = $this->app->input->getBool('is_included', true);
		
		
		$includedFilter = ($isIncluded && ContentCustomFilterHelper::hasCustomFieldInFilters($customFields, $suggestedFilters));
		
		$excludedFilter = (!$isIncluded && !ContentCustomFilterHelper::hasCustomFieldInFilters($customFields, $suggestedFilters));
		
		$view  = $this->app->input->getCmd('view');
		$model = null;
		if (null !== $view)
		{
			BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_content/models');
			
			$model = BaseDatabaseModel::getInstance(ucfirst($view), 'ContentModel', ['ignore_request' => true]);
		}
		
		
		if (($includedFilter && !$excludedFilter) || ($excludedFilter && !$includedFilter))
		{
			
			//$dom = (new DOMDocument());
			
			
			//"highlight" filtered items
			if (!empty($item->introtext))
			{
				$item->text = '<div class="contentcustomfilter filtered-item hasTooltip" title="This content matches the filters you provided" target="_blank" rel="noopener">' . $item->text . '</div>';
			}
			else
			{
				$item->text = '[Filtered] ' . $item->text;
			}
			
			// collect filtered items ids
			if (!isset($filteredItems))
			{
				$filteredItems = $this->app->getUserState('plg_system_contentcustomfilter.filtered.items', []);
			}
			
			// prevent duplicate filtered items ids
			if (!in_array($item->id, $filteredItems, true))
			{
				$filteredItems[] = $item->id;
				$this->app->setUserState('plg_system_contentcustomfilter.filtered.items', $filteredItems);
			}
		}
		else
		{
			if (empty($suggestedFilters))
			{
				return true;
			}
			
			$isJ4 = (Version::MAJOR_VERSION === 4);
			
			$nullDate           = ($isJ4 ? null : Factory::getDbo()->getNullDate());
			$item               = new stdClass();
			$item->params       = new Registry();
			$item->event        = new stdClass();
			$item->text         = '';
			$item->publish_down = $nullDate;
			$item->publish_up   = $nullDate;
			$item->state        = 1;
			$item->images       = '';
			$item->urls         = '';
		}
		
		return true;
	}
	
	public function onBeforeCompileHead()
	{
		if ($this->app->isClient('site'))
		{
			HTMLHelper::_('stylesheet', 'plg_system_contentcustomfilter/style.css', ['relative' => true, 'auto' => true]);
		}
	}
}
