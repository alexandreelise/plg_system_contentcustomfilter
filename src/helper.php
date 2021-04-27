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

use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

/**
 * Class ContentCustomFilterHelper
 */
abstract class ContentCustomFilterHelper
{
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
		
		$nameMapping = self::getCustomFieldsByKey($fields, 'name');
		
		$outcome = true;
		
		foreach ($suggestedFilters as $suggestedFilterName => $suggestedFilterValue)
		{
			if (!isset($nameMapping[$suggestedFilterName]))
			{
				continue;
			}
			$matchingField = $nameMapping[$suggestedFilterName];
			
			
			switch ($matchingField->type)
			{
				case 'color':
					$outcome = ($outcome && (str_replace('#', '',$matchingField->rawvalue) === $suggestedFilterValue));
					break;
				case 'calendar':
					$outcome = ($outcome && (strtotime($matchingField->rawvalue) === strtotime(rawurldecode($suggestedFilterValue))));
					break;
				default:
					$outcome = ($outcome && ($matchingField->rawvalue === $suggestedFilterValue));
					break;
			}
		}
		
		return $outcome;
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
	 * Prepare field data for easier filtering
	 *
	 * @param   array  $fields
	 *
	 * @return array
	 */
	private static function prepareMinimalFilterData(array $fields)
	{
		$customFieldsByName = self::getCustomFieldsByKey($fields, 'name');
		
		return array_combine(array_keys($customFieldsByName), array_column($customFieldsByName, 'rawvalue'));
	}
}
