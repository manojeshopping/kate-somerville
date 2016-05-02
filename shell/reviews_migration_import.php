<?php

echo "\n** Importing reviews...";

include '../app/Mage.php';
umask(0);
Mage::app();

$file = 'katereviews_export.json';
$master_array = json_decode(file_get_contents($file));

foreach ($master_array as $id => $review) {
    $pre_model = Mage::getModel('alliance_katereviews/review')->load($id);
    if ($pre_model->getId()) {
		$transaction = Mage::getSingleton('core/resource')->getConnection('core_write');
		try {
			$transaction->beginTransaction();
			$transaction->query("DELETE FROM alliance_katereviews_reviews WHERE id=$id");
			$transaction->commit();
		}
		catch (Exception $e) {
			$transaction->rollBack();
		}
	}
    $transaction = Mage::getSingleton('core/resource')->getConnection('core_write');
    try {
        $transaction->beginTransaction();
        $transaction->query("INSERT INTO alliance_katereviews_reviews (id) VALUES ($id)");
        $transaction->commit();
    }
    catch (Exception $e) {
        $transaction->rollBack();
    }
    $new_model = Mage::getModel('alliance_katereviews/review')->load($id);
    foreach ($review as $key => $val) {
        if ($key == 'product_id' && $val == 355) $val = 473;
        if ($key == 'product_id' && $val == 437) $val = 474;
        $new_model->setData($key, $val);
    }
    $new_model->save();
}

echo "\n** Review import completed successfully.\n\n";
