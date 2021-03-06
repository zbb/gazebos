<?php
/**
 * @version     1.0.0
 * @package     com_gazebos
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Don Gilbert <don@electriceasel.com> - http://www.electriceasel.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldSpecifications extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'specifications';

	protected $multiple = true;

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$this->prepareDocument();
		$registry = new JRegistry($this->value);
		$specs = $registry->toArray();
		$html = array();

		$html[] = '<div id="options_div">';
		$html[] = '<p>Add product specs below. The first box is the title, the second box is the description.</p>';

		$total = count($specs['title']);

		for ($i = 0; $i < $total; $i++)
		{
			// Make sure there is always at least one element.
			$html[] = '<div class="specHolder" style="width:300px;">';
				$html[] = '<div style="display:block;width:300px">';
					$html[] = '<input type="text" value="'.htmlentities($specs['title'][$i]).'" style="width:300px" name="jform[specifications][title][]" />';
				$html[] = '</div>';
				$html[] = '<div style="display:block;width:300px">';
					$html[] = '<textarea style="width:300px;height:100px;resize:none;" name="jform[specifications][value][]">'.htmlentities($specs['value'][$i]).'</textarea>';
				$html[] = '</div>';
				$html[] = '<div style="display:block;width:300px">';
					$html[] = '<button class="button quicknavAdd" name="+" type="button" onclick="javascript:addOption(this);">Add</button>';
					$html[] = '<button class="button" name="-" type="button" onclick="javascript:removeOption(this);">Remove</button>';
				$html[] = '</div>';
				$html[] = '<div style="clear:both"></div>';
			$html[] = '</div>';
		}

			// Make sure there is always at least one element.
			$html[] = '<div class="specHolder" style="width:300px;">';
				$html[] = '<div style="display:block;width:300px">';
					$html[] = '<input type="text" value="" style="width:300px" name="jform[specifications][title][]" />';
				$html[] = '</div>';
				$html[] = '<div style="display:block;width:300px">';
					$html[] = '<textarea style="width:300px;height:100px;resize:none;" name="jform[specifications][value][]"></textarea>';
				$html[] = '</div>';
				$html[] = '<div style="display:block;width:300px">';
					$html[] = '<button class="button quicknavAdd" name="+" type="button" onclick="javascript:addOption(this);">Add</button>';
					$html[] = '<button class="button" name="-" type="button" onclick="javascript:removeOption(this);">Remove</button>';
				$html[] = '</div>';
				$html[] = '<div style="clear:both"></div>';
			$html[] = '</div>';

		// Close the options_div
		$html[] = '</div>';

		return implode($html);
	}

	protected function prepareDocument()
	{
		JFactory::getDocument()
			->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js')
			->addScriptDeclaration('
			// <![CDATA[
			var addOption = function (el) {
			    var option_field = $(el).parent().parent();
			    var new_option_field = option_field.clone();
			    $(new_option_field).find("input").val("");
			    $(option_field).after(new_option_field);
			}
				
			var removeOption = function (el) {
			    $(el).parent().parent().remove();
			}
			// ]]>
			');
	}

	public function getLabel()
	{
		return null;
	}
}
