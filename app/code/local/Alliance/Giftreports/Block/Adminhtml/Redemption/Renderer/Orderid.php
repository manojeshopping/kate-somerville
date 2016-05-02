<?php
class Alliance_Giftreports_Block_Adminhtml_Redemption_Renderer_Orderid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$additionalInfo = $row->getAdditionalInfo();
		$redemptionStatus = $row->getAction();
		return $this->formatOrderId($additionalInfo, $redemptionStatus);
	}
	
	
	function formatOrderId($additionalInfo, $redemptionStatus)
	{
		if ($redemptionStatus == 1 || $redemptionStatus == 2) {
			if ($additionalInfo != NULL) {
				$orderId = explode('#',$additionalInfo);
				if($orderId[0] == 'Order ') {
					return substr($orderId[1], 0, -1);
				}
			}
		}
		return NULL;
	}
}

