<?php 

require_once dirname(dirname(__FILE__)) . '/abstract.php';

class Alliance_Shell_Alliance_Autoreplenish_Sftpcsv_To_Db extends Mage_Shell_Abstract 
{
	public $dbConnection;
	public $autoReplenishDbTable;
	
	public function run()
	{
		echo "Started : Reading the Latest CSV File from OrderGroove STFP ".PHP_EOL;
		$this->establishDatabaseConnection();
		$this->readCsvFromSftp();
		echo "Completed : Updated Database with the CSV File Contents".PHP_EOL;
	}
	
	public function establishDatabaseConnection()
	{
		$this->dbConnection = Mage::getSingleton( 'core/resource' )->getConnection( 'core_write' ); // To write to the database
		$this->autoReplenishDbTable =  Mage::getSingleton( 'core/resource' )->getTableName( 'autoreplenish/autoreplenish' );
	}
	
	public function readCsvFromSftp()
	{
		$sftpDumpFile = new Varien_Io_Sftp();
		$remoteDir = '/incoming/reports/';
		$host = 'feeds.ordergroove.com';
		$username = 'katesomerville';
		$port = 22;
		$password = 'A5_9Q::y^es}VEk';
		$remoteDir = '/incoming/reports'; 
		try {
			$sftpDumpFile->open(
				array(
					'host'      =>  $host,
					'username'  =>  $username,
					'password'  =>  $password,
					'timeout'   => '100'
				)
			);
			
			$sftpDumpFile->cd('/incoming/reports');
			$listOfFiles = $sftpDumpFile->ls();
			$validFiles = array();
			$validFilesSort = array();
			
			for($i=0;$i <count($listOfFiles);$i++) {
				if (preg_match_all('/KateSomerville_crm_subscription_([\d]+)/',$listOfFiles[$i]['text'],$matches)) {					
					$extract_date = explode("_", $matches[0][0]);
					$date =  substr($extract_date[3], 0, 2)."/".
					         substr($extract_date[3], 2, 2)."/".
							 substr($extract_date[3], 4, 4)." ".
							 substr($extract_date[3], 8, 2).":".
							 substr($extract_date[3], 10, 2).":".
							 substr($extract_date[3], 12, 2); 
					$date = date("YmdHis", strtotime($date));
					$validFiles[$i]['matches'] = $matches[0][0];
					$validFiles[$i]['cdate']   = $date;
				}
			}
			
			foreach($validFiles as $k => $d) {
				$cdate[$k] = $d['cdate'];
			}
			array_multisort($cdate, SORT_ASC, $validFiles);
			$validFilesSort = array_values($validFiles);
			
			$latestElem = end($validFilesSort);
			$latestFile = $latestElem['matches'];
			
			echo "\n Latest File: " . $latestFile;
			echo "\n ";
			
			$csvFileContent = $sftpDumpFile->read($latestFile.".csv");
			$autoreplenishRecords = $this->parseCsv($csvFileContent);
			
			for($i=0; $i<count($autoreplenishRecords)-1; $i++) {
				$this->writeToDatabase($autoreplenishRecords[$i]);
			}
			
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	public function parseCsv($csvFileContent)
	{
		$csvFileContent = str_replace(chr(13).chr(10),',', $csvFileContent);
		$csvFileContent = str_replace('"','',$csvFileContent);
		$csvContentArray = explode(',', $csvFileContent);
		return array_chunk($csvContentArray, 11);
    }
	
	public function writeToDatabase($record)
	{
		
		$orderCreateDate = strtotime(
							substr($record[3], 4, 4)."-".
							substr($record[3], 0, 2)."-".
							substr($record[3], 2, 2)." ".
							substr($record[3], 8, 2).":".
							substr($record[3], 10, 2).":".
							substr($record[3], 12, 2));
							
		$orderCreateDate = date('Y-m-d h:i:s',$orderCreateDate);
		
		$nextOrderDate = strtotime(
							substr($record[10], 4, 4)."-".
							substr($record[10], 0, 2)."-".
							substr($record[10], 2, 2)." ".
							substr($record[10], 8, 2).":".
							substr($record[10], 10, 2).":".
							substr($record[10], 12, 2));
							
		$nextOrderDate = date('Y-m-d h:i:s',$nextOrderDate);
		
		$this->updateStatusOfTheOrder($record);
		
		$query = "INSERT IGNORE INTO " . $this->autoReplenishDbTable.
		" SET og_subscription_id = '".$record[0]."', 
		customer_id = '".$record[1]."', 
		customer_email = '".$record[2]."',
		order_create_date = '".$orderCreateDate."',
		order_id = '".$record[4]."',
		product_id = '".$record[5]."',
		sku = '".$record[6]."',
		qty = '".$record[7]."',
		frequency = '".$record[8]."',
		status = '".$record[9]."',
		next_order_date = '".$nextOrderDate."',
		loyalty_points = 0";
		
		$this->dbConnection->query( $query );
	}
	
	public function updateStatusOfTheOrder($record)
	{
		$query = "SELECT * from ".$this->autoReplenishDbTable." WHERE order_id = '".$record[4]."' AND product_id = '".$record[5]."'";
		$results = $this->dbConnection->fetchAll($query);
		for ($i=0;$i < count($results);$i++) {
			if ($record[9] != $results[$i]['status']) {
				$this->dbConnection->query( $query );
				$query = "UPDATE ".$this->autoReplenishDbTable." SET status = '".$record[9]."' WHERE order_id = '".$record[4]."' AND product_id = '".$record[5]."'";
				$this->dbConnection->query( $query );
			}
		}
	}
}
$shell = new Alliance_Shell_Alliance_Autoreplenish_Sftpcsv_To_Db();
$shell->run();
	