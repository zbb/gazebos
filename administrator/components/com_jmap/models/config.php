<?php
// namespace administrator\components\com_jmap\models;
/**
 *
 * @package JMAP::CONFIG::administrator::components::com_jmap
 * @subpackage models
 * @author Joomla! Extensions Store
 * @copyright (C) 2013 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelform');

/**
 * Config model responsibilities
 *
 * @package JMAP::CONFIG::administrator::components::com_jmap
 * @subpackage models
 * @since 1.0
 */
interface IConfigModel {

	/**
	 * Ottiene i dati di configurazione da db params field record component
	 *
	 * @access public
	 * @return Object
	 */
	public function &getData();

	/**
	 * Effettua lo store dell'entity config
	 *
	 * @access public
	 * @return boolean
	 */
	public function storeEntity();
}

/**
 * Config model concrete implementation
 *
 * @package JMAP::CONFIG::administrator::components::com_jmap
 * @subpackage models
 * @since 1.0
 */
class JMapModelConfig extends JModelForm implements IConfigModel {
	/**
	 * Clean the cache
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 * @return  void
	 * @since   11.1
	 */
	private function cleanComponentCache($group = null, $client_id = 0) {
		// Initialise variables;
		$conf = JFactory::getConfig();
		$dispatcher = JDispatcher::getInstance();

		$options = array('defaultgroup' => ($group) ? $group : JRequest::getCmd('option'), 'cachebase' => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'));

		$cache = JCache::getInstance('callback', $options);
		$cache->clean();

		// Trigger the onContentCleanCache event.
		$dispatcher->trigger('onContentCleanCache', $options);
	}

	/**
	 * Ottiene i dati di configurazione da db params field record component
	 *
	 * @access public
	 * @return Object
	 */
	private function &getConfigData() {
		$instance = JComponentHelper::getParams('com_jmap');
		return $instance;
	}

	/**
	 * Effettua lo storing dell'asset delle permissions sul component level
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function storePermissionsAsset($data) {  
		// Save the rules.
		if (isset($data['jform']) && isset($data['jform']['rules'])) {
			$form = $this->getForm($data);
			// Validate the posted data.
			$postedRules = $this->validate($form, $data['jform']);
			
			$rules = new JAccessRules($postedRules['rules']);
			$asset = JTable::getInstance('asset');

			if (!$asset->loadByName($data['option'])) {
				$root = JTable::getInstance('asset');
				$root->loadByName('root.1');
				$asset->name = $data['option'];
				$asset->title = $data['option'];
				$asset->setLocation($root->id, 'last-child');
			}
			$asset->rules = (string) $rules;

			if (!$asset->check() || !$asset->store()) {
				$this->setError($asset->getError());
				return false;
			} 
		}
  
		return true;
	}
	
	/**
	 * Method to get a form object.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		jimport('joomla.form.form');
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_jmap');

		// Get the form.
		$form = $this->loadForm('com_jmap.component', 'config', array('control' => 'jform', 'load_data' => $loadData), false, '/config');

		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Ottiene i dati di configurazione del componente
	 *
	 * @access public
	 * @return Object
	 */
	public function &getData() {
		return $this->getConfigData();
	}

	/**
	 * Effettua lo store dell'entity config
	 *
	 * @access public
	 * @return boolean
	 */
	public function storeEntity() {
		$table = &JTable::getInstance('extension');

		if (!$idComponente = $table->find(array('element' => 'com_jmap'))) {
			JError::raiseWarning(500, 'Not a valid component');
			return false;
		} else {
			$table->load($idComponente);
		}

		$post = JRequest::get('post');
		$post['params'] = json_encode($post['jform']);
		$table->bind($post);

		// pre-save checks
		if (!$table->check()) {
			JError::raiseWarning(500, $table->getError());
			return false;
		}

		// save the changes
		if (!$table->store()) {
			JError::raiseWarning(500, $table->getError());
			return false;
		}
		
		// save the changes
		if (!$this->storePermissionsAsset($post)) {
			JError::raiseWarning(500, JTEXT::_('ERROR_STORING_PERMISSIONS'), $this->getError());
			return false;
		}

		// Clean the cache.
		$this->cleanComponentCache('_system', 0);
		$this->cleanComponentCache('_system', 1);
		return true;
	}
}
?>