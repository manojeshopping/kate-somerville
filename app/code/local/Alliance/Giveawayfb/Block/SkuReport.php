<?php



class Alliance_Giveawayfb_Block_SkuReport extends Mage_Core_Block_Template
{
	
	function getSkuReport()
	{
		$collection = Mage::getModel('giveawayfb/giveawayfb')->getCollection()
			->addFieldToFilter('order_id', array('notnull' => true))
			->addFieldToFilter('samplekit', array('notnull' => true))
		;
		$collection->getSelect()
			->reset('columns')
			->columns(new Zend_Db_Expr("samplekit"))
			->columns(new Zend_Db_Expr("COUNT(*) AS total"))
			->group('samplekit')
		;
		
		return $collection;
	}
	
	
	function getTotalQueueOrders()
	{
		$collection = Mage::getModel('giveawayfb/giveawayfb')->getCollection()
			->addFieldToFilter('order_id', array('null' => true))
			->addFieldToFilter('customer_id', array('notnull' => true))
		;
		
		return $collection->count();
	}
	
	function getReport()
	{
		// Get data.
		$registers = $this->getRegisterPerday();
		$confirmed = $this->getConfirmedPerDay();
		$orders = $this->getOrdersPerDay();
		
		// Get initial day.
		$dayInitial = key($registers);
		$dayInitialTime = strtotime($dayInitial);
		
		// Get end day.
		end($orders);
		$dayEndOrders = key($orders);
		end($registers);
		$dayEndRegisters = key($registers);
		$dayEnd = ($dayEndOrders > $dayEndRegisters) ? $dayEndOrders : $dayEndRegisters;
		
		// Get diff.
		$dayDiff = (strtotime($dayEnd) - $dayInitialTime) / 86400;
		
		$report = array();
		for($i = 0; $i <= $dayDiff; $i++) {
			$date = date('Y-m-d', ($dayInitialTime + ($i * 86400)));
			
			$report[$date] = array(
				'register' => (isset($registers[$date]) ? $registers[$date] : 0),
				'confirmed' => (isset($confirmed[$date]) ? $confirmed[$date] : 0),
				'order' => (isset($orders[$date]) ? $orders[$date] : 0),
			);
		}
		
		return $report;
	}
	
	function getRegisterPerday()
	{
		$collection = Mage::getModel('giveawayfb/giveawayfb')->getCollection()
			->addFieldToFilter('register_creation', array('gt' => 0))
		;
		$collection->getSelect()
			->reset('columns')
			->columns(new Zend_Db_Expr("DATE(register_creation) AS register_day"))
			->columns(new Zend_Db_Expr("COUNT(*) AS total"))
			->group('register_day')
		;
		
		// Made array with totals.
		$registers = array();
		foreach($collection as $_register) {
			$registers[$_register->getRegisterDay()] = $_register->getTotal();
		}
		
		return $registers;
	}
	
	function getConfirmedPerDay()
	{
		$collection = Mage::getModel('giveawayfb/giveawayfb')->getCollection()
			->addFieldToFilter('SUBDATE(confirm_creation, INTERVAL 7 HOUR)', array('gt' => 0))
		;
		$collection->getSelect()
			->reset('columns')
			->columns(new Zend_Db_Expr("DATE(SUBDATE(confirm_creation, INTERVAL 7 HOUR)) AS confirm_day"))
			->columns(new Zend_Db_Expr("COUNT(*) AS confirmed"))
			->group('confirm_day')
		;
		
		// Made array with totals.
		$registers = array();
		foreach($collection as $_register) {
			$registers[$_register->getConfirmDay()] = $_register->getConfirmed();
		}
		
		return $registers;
	}
	
	function getOrdersPerDay()
	{
		$collection = Mage::getModel('giveawayfb/giveawayfb')->getCollection()
			->addFieldToFilter('SUBDATE(order_creation, INTERVAL 7 HOUR)', array('gt' => 0))
		;
		$collection->getSelect()
			->reset('columns')
			->columns(new Zend_Db_Expr("DATE(SUBDATE(order_creation, INTERVAL 7 HOUR)) AS order_day"))
			->columns(new Zend_Db_Expr("COUNT(*) AS orders"))
			->group('order_day')
		;
		
		// Made array with totals.
		$registers = array();
		foreach($collection as $_register) {
			$registers[$_register->getOrderDay()] = $_register->getOrders();
		}
		
		return $registers;
	}
}
