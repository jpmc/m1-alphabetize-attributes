<?php
/**
 * Created by PhpStorm.
 * User: jcorrao
 * Date: 10/9/15
 * Time: 5:20 PM
 */

class James_Alphabetize_Model_Observer
{
	public function attributeAlphabetize($observer)
	{
		//get Admin Block
		$container = $observer->getBlock();
		//check if there is a container and it is the type we want
		if(!is_null($container)
		   && strcasecmp($container->getType(),'adminhtml/catalog_product_attribute_edit') == 0)
		{
			//grab attribute ID from Attribute page request .../edit/attribute_id/{$id}
			$attribute_id = Mage::app()->getRequest()->getParam('attribute_id');
			//create button data
			$data = array(
				'label' => 'Alphabetize',
				'class' => 'scalable',
				'onclick' => 'setLocation(\''.Mage::helper("adminhtml")->getUrl("attribute/alphabetize/index/attribute_id/{$attribute_id}").'\')',
			);
			//attach button to container
			$container->addButton('alphabetize_button',$data);
		}
		return $this;
	}
}