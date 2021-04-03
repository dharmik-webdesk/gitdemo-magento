<?php
/**
 * XLS parsing uses php-excel-reader from http://code.google.com/p/php-excel-reader/
 */
	header('Content-Type: text/plain');
	$Filepath = 'REDCAT Example.xls';
	require('php-excel-reader/excel_reader2.php');
	require('SpreadsheetReader.php');
	date_default_timezone_set('UTC');
	$StartMem = memory_get_usage();
	
	try
	{
		$Spreadsheet = new SpreadsheetReader($Filepath);
		$BaseMem = memory_get_usage();

		$Sheets = $Spreadsheet -> Sheets();
		
		//print_r($Sheets);

		foreach ($Sheets as $Index => $Name)
		{
			
			$Time = microtime(true);

			$Spreadsheet -> ChangeSheet($Index);

			foreach ($Spreadsheet as $Key => $Row)
			{
				
				
				
				
				
				if ($Row)
				{
					print_r($Row);
				}
				else
				{
					var_dump($Row);
				}
			}
		
		}
		
	}
	catch (Exception $E)
	{
		echo $E -> getMessage();
	}
?>
