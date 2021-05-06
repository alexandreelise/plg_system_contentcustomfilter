<?php
/**
 * ContentCustomFilterHelper
 *
 * @version       1.0.0
 * @package       helper
 * @author        Alexandre ELISÉ <contact@alexandre-elise.fr>
 * @copyright (c) 2009-2021 . Alexandre ELISÉ . Tous droits réservés.
 * @license       GPL-2.0-and-later GNU General Public License v2.0 or later
 * @link          https://coderparlerpartager.fr
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Version;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

/**
 * Class ContentCustomFilterHelper
 */
abstract class ContentCustomFilterHelper
{
	/**
	 * @var \Joomla\Registry\Registry $pluginParams
	 */
	private static $pluginParams;
	
	
	/**
	 * Filter items onContentPrepare
	 * (decouple business logic from the event
	 * and put it in this helper class)
	 *
	 * @param          $item
	 * @param   array  $customFields
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public static function processItem(&$item, array $customFields): bool
	{
		// if no custom fields found stop here
		if (empty($customFields))
		{
			return false;
		}
		
		$app = Factory::getApplication();
		
		// get user defined custom filters to filter custom fields by name
		// usage: ?filterfield[article-test-field]=hello&filterfield[is-done]=1
		$suggestedFilters = $app->input->get('filterfield', [], 'ARRAY');
		
		
		// special uri var to include or exclude items based on the filters above
		$isIncluded = $app->input->getBool('is_included', true);
		
		$includedFilter = ($isIncluded && self::hasCustomFieldInFilters($customFields, $suggestedFilters));
		
		$excludedFilter = (!$isIncluded && !self::hasCustomFieldInFilters($customFields, $suggestedFilters));
		
		if (($includedFilter && !$excludedFilter) || (!$includedFilter && $excludedFilter))
		{
			$currentPluginParams = self::getPluginParams();
			
			//styling enabled
			if (((int) $currentPluginParams->get('enable_article_styling', 1)) === 1)
			{
				//"highlight" filtered items
				if (!empty($item->text))
				{
					if (!empty($item->introtext))
					{
						$item->text = '<div class="contentcustomfilter filtered-item hasTooltip" title="' . Text::_('PLG_SYSTEM_CONTENTCUSTOMFILTER_MATCHING_FILTER_TOOLTIP_TEXT') . '" target="_blank" rel="noopener">' . $item->text . '</div>';
					}
					else
					{
						$item->text = Text::_('PLG_SYSTEM_CONTENTCUSTOMFILTER_MATCHING_FILTER_WITH_NO_INTROTEXT') . ' ' . $item->text;
					}
				}
			}
			
			if (((int) $currentPluginParams->get('enable_collect_filtered_ids', 0)) === 1)
			{
				// collect filtered items ids
				if (!isset($filteredItems))
				{
					$filteredItems = $app->getUserState('plg_system_contentcustomfilter.filtered.items', []);
				}
				
				// prevent duplicate filtered items ids
				if (!in_array($item->id, $filteredItems, true))
				{
					$filteredItems[] = $item->id;
					$app->setUserState('plg_system_contentcustomfilter.filtered.items', $filteredItems);
				}
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
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Does the current item' custom field matches filters in url
	 *
	 * @param   array  $fields
	 * @param   array  $suggestedFilters
	 *
	 * @return bool
	 */
	public static function hasCustomFieldInFilters(array $fields, array $suggestedFilters)
	{
		if (empty($fields))
		{
			return false;
		}
		
		if (empty($suggestedFilters))
		{
			return false;
		}
		
		// fields indexed by id
		$idMapping = self::getCustomFieldsByKey($fields, 'id');
		
		// fields indexed by name
		$nameMapping = self::getCustomFieldsByKey($fields, 'name');
		
		$outcome = false;
		
		foreach ($suggestedFilters as $suggestedFilterKey => $suggestedFilterValue)
		{
			$matchingField = null;
			if (isset($nameMapping[$suggestedFilterKey]))
			{
				$matchingField = $nameMapping[$suggestedFilterKey];
			}
			elseif (isset($idMapping[$suggestedFilterKey]))
			{
				$matchingField = $idMapping[$suggestedFilterKey];
			}
			
			//stop early
			if (!isset($matchingField))
			{
				continue;
			}
			
			if (!isset($matchingField->type))
			{
				continue;
			}
			
			if (!isset($matchingField->rawvalue))
			{
				continue;
			}
			
			if ($matchingField->type === 'color')
			{
				if (is_array($suggestedFilterValue))
				{
					// multiple values case-insensitive match for colors eg: fF00aB
					$outcome = in_array(str_replace('#', '', $matchingField->rawvalue), $suggestedFilterValue, false);
				}
				elseif (is_string($suggestedFilterValue))
				{
					// single value case-insensitive contains pattern of hexadecimal color
					$outcome = (stripos(str_replace('#', '', $matchingField->rawvalue), $suggestedFilterValue) !== false);
					
				}
			}
			elseif ($matchingField->type === 'calendar')
			{
				
				if (is_array($suggestedFilterValue))
				{
					// multiple values exact match comparison between strtotime (unix timestamps)
					$outcome = (in_array(strtotime($matchingField->rawvalue), array_map(function ($item) {
							return strtotime(rawurldecode($item));
						}, $suggestedFilterValue), true));
				}
				elseif (is_string($suggestedFilterValue))
				{
					// single value exact match between two unix timestamps
					$outcome = (strtotime($matchingField->rawvalue) === strtotime(rawurldecode($suggestedFilterValue)));
				}
			}
			else
			{
				if (is_array($suggestedFilterValue))
				{
					//multiple values case-insensitive match for all other custom field types mainly used for text compare
					$outcome = in_array($matchingField->rawvalue, $suggestedFilterValue, false);
				}
				elseif (is_string($suggestedFilterValue))
				{
					// single value case-insensitive contains pattern of text matching
					$outcome = (stripos($matchingField->rawvalue, $suggestedFilterValue) !== false);
				}
			}
			
		}
		
		return $outcome;
	}
	
