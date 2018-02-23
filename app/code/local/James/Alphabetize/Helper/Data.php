<?php
/**
 * Created by PhpStorm.
 * User: jcorrao
 * Date: 10/9/15
 * Time: 5:12 PM
 */ 
class James_Alphabetize_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * @param int|string $attrId Attribute ID to alphabetize
	 * Alphabetizes option values using the Position field
	 * @return bool Success or Failure
	 * @throws Mage_Core_Exception
	 */
	function alphabetizeAttribute($attrId)
	{
		//get connection and attribute
		$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
		$attr = Mage::getModel('eav/entity_attribute')->load($attrId);
		//check if attribute exists
		if(!$attr->getId())
		{
			Mage::log('Attribute ID does not exist: '.$attrId,null,'alphabetize.log');
			return false;
		}
		//get options, then check if any options
		$_options = $attr->getSource()->getAllOptions(false);
		if(count($_options) == 0)
		{
			Mage::log('There were no options to alphabetize on Attribute ID#'.$attrId,null,'alphabetize.log');
			return false;
		}
		//sort options by label
		usort($_options, array($this,'sortCallback'));
		//attribute option table, with prefixes or other Magento formatting/translation
		$attribute_option_table = Mage::getSingleton('core/resource')->getTableName('eav_attribute_option');
		$conn->beginTransaction();
		try{
			//update the sorting position of each attribute option value
			foreach($_options as $k=>$o)
			{
				$_fields = array( 'sort_order' => $k+1 );
				$_where = $conn->quoteInto('option_id =?', $o['value']);
				$conn->update($attribute_option_table, $_fields,$_where);
			}
			//commit results at once
			$conn->commit();
			return true;
		}
		catch(Exception $x)
		{
			//rollback transaction in case of Exception
			$conn->rollback();
			Mage::log($x->getMessage().PHP_EOL.$x->getTraceAsString(),null,'alphabetize.log');
			return false;
		}
	}
	/**
	 * @param array $option1 First option tuple
	 * @param array $option2 Second option tuple
	 * @param bool $keepThePrefix boolean to keep leading "The " in a string
	 * See $this->stripDown() to understand $keepThePrefix
	 * @return int
	 */
	private function sortCallback($option1, $option2, $keepThePrefix = true)
	{
		$a = $keepThePrefix?$option1['label']:$this->stripDown($option1['label']);
		$b = $keepThePrefix?$option2['label']:$this->stripDown($option2['label']);
		if (strcasecmp($a, $b) == 0)
			{ return 0; }
		else if (strcasecmp($a, $b) < 0)
			{ return -1; }
		else
			{ return 1; }
	}
	/**
	 * @param $string String to alter
	 * Trims leading "The" from names, and then sets to lowercase
	 * Used in sorting titles. Such as movies.
	 * Ex. "The Hulk" would be a movie grouped with "H" titles alphabetically
	 * @return string
	 */
	private function stripDown($string)
	{ return strtolower(str_ireplace('the ', '', $string)); }
}