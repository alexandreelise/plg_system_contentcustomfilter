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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;

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
		
		$isCategory   = false;
		$customFields = [];
		switch ($context)
		{
			case 'com_content.categories':
				$isCategory = true;
				break;
			case 'com_content.category':
			case 'com_content.featured':
			case 'com_content.archive':
			case 'com_content.article':
			case 'com_content.articles':
				$customFields = FieldsHelper::getFields('com_content.article', $item, false);
				break;
			default:
				break;
		}
		
		if ($isCategory)
		{
			$articles = ContentCustomFilterHelper::getArticlesFromCategoryId((int) $this->app->input->getInt('id', $item->id ?? 0));
			
			// if no articles in category stop here
			if (!isset($articles))
			{
				return true;
			}
			
			foreach ($articles as $article)
			{
				$customFields = FieldsHelper::getFields('com_content.article', $article, false);
				//TODO: find a way to handle category list articles for now it's not working as expected
				ContentCustomFilterHelper::processItem($article, $customFields);
			}
		}
		else
		{
			ContentCustomFilterHelper::processItem($item, $customFields);
		}
		
		return true;
	}
	
	
	public function onBeforeCompileHead()
	{
		if ($this->app->isClient('site'))
		{
			if (((int) ($this->params->get('enable_article_styling', 1))) === 1)
			{
				HTMLHelper::_('stylesheet', 'plg_system_contentcustomfilter/style.css', ['relative' => true, 'version' => 'auto']);
			}
		}
	}
}
