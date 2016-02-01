<?php
class KindleGenCommand {
	private $COMMAND;
	
	private $inputFile;
	private $outputFile;
	
	public $output;
	public $result;
	
	public static function newInstance($inputFile, $outputFile) {
		$command = new KindleGenCommand();
		$command->setInputFile($inputFile);
		$command->setOutputFile($outputFile);
		return $command;
	}
	
	function __construct() {
		$this->COMMAND = PATH_LIB . '/kindlegen';
	}
	
	public function setInputFile($inputFile) {
		$this->inputFile = $inputFile;
	}
	
	public function setOutputFile($outputFile) {
		$this->outputFile = $outputFile;
	}
	
	public function exec() {
		exec(sprintf('%s %s -o %s', $this->COMMAND, $this->inputFile, $this->outputFile), $this->output, $this->result);
	}
}
