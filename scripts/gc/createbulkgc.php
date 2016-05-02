<?php 

require_once '../../app/Mage.php';
$app = Mage::app('default');


$totalGc = '1';
$expiryDate = date('Y-m-d',strtotime('+1 year'));

if($_GET['qty']) {
	if(is_numeric($_GET['qty'])) {
		$totalGc = $_GET['qty'];
	} else {
		echo "Error ! Please enter valid gift card quantity";
		die();
	}
}else {
	echo "Error ! No quantity found.Please enter gift card quantity";
	die();
}

if($_GET['expiry']) {
	$expiryDate = $_GET['expiry'];
	$currentDate = date('Y-m-d');
	$expiryDate = date('Y-m-d',strtotime($expiryDate));
	if($expiryDate <= $currentDate) { echo "Error! Invalid expiry date. "; die(); }
} else {
	echo "Error ! No quantity found.Please enter gift expiry date";
	die();
}


if($_GET['amount']) {
	
	if(is_numeric($_GET['amount'])) {
		$amount = $_GET['amount'];
	} else {
		echo "Error ! Please enter valid gift card amount";
		die();
	}
} else {
	echo "Error ! Please enter giftcard amount";
	die();
}
$message = 'Giftcard created in bulk by admin';
if($_GET['message']) {
	$message = $_GET['message'];
}
echo "<pre>";
echo "<div style='margin:0px auto;'><br/><Br/>";
echo "-----------------GIFT CARD GENERATION SCRIPT--------------------";
echo "<br/><Br/>";
echo "Amount : ".$amount;
echo "<Br/>Expiry Date : ".$expiryDate;
echo "<Br/>Quantity : ".$totalGc;
echo "<Br/><Br/>";

	echo "<table width='50%' border='1'  cellpadding='5' cellspacing='2' >";
	echo "<tr><td><B>S.No.</B></td><td><B>Code</B></td><td><B>Expiry Date</B></td><td><B>Balance</B></td><td><B>Additional Info</B></td></tr>";
	for($k = 1 ; $k <= $totalGc ; $k++) {
		$model = Mage::getModel('enterprise_giftcardaccount/giftcardaccount');
		$model->setWebsiteId('1');
		$model->setStatus('1');
		//$model->setBalance('0');
		$model->setBalance($amount);
		//$model->setDateExpires(date('Y-m-d',strtotime('+1 year')));
		$model->setDateExpires($expiryDate);
		$model->setAction('Issued');
		$model->setHistoryAction(Enterprise_GiftCardAccount_Model_History::ACTION_CREATED);
		$model->setAdditionalInfo($message);
		$model->save();
	
		$history = Mage::getModel('enterprise_giftcardaccount/history')->getCollection()
         ->addFieldToFilter('giftcardaccount_id', $model->getData('giftcardaccount_id'));

		$history = $history->getFirstItem();

			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$connection->beginTransaction();
			$fields = array();
			$fields['additional_info'] = $message;
			$where = $connection->quoteInto('history_id =?', $history->getHistoryId());
			$connection->update('enterprise_giftcardaccount_history', $fields, $where);
			$connection->commit();


		echo "<tr>";
		echo "<td>".$k."</td><td>".$model->getCode()."</td><td>".$model->getDateExpires()."</td><td>".$model->getBalance()."</td><td>".$model->getAdditionalInfo()."</td>";
		echo "</tr>";
	}
	echo "</table></div>";


?>