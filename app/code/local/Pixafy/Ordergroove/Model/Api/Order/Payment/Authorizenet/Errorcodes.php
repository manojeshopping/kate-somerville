<?php
/**
 * Authorize.net specific error code mappings
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * @method getErrorCodeMaps()
 * 
 */
class Pixafy_Ordergroove_Model_Api_Order_Payment_Authorizenet_Errorcodes extends Pixafy_Ordergroove_Model_Api_Order_Payment_Abstract{
	/**
	 * Codes from Auth.net
	 * Deprecated
	 
	const ERROR_CARD_HAS_EXPIRED				=	'Incorrect credit card expiration date.';
	const ERROR_CARD_INVALID_BILLING_ADDRESS	=	'Gateway error: The transaction has been declined because of an AVS mismatch. The address provided does not match billing address of cardholder.';
	const ERROR_CARD_INVALID_CARD_NUMBER		=	'Invalid Credit Card Number';
	const ERROR_CARD_INVALID_TYPE				=	'Credit card type is not allowed for this payment method.';
	const ERROR_CARD_TRANSACTION_DECLINED		=	'Gateway error: This transaction has been declined.';
	const ERROR_CARD_NUMBER_TYPE_MISMATCH		=	'Credit card number mismatch with credit card type.';
	*/
	
