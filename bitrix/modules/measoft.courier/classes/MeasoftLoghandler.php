<?php
use Bitrix\Main\IO,
    Bitrix\Main\Application;

define("measoft_log_storage_days", 3);

/** Ehchange logging class
 * Class MeasoftLoghandler
 */

class MeasoftLoghandler{

    /** Write the log to a file
     * @param $data
     * @throws \Exception
     */
    public static function log($data){
		
        self::logDirectoryCheck();
        $delFiles = self::logCleaner();
        $date = date("m.d.y");

        $f = fopen ($_SERVER['DOCUMENT_ROOT'].'/upload/MeasoftLogs/'.$date.".log", "a");
        $log = "\n------------------------\n";
        $log .= date("Y.m.d G:i:s") . "\n";
		if($delFiles['sucess'] === false) {
			$log .= "\n----delete file error---\n";
			$log .= print_r($delFiles['error'], 1);
			$log .= "\n------------------------\n";
		}
        $log .= print_r($data, 1);
        fwrite ($f, $log);
        fclose($f);
		
    }


    /**
     * Check the existence of the directory
     */
    public static function logDirectoryCheck(){

        $dir = new IO\Directory(Application::getDocumentRoot().'/upload/MeasoftLogs');

        if(!($dir->isExists()))
        {
            IO\Directory::createDirectory(Application::getDocumentRoot() . "/upload/MeasoftLogs");
        }
    }

    /**
     * Delete old logs
     * @throws \Exception
	 * @return array
     */
    public static function logCleaner(){

        $dir = new IO\Directory(Application::getDocumentRoot().'/upload/MeasoftLogs/');
        $arDir = $dir->getChildren();

        foreach($arDir as $dirItem) {
			try {
				$timeOfCreation = new DateTime();
				$timeOfCreation->setTimestamp($dirItem->getCreationTime());
				$diff = date_diff(new \DateTime(), $timeOfCreation)->days;

				if ($diff > measoft_log_storage_days)
				{
					$dirItem->delete();
				}

			} catch (Exception $e) {
				$errorMessage = $e->getMessage();
				return array(
						'sucess' => false,
						'error' => $errorMessage
					);
			}
        }
		return array('sucess' => true);;
    }
}
?>