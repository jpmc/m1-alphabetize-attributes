<?php
/**
 * Created by PhpStorm.
 * User: jcorrao
 * Date: 10/9/15
 * Time: 6:04 PM
 */

class James_Alphabetize_AlphabetizeController extends Mage_Adminhtml_Controller_Action {

	public function indexAction()
	{
		//grab session, to set messages
		$session = Mage::getSingleton('adminhtml/session');
		//grab and check for the attribute ID
		$attribute_id =  $this->getRequest()->getParam('attribute_id');
		if(is_null($attribute_id) || empty($attribute_id))
		{
			$session->addError('No attribute ID provided.');
		}
		//alphabetize attribute provided
		$helper = Mage::helper('james_alphabetize');
		if($helper->alphabetizeAttribute($attribute_id))
		{
			$session->addSuccess('Successfully alphabetized attribute options!');
		}
		else
		{
			$session->addError('Could not alphabetize attribute. Please check \'alphabetize.log\' in your log directory for additional details.');
		}
		//redirect to attribute page with any of the messages set above
		$this->_redirectReferer();
	}
}