	/**
	 * @param   string  $name
	 * @param   string  $prefix
	 * @param   bool[]  $options
	 *
	 * @return bool|\JModelLegacy
	 */
	public static function getModel($name = 'Category', $prefix = 'ContentModel', $options = ['ignore_request' => true])
	{
		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_content/models');
		
		return BaseDatabaseModel::getInstance($name, $prefix, $options);
	}
	
	/**
	 * Get all articles with given categoryId if any
	 *
	 * @param   int  $categoryId
	 *
	 * @return array|null
	 */
	public static function getArticlesFromCategoryId(int $categoryId): ?array
	{
		$model = self::getModel();
		
		if (!isset($model))
		{
			return null;
		}
		
		$app = Factory::getApplication();
		
		// Load the parameters. Merge Global and Menu Item params into new object
		$params     = $app->getParams();
		$menuParams = new Registry;
		
		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}
		
		$mergedParams = clone $menuParams;
		$mergedParams->merge($params);
		
		$model->setState('params', $mergedParams);
		$model->setState('category.id', $categoryId);
		
		return $model->getItems();
	}
	
	
	/**
	 * Index custom fields by key
	 *
	 * @param   array   $fields
	 * @param   string  $key
	 *
	 * @return array
	 */
	private static function getCustomFieldsByKey(array $fields, $key)
	{
		return ArrayHelper::pivot($fields, (string) $key);
	}
	
	/**
	 * @param   string  $type
	 * @param   string  $name
	 *
	 * @return \Joomla\Registry\Registry
	 */
	private static function getPluginParams(string $type = 'system', string $name = 'contentcustomfilter'): Registry
	{
		if (!isset(self::$pluginParams))
		{
			self::$pluginParams = (new Registry(PluginHelper::getPlugin($type, $name)->params));
		}
		
		return self::$pluginParams;
	}
}