	/**
	 * Error messages from Authorize.net
	 */
	const ERROR_CARD_HAS_DECLINED				= 	'auth.net error 2 - this transaction has been declined.';
	const ERROR_CARD_HAS_DECLINED_2				= 	'auth.net error 3 - this transaction has been declined.';
	const ERROR_CARD_HAS_DECLINED_3				= 	'auth.net error 4 - this transaction has been declined.';
	const ERROR_CARD_INVALID_AMOUNT				= 	'auth.net error 5 - a valid amount is required.';
	const ERROR_CARD_INVALID_CARD_NUMBER		= 	'auth.net error 6 - the credit card number is invalid.';
	const ERROR_CARD_INVALID_EXPIRATION			= 	'auth.net error 7 - credit card expiration date is invalid.';	
	const ERROR_CARD_HAS_EXPIRED				=  	'auth.net error 8 - the credit card has expired.';
	const ERROR_CARD_INVALID_ABA				= 	'auth.net error 9 - the aba code is invalid';
	const ERROR_CARD_INVALID_ACCOUNT			= 	'auth.net error 10 - the account number is invalid';
	const ERROR_CARD_DUPLICATE_TRANSACTION		= 	'auth.net error 11 - a duplicate transaction has been submitted.';
	const ERROR_CARD_MISSING_AUTH_CODE			=	'auth.net error 12 - an authorization code is required but not present.';
	const ERROR_CARD_INVALID_REFERRER			= 	'auth.net error 14 - the referrer, relay response or receipt link url is invalid.';
	const ERROR_CARD_INVALID_TANSACTION_ID		=	'auth.net error 15 - the transaction id is invalid or not present.';
	const ERROR_CARD_TRANSACTION_NOT_FOUND		=	'auth.net error 16 - the transaction cannot be found.';
	const ERROR_CARD_INVALID_CARD_TYPE			= 	'auth.net error 17 - the merchant does not accept this type of credit card.';
	const ERROR_CARD_INVALID_TRANSACTION		= 	'auth.net error 18 - ach transactions are not accepted by this merchant.';
	const ERROR_CARD_INVALID_TERMINAL_ID		= 	'auth.net error 24 - the nova bank number or terminal id is incorrect. call merchant service provider.';
	const ERROR_CARD_AVS_MISMATCH				= 	'auth.net error 27 - the transaction has been declined because of an avs mismatch. the address provided does not match billing address of cardholder.';
	const ERROR_CARD_INVALID_CREDIT_CARD		= 	'auth.net error 28 - the merchant does not accept this type of credit card.';
	const ERROR_CARD_INVALID_PAYMENT_ID			= 	'auth.net error 29 - the paymentech identification numbers are incorrect. call merchant service provider.';
	const ERROR_CARD_INVALID_CONFIGURATION		= 	'auth.net error 30 - the configuration with processor is invalid. call merchant service provider.';	
	const ERROR_CARD_INVALID_FDC_ID				= 	'auth.net error 31 - the fdc merchant id or terminal id is incorrect. call merchant service provider.';
	const ERROR_CARD_INVALID_MERCHANT_PASSWORD	=	'auth.net error 35 - the merchant password is invalid or not present.';
	const ERROR_CARD_FAILED_SETTLEMENT			= 	'auth.net error 36 - the authorization was approved but settlement failed.';	
	const ERROR_CARD_INVALID_CREDIT_CARD_NUMBER	=	'auth.net error 37 - the credit card number is invalid.';
	const ERROR_CARD_PAYMENT_SYSTEM_ID			= 	'auth.net error 38 - the global payment system identification numbers are incorrect. call merchant service provider.';
	const ERROR_CARD_TRANSACTION_NOT_ENCRYPTED	= 	'auth.net error 40 - this transaction must be encrypted.';
	const ERROR_CARD_MERCHANT_SETUP				= 	'auth.net error 43 - the merchant was incorrectly set up at the processor. call merchant service provider.';
	const ERROR_CARD_MAXIMUM_AMOUNT				= 	'auth.net error 47 - the amount requested for settlement cannot be greater than the original amount authorized.';
	const ERROR_CARD_PATRIAL_REVERSAL			= 	'auth.net error 48 - this processor does not accept partial reversals.';
	const ERROR_CARD_MAXIMUM_AMOUNT_ALLOWED		=	'auth.net error 49 - the transaction amount submitted was greater than the maximum amount allowed.';
	const ERROR_CARD_WAITING_SETTLEMENT			= 	'auth.net error 50 - this transaction is awaiting settlement and cannot be refunded.';
	const ERROR_CARD_SUM_MAXIMUM_AMOUNT			= 	'auth.net error 51 - the sum of all credits against this transaction is greater than the original transaction amount.';	
	const ERROR_CARD_CLIENT_NOT_NOTIFIED		= 	'auth.net error 52 - the transaction was authorized but the client could not be notified; it will not be settled.';
	const ERROR_CARD_INVALID_ACH_TRAN_TYPE		= 	'auth.net error 53 - the transaction type is invalid for ach transactions.';
	const ERROR_CARD_INVALID_REFERENCED_TRAN	= 	'auth.net error 54 - the referenced transaction does not meet the criteria for issuing a credit.';
	const ERROR_CARD_MAXIMUM_REFERENCED_TRAN	= 	'auth.net error 55 - the sum of credits against the referenced transaction would exceed original debit amount.';
	const ERROR_CARD_TRANSACTION_NOT_ACCEPTED	=	'auth.net error 66 - this transaction cannot be accepted for processing.';
	const ERROR_CARD_INVALID_PARAMETER			= 	'auth.net error 68 - the version parameter is invalid';
	const ERROR_CARD_INVALID_TRAN_TYPE			= 	'auth.net error 69 - the transaction type is invalid';
	const ERROR_CARD_INVALID_TRANSACTION_METHOD	=	'auth.net error 70 - the transaction method is invalid.';
	const ERROR_CARD_INVALID_BANK_ACCOUNT		= 	'auth.net error 71 - the bank account type is invalid.';
	const ERROR_CARD_INVALID_AUTH_CODE			= 	'auth.net error 72 - the authorization code is invalid.';
	const ERROR_CARD_INVALID_DRIVER_DOB			= 	"auth.net error 73 - the driver's license date of birth is invalid.";
	const ERROR_CARD_INVALID_DUTY_AMOUNT		= 	'auth.net error 74 - the duty amount is invalid.';
	const ERROR_CARD_INVALID_FREIGHT_AMOUNT		= 	'auth.net error 75 - the freight amount is invalid.';
	const ERROR_CARD_INVALID_TAX_AMOUNT			= 	'auth.net error 76 - the tax amount is invalid.';
	const ERROR_CARD_INVALID_SSN				= 	'auth.net error 77 - the ssn or tax id is invalid.';
	const ERROR_CARD_INVALID_CARD_CODE			=	'auth.net error 78 - the card code is invalid.';
	const ERROR_CARD_IVALID_DRIVER_LICENSE		= 	"auth.net error 79 - the driver's license number is invalid.";
	const ERROR_CARD_INVALID_DRIVER_STATE		= 	"auth.net error 80 - the driver's license state is invalid.";
	const ERROR_CARD_INVALID_REQUEST_TYPE		= 	'auth.net error 81 - the requested form type is invalid.';
	const ERRPR_CARD_INVALID_SCRIPT_VERSION		=  	'auth.net error 82 - scripts are only supported in version 2.5.';
	const ERROR_CARD_REQUEST_SCRIPT_NOT_SUPPORT	= 	'auth.net error 83 - the requested script is either invalid or no longer supported.';
	const ERROR_CARD_INVALID_DEVICE_TYPE		= 	'auth.net error 84 - the device type is invalid';
	const ERROR_CARD_INVALID_MARKET_TYPE		= 	'auth.net error 85 - the market type is invalid';
	const ERROR_CARD_INVALID_REPONSE_FORMAT		= 	'auth.net error 86 - the response format is invalid';
	const ERROR_CARD_TRAN_MARKET_NOT_PROCESSED	= 	'auth.net error 87 - transactions of this market type cannot be processed on this system.';
	const ERROR_CARD_INVALID_TRACK1_DATA		= 	'auth.net error 88 - track1 data is not in a valid format.';
	const ERROR_CARD_INVALID_TRAK2_DATA			= 	'auth.net error 89 - track2 data is not in a valid format.';
	const ERROR_CARD_ACH_TRAN_NOT_ACCEPTED		=	'auth.net error 90 - ach transactions cannot be accepted by this system.';
	const ERROR_CARD_VERSION_NOT_SUPPORTED		= 	'auth.net error 91 - version 2.5 is no longer supported.';
	const ERROR_CARD_GATEWAY_NOT_INTEGRATE		= 	'auth.net error 92 - the gateway no longer supports the requested method of integration.';
	const ERROR_CARD_TRANSACTION_FAILED			= 	'auth.net error 97 - this transaction cannot be accepted.';
	const ERROR_CARD_INVALID_ECHECK_TYPE		= 	'auth.net error 100 - the echeck type parameter is invalid.';
	const ERROR_CARD_ACCOUNT_NAME_MISMATCH		= 	'auth.net error 101 - the given name on the account and/or the account type does not match the actual account.';
	const ERROR_CARD_INVALID_REQUEST			= 	'auth.net error 102 - this request cannot be accepted.';
	const ERROR_CARD_PAYMENT_CAPTURE_FALIED		= 	'auth.net error 103 - this transaction cannot be accepted.';
	const ERRPR_CARD_INVALID_AUTH_INDICATOR		= 	'auth.net error 116 - the authentication indicator is invalid.';
	const ERROR_CARD_INVALID_AUTH_VALUE			= 	'auth.net error 117 - the cardholder authentication value is invalid.';
	const ERROR_CARD_INVALID_COMBO_CARD_TYPE	=	'auth.net error 118 - the combination of card type, authentication indicator and cardholder authentication value is invalid.';
	const ERROR_CARD_TRAN_NOT_RECIRRING			= 	'auth.net error 119 - transactions having cardholder authentication values cannot be marked as recurring.';
	const ERROR_CARD_ACCOUNT_NOT_PERMISSON		= 	'auth.net error 123 - this account has not been given the permission(s) required for this request.';
	const ERROR_CARD_AVS_ADDRESS_MISMATCH		= 	'auth.net error 127 - the transaction resulted in an avs mismatch. the address provided does not match billing address of cardholder.';
	const ERROR_CARD_TRANSACTION_NOT_PROCESSED	= 	'auth.net error 128 - this transaction cannot be processed.';
	const ERROR_CARD_REQUEST_ERROR				= 	'auth.net error 170 - an error occurred during processing. please contact the merchant.';
	const ERROR_CARD_TRAN_TYPE_INVALID			=	'auth.net error 174 - the transaction type is invalid. please contact the merchant.';
	const ERROR_CARD_VOIDING_CREDITS			=	'auth.net error 175 - this processor does not allow voiding of credits.';
	const ERROR_CARD_PROCESSING_ERROR			= 	'auth.net error 180 - an error occurred during processing.  please try again.';
	const ERROR_CARD_TRAN_DECLINED				= 	'auth.net error 200 - this transaction has been declined';
	const ERROR_CARD_ECHECK_RECURING			= 	'auth.net error 243 - recurring billing is not allowed for this echeck.net type.';
	const ERROR_CARD_INVALID_ECHECK				= 	'auth.net error 244 - this echeck.net type is not allowed for this bank account type.';
	const ERROR_ECHECK_NOT_ALLOWED				=	'auth.net error 245 - this echeck.net type is not allowed when using the payment gateway hosted payment form.';
	const ERROR_CHECK_TYPE_NOT_ALLOWED			=	'auth.net error 246 - this echeck.net type is not allowed.';
	const ERROR_TRANSACTION_DECLINED			= 	'auth.net error 250 - this transaction has been declined.';
	const ERROR_INVALID_ITEM					=	'auth.net error 270 - line item [item number] is invalid.';
	const ERROR_MAXIMUM_LINES_SUBMITTED			=	'auth.net error 271 - the number of line items submitted is not allowed. a maximum of %1 line items can be submitted.';
	const ERROR_ZERO_DOLLAR_CARD_TYPE			= 	'auth.net error 289 - this processor does not accept zero dollar authorization for this card type.';
	const ERROR_CARD_MISSING_REQURED_FILEDS		= 	'auth.net error 290 - there is one or more missing or invalid required fields.';
	const ERROR_CARD_PARTIALLY_APPROVED			=	'auth.net error 295 - the amount of this request was only partially approved on the given prepaid card. an additional payment is required to fulfill the balance of this transaction.';
	const ERROR_CARD_INVALID_SPLITTEDNERID		=	'auth.net error 296 - the specified splittenderid is invalid.';
	const ERROR_SPLIT_TRAN_ID_CONFLICT			=	'auth.net error 297 - transaction id and split tender id cannot both be used in the same request.';
	const ERROR_INVALID_DEVICE_ID				=	'auth.net error 300 - the device id is invalid.';
	const ERROR_INVALID_DEVICE_BATCH			= 	'auth.net error 301 - the device batch id is invalid.';
	const ERROR_INVALID_REVERSAL_FLAG			=	'auth.net error 302 - the reversal flag is invalid.';
	const ERROR_DEVICE_BATCH_FULL				= 	'auth.net error 303 - the device batch is full. please close the batch.';
	const ERROR_TRANSACTION_IN_CLOSED_BATCH		= 	'auth.net error 304 - the original transaction is in a closed batch.';
	const ERROR_AUTO_CLOSE						= 	'auth.net error 305 - the merchant is configured for auto-close.';
	const ERROR_BATCH_CLOSED					= 	'auth.net error 306 - the batch is already closed.';
	const ERROR_DEVICE_DISABLE					=	'auth.net error 309 - the device has been disabled.';
	const ERROR_CARD_INVALID_EXPIRED_DATE		=	'auth.net error 316 - credit card expiration date is invalid.';
	const ERROR_CREDIT_CARD_EXPIRED				=	'auth.net error 317 - the credit card has expired.';
	const ERROR_DUPLICATED_TRAN					=	'auth.net error 318 - a duplicate transaction has been submitted.';
	const ERROR_TRANSACTION_NOT_FOUND			= 	'auth.net error 319 - the transaction cannot be found.';
	
