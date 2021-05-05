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
}
