<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modeladmin');

class PhocaPanoramaCpModelPhocaPanoramaItem extends JModelAdmin
{
	protected	$option 		= 'com_phocapanorama';
	protected 	$text_prefix	= 'com_phocapanorama';
    public $typeAlias 			= 'com_phocapanorama.phocapanoramaitem';

	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		if (!empty($record->catid)) {
			return $user->authorise('core.delete', 'com_phocapanorama.phocapanoramaitem.'.(int) $record->catid);
		} else {
			return parent::canDelete($record);
		}
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		if (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_phocapanorama.phocapanoramaitem.'.(int) $record->catid);
		} else {
			return parent::canEditState($record);
		}
	}

	public function getTable($type = 'PhocaPanoramaItem', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		$app	= JFactory::getApplication();
		$form 	= $this->loadForm('com_phocapanorama.phocapanoramaitem', 'phocapanoramaitem', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_phocapanorama.edit.phocapanorama.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

		public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			// Convert the params field to an array.
			//$registry = new JRegistry;
			//$registry->loadString($item->metadata);
			//$item->metadata = $registry->toArray();
		}

		return $item;
	}

	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias		= JApplicationHelper::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias = JApplicationHelper::stringURLSafe($table->title);
		}

		if (empty($table->id)) {
			// Set the values
			//$table->created	= $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = JFactory::getDbo();

				$db->setQuery('SELECT MAX(ordering) FROM #__phocapanorama_items WHERE catid = '.(int)$table->catid);
				$max = $db->loadResult();

				$table->ordering = $max+1;
			}
		}
		else {
			// Set the values
			//$table->modified	= $date->toSql();
			//$table->modified_by	= $user->get('id');
		}
	}



	protected function getReorderConditions($table = null)
	{
		$condition = array();
		$condition[] = 'catid = '. (int) $table->catid;
		//$condition[] = 'state >= 0';
		return $condition;
	}



	protected function batchCopy($value, $pks, $contexts)
	{
		$categoryId	= (int) $value;

		$table	= $this->getTable();
		$db		= $this->getDbo();

		// Check that the category exists
		if ($categoryId) {
			$categoryTable = JTable::getInstance('PhocaPanoramaCat', 'Table');
			if (!$categoryTable->load($categoryId)) {
				if ($error = $categoryTable->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				}
				else {
					$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
					return false;
				}
			}
		}

		if (empty($categoryId)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
			return false;
		}

		// Check that the user has create permission for the component
		$extension	= JFactory::getApplication()->input->getCmd('option');
		$user		= JFactory::getUser();
		if (!$user->authorise('core.create', $extension)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
			return false;
		}

		$i		= 0;

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$table->reset();

			// Check that the row actually exists
			if (!$table->load($pk)) {
				if ($error = $table->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				}
				else {
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Alter the title & alias
			$data = $this->generateNewTitle($categoryId, $table->alias, $table->title);
			$table->title   = $data['0'];
			$table->alias   = $data['1'];

			// Reset the ID because we are making a copy
			$table->id		= 0;

			// New category ID
			$table->catid	= $categoryId;

			// Ordering
			$table->ordering = $this->increaseOrdering($categoryId);

			$table->hits = 0;

			// Check the row.
			if (!$table->check()) {
				$this->setError($table->getError());
				return false;
			}

			// Store the row.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}

			$newId = $table->get('id');

			// Add the new ID to the array
			//$newIds[$i]	= $newId;
			$newIds[$pk]	= $newId;
			$i++;
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	protected function batchMove($value, $pks, $contexts)
	{
		$categoryId	= (int) $value;

		$table	= $this->getTable();
		//$db		= $this->getDbo();

		// Check that the category exists
		if ($categoryId) {
			$categoryTable = JTable::getInstance('PhocaPanoramaCat', 'Table');
			if (!$categoryTable->load($categoryId)) {
				if ($error = $categoryTable->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				}
				else {
					$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
					return false;
				}
			}
		}

		if (empty($categoryId)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
			return false;
		}

		// Check that user has create and edit permission for the component
		$extension	= JFactory::getApplication()->input->getCmd('option');
		$user		= JFactory::getUser();
		if (!$user->authorise('core.create', $extension)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
			return false;
		}

		if (!$user->authorise('core.edit', $extension)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
			return false;
		}

		// Parent exists so we let's proceed
		foreach ($pks as $pk)
		{
			// Check that the row actually exists
			if (!$table->load($pk)) {
				if ($error = $table->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				}
				else {
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Set the new category ID
			$table->catid = $categoryId;

			// Check the row.
			if (!$table->check()) {
				$this->setError($table->getError());
				return false;
			}

			// Store the row.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}


	public function increaseOrdering($categoryId) {

		$ordering = 1;
		$this->_db->setQuery('SELECT MAX(ordering) FROM #__phocapanorama_items WHERE catid='.(int)$categoryId);
		$max = $this->_db->loadResult();
		$ordering = $max + 1;
		return $ordering;
	}
}
?>
