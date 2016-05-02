<?php
/**
 * OrderGroove module constants file. Contains static values specific to this module.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Helper_Constants extends Mage_Core_Helper_Abstract{
	
	/**
	 * Attribute code for the OrderGroove custom attribute.
	 */
	const	ATTRIBUTE_CODE_SUBSCRIPTION_ENABLED	=	'og_subscription_enabled';
	const	ATTRIBUTE_CODE_PRODUCT_DISCONTINUED	=	'og_is_discontinued';
	
	/**
	 * Registry key used to indicate not to send a subscription for an order. Mainly
	 * used for orders processed via the API so that a subscription order does not
	 * create a new subscription.
	 */
	const	REGISTRY_KEY_SKIP_SUBSCRIPTION		=	'ordergroove_skip_subscription';
	const	REGISTRY_KEY_FORCE_ALLOW_FREE		=	'ordergroove_force_allow_free';
	const	REGISTRY_KEY_NEGATIVE_TOTAL_API_ORDER	=	'ordergroove_neg_total_api_order';
	
	/**
	 * Registry key for the order to be used on success page for page tagging
	 */
	const REGISTRY_KEY_ORDER_SUCCESS_ORDER		=	'ordergroove_order';
	
	/**
	 * Registry key for current log entry on view log page
	 */
	const REGISTRY_KEY_CURRENT_LOG				=	'ordergroove_current_log';
	
	/**
	 * Order column keys for additional totals
	 */
	const ORDER_COLUMN_OG_IOI_ORDER_DISCOUNT		=	'og_ioi_order_discount';
	const ORDER_COLUMN_OG_IOI_ITEM_DISCOUNT			=	'og_ioi_item_discount';
	const ORDER_COLUMN_BASE_OG_IOI_ORDER_DISCOUNT	=	'base_og_ioi_order_discount';
	const ORDER_COLUMN_BASE_OG_IOI_ITEM_DISCOUNT	=	'base_og_ioi_item_discount';
	
	/**
	 * Log message types
	 */
	const LOG_TYPE_ERROR	=	'ERROR';
	const LOG_TYPE_SUCCESS	=	'SUCCESS';
	const LOG_TYPE_DATA		=	'DATA';
	const LOG_TYPE_RESPONSE	=	'RESPONSE';
	const LOG_TYPE_REQUEST	=	'REQUEST';
	
	/**
	 * Functionality check constants
	 */
	const FUNCTIONALITY_CHECK_DISABLED	=	'og_functionality_disabled';
	const FUNCTIONALITY_CHECK_ENABLED	=	'og_functionality_enabled';
	
	/**
	 * USA address country id for checking to disable 
	 * address types.
	 */
	const USA_COUNTRY_ID	=	'US';
	
	public static function getLogTypes(){
		return array(
			self::LOG_TYPE_ERROR	=>	self::LOG_TYPE_ERROR,
			self::LOG_TYPE_SUCCESS	=>	self::LOG_TYPE_SUCCESS,
			self::LOG_TYPE_DATA		=>	self::LOG_TYPE_DATA,
			self::LOG_TYPE_RESPONSE	=>	self::LOG_TYPE_RESPONSE,
			self::LOG_TYPE_REQUEST	=>	self::LOG_TYPE_REQUEST,
		);
	}
}
?>