	/**
	 * Return the error code mappings
	 * 
	 * @return array
	 */
	public function getErrorCodeMaps(){
		return array(
			/*
			strtolower(self::ERROR_CARD_HAS_EXPIRED)				=>	self::ERROR_CODE_CARD_HAS_EXPIRED,
			strtolower(self::ERROR_CARD_INVALID_CARD_NUMBER)		=>	self::ERROR_CODE_INVALID_CARD_NUMBER,
			strtolower(self::ERROR_CARD_INVALID_TYPE)				=>	self::ERROR_CODE_INVALID_CARD_TYPE,
			strtolower(self::ERROR_CARD_INVALID_BILLING_ADDRESS)	=>	self::ERROR_CODE_INVALID_BILLING_ADDRESS,
			strtolower(self::ERROR_CARD_TRANSACTION_DECLINED)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_NUMBER_TYPE_MISMATCH)		=>	self::ERROR_CODE_INVALID_CARD_NUMBER,
			*/
			strtolower(self::ERROR_CARD_HAS_DECLINED)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_HAS_DECLINED_2)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_HAS_DECLINED_3)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_AMOUNT)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_CARD_NUMBER)		=>	self::ERROR_CODE_INVALID_CARD_NUMBER,
			strtolower(self::ERROR_CARD_INVALID_EXPIRATION)			=>	self::ERROR_CODE_CARD_HAS_EXPIRED,
			strtolower(self::ERROR_CARD_HAS_EXPIRED)				=>	self::ERROR_CODE_CARD_HAS_EXPIRED,
			strtolower(self::ERROR_CARD_INVALID_ABA)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_ACCOUNT)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_DUPLICATE_TRANSACTION)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_MISSING_AUTH_CODE)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_REFERRER)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_TANSACTION_ID)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_TRANSACTION_NOT_FOUND)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_CARD_TYPE)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_TRANSACTION)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_TERMINAL_ID)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_AVS_MISMATCH)				=>	self::ERROR_CODE_INVALID_BILLING_ADDRESS,
			strtolower(self::ERROR_CARD_INVALID_CREDIT_CARD)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_PAYMENT_ID)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_CONFIGURATION)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_FDC_ID)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_MERCHANT_PASSWORD)	=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_FAILED_SETTLEMENT)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_CREDIT_CARD_NUMBER)	=>	self::ERROR_CODE_INVALID_CARD_NUMBER,
			strtolower(self::ERROR_CARD_PAYMENT_SYSTEM_ID)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_TRANSACTION_NOT_ENCRYPTED)	=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_MERCHANT_SETUP)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_MAXIMUM_AMOUNT)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_PATRIAL_REVERSAL)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_MAXIMUM_AMOUNT_ALLOWED)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_WAITING_SETTLEMENT)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_SUM_MAXIMUM_AMOUNT)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_CLIENT_NOT_NOTIFIED)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_ACH_TRAN_TYPE)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_REFERENCED_TRAN)	=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_MAXIMUM_REFERENCED_TRAN)	=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_TRANSACTION_NOT_ACCEPTED)	=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_PARAMETER)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_TRAN_TYPE)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_TRANSACTION_METHOD)	=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_BANK_ACCOUNT)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_AUTH_CODE)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_DRIVER_DOB)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_DUTY_AMOUNT)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_FREIGHT_AMOUNT)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_TAX_AMOUNT)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_SSN)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_CARD_CODE)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_IVALID_DRIVER_LICENSE)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_DRIVER_STATE)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_REQUEST_TYPE)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERRPR_CARD_INVALID_SCRIPT_VERSION)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_REQUEST_SCRIPT_NOT_SUPPORT)	=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_DEVICE_TYPE)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_MARKET_TYPE)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_REPONSE_FORMAT)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_TRAN_MARKET_NOT_PROCESSED)	=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_TRACK1_DATA)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_TRAK2_DATA)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_ACH_TRAN_NOT_ACCEPTED)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_VERSION_NOT_SUPPORTED)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_GATEWAY_NOT_INTEGRATE)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_TRANSACTION_FAILED)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_ECHECK_TYPE)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_ACCOUNT_NAME_MISMATCH)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_REQUEST)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_PAYMENT_CAPTURE_FALIED)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERRPR_CARD_INVALID_AUTH_INDICATOR)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_AUTH_VALUE)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_COMBO_CARD_TYPE)	=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_TRAN_NOT_RECIRRING)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_ACCOUNT_NOT_PERMISSON)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_AVS_ADDRESS_MISMATCH)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_TRANSACTION_NOT_PROCESSED)	=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_REQUEST_ERROR)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_TRAN_TYPE_INVALID)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_VOIDING_CREDITS)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_PROCESSING_ERROR)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_TRAN_DECLINED)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_ECHECK_RECURING)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_ECHECK)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_ECHECK_NOT_ALLOWED)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CHECK_TYPE_NOT_ALLOWED)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_TRANSACTION_DECLINED)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_INVALID_ITEM)					=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_MAXIMUM_LINES_SUBMITTED)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_ZERO_DOLLAR_CARD_TYPE)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_MISSING_REQURED_FILEDS)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_PARTIALLY_APPROVED)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_SPLITTEDNERID)		=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_SPLIT_TRAN_ID_CONFLICT)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_INVALID_DEVICE_ID)				=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_INVALID_DEVICE_BATCH)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_INVALID_REVERSAL_FLAG)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_DEVICE_BATCH_FULL)	 			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_TRANSACTION_IN_CLOSED_BATCH)	 	=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_AUTO_CLOSE)	 					=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_BATCH_CLOSED)					=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_DEVICE_DISABLE)					=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_CARD_INVALID_EXPIRED_DATE)		=>	self::ERROR_CODE_CARD_HAS_EXPIRED,
			strtolower(self::ERROR_CREDIT_CARD_EXPIRED)				=>	self::ERROR_CODE_CARD_HAS_EXPIRED,
			strtolower(self::ERROR_DUPLICATED_TRAN)					=>	self::ERROR_CODE_PAYMENT_DECLINED,
			strtolower(self::ERROR_TRANSACTION_NOT_FOUND)			=>	self::ERROR_CODE_PAYMENT_DECLINED,
			
		);
	}
}
?>
