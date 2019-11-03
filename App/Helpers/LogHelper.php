<?php 
namespace App\Helpers;

use App\Core\SiteException;
use App\Enum\MessageCodes;

/**
 * Class to write to the log file
 * @since 17-12-2017
 * @version 1.0
 * @author R Haan
 */
class LogHelper
{
	private static $logFilePath = "../config/log.txt";

	/**
	 * Writes to the log file.
	 * @param $text, the text to write.
	 */
	public static function WriteToLogFile(string $text)
	{
		$now = date("d-m-Y H:i:s");
		// Open the file to get existing content.
		$content = self::ReadFile();
		// Append a new person to the file.
		$content .= "[{$now}]\t{$text}\n\n";
		// Write the contents back to the file.
		self::WriteToFile($content);
	}

	public static function WriteToFile(string $text) 
	{
		if (!file_exists(self::$logFilePath)) 
		{
			$logfile = fopen(self::$logFilePath, "w") or die("Unable to open file!");
			fwrite($logfile, "");
			fclose($logfile);
		}
		
		file_put_contents(self::$logFilePath, $text);
	}
	
	/**
	 * Reads the log file.
	 */
	public static function ReadFile()
	{
		if (file_exists(self::$logFilePath)) 
		{
			return file_get_contents(self::$logFilePath);
		}
		
		throw new SiteException(MessageCodes::wwilhr001, "The log file doesn't exists.");
	}
}

?>
