<?php

require_once(dirname(dirname(__FILE__)) . '/app/Mage.php');
umask(0);
Mage::app();

$configurable_collection = Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToSelect('*')
    ->addAttributeToFilter('type_id', 'configurable');

$simple_collection = Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToSelect('*')
    ->addAttributeToFilter('type_id', 'simple');

$giftcard_collection = Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToSelect('*')
    ->addAttributeToFilter('type_id', 'giftcard');

$master_array = array();

foreach ($configurable_collection as $product) {
    if ($product->getStatus() == 1 && $product->getStockItem()->getIsInStock() && $product->getFinalPrice() > 0 && $product->getVisibility() == '4') {
        $child_products = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
        foreach ($child_products as $child) {
            if ($child->getStatus() == 1 && $child->getStockItem()->getIsInStock() && $child->getFinalPrice() > 0) {
                $image_url      = strval(Mage::helper('catalog/image')->init($child, 'small_image')->resize(300));
                $master_array[] = array(
                    'product_id'          => $child->getId(),
                    'product_name'        => $child->getName(),
                    'product_title'       => $child->getTitle(),
                    'product_sub_title'   => $child->getSubTitle(),
                    'product_page_url'    => $child->getProductUrl(),
                    'image_url'           => $image_url,
                    'price'               => '$' . number_format($child->getPrice(), 2),
                    'sale_price'          => $child->getFinalPrice() < $child->getPrice() ? '$' . number_format($child->getFinalPrice(), 2) : '',
                    'product_description' => strip_tags($child->getDescription()),
                    'brand'               => $child->getAttributeText('brand'),
                    'category'            => '#' . implode('#', $child->getCategoryIds()) . '#',
                    'parent_product_id'   => $product->getId(),
                    'quantity_available'  => Mage::getModel('cataloginventory/stock_item')->loadByProduct($child)->getQty(),
                );
            }
        }
    }
}

foreach ($simple_collection as $product) {
    if ($product->getStatus() == 1 && $product->getStockItem()->getIsInStock() && $product->getFinalPrice() > 0 && $product->getVisibility() == '4') {
        $image_url      = strval(Mage::helper('catalog/image')->init($product, 'small_image')->resize(300));
        $master_array[] = array(
            'product_id'          => $product->getId(),
            'product_name'        => $product->getName(),
            'product_title'       => $product->getTitle(),
            'product_sub_title'   => $product->getSubTitle(),
            'product_page_url'    => $product->getProductUrl(),
            'image_url'           => $image_url,
            'price'               => '$' . number_format($product->getPrice(), 2),
            'sale_price'          => $product->getFinalPrice() < $product->getPrice() ? '$' . number_format($product->getFinalPrice(), 2) : '',
            'product_description' => strip_tags($product->getDescription()),
            'brand'               => $product->getAttributeText('brand'),
            'category'            => '#' . implode('#', $product->getCategoryIds()) . '#',
            'parent_product_id'   => '',
            'quantity_available'  => Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty(),
        );
    }
}

foreach ($giftcard_collection as $product) {
    if ($product->getStatus() == 1 && $product->getStockItem()->getIsInStock() && $product->getVisibility() == '4') {
        $image_url      = strval(Mage::helper('catalog/image')->init($product, 'small_image')->resize(300));
        $master_array[] = array(
            'product_id'          => $product->getId(),
            'product_name'        => $product->getName(),
            'product_title'       => $product->getTitle(),
            'product_sub_title'   => $product->getSubTitle(),
            'product_page_url'    => $product->getProductUrl(),
            'image_url'           => $image_url,
            'price'               => '',
            'sale_price'          => '',
            'product_description' => strip_tags($product->getDescription()),
            'brand'               => $product->getAttributeText('brand'),
            'category'            => '#' . implode('#', $product->getCategoryIds()) . '#',
            'parent_product_id'   => '',
            'quantity_available'  => Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty(),
        );
    }
}

$csv_headings = array('Product ID', 'Product Name', 'Product Title', 'Product Sub Title',
    'Product Page URL', 'Image URL', 'Price', 'Sale Price', 'Product Description', 'Brand',
    'Category', 'Parent Product ID', 'Quantity Available'
);

$handle = fopen(dirname(__FILE__) . '/comparemetrics.csv', 'w');

fputcsv($handle, $csv_headings);

foreach ($master_array as $row) {
    fputcsv($handle, $row);
}

fclose($handle);
