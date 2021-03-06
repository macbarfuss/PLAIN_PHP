<?php 

namespace PLAIN_PHP\Exceptions;
 
class Exception extends \Exception {
	
	private $msg = "AN ERROR OCCURRED";
	protected $helpText;
	
	function __construct($argument) {
		$this->msg = $argument;
	}
	
	public function __toString(){
		$this->head();
        if($this->helpText != null && $this->helpText != ""){
            $this->help();
        }
		$this->trace();
		exit();
	}
	
	private function trace(){
		echo '<div style="color: white; width: 100%; background-color: #FFBB33; padding: 20px;">';
        echo "<h2>Stacktrace:</h2>";
        
		foreach ($this->getTrace() as $t) {
			
			if(!isset($t["file"])) $t["file"] = "";
			if(!isset($t["line"])) $t["line"] = "--";
			
			echo "<p>";
			echo $t["file"] . " in " . $t["function"] . " ( " . $t["line"] . " ) " ;
			echo "</p>";
		}
		echo '</div>';
	}
	
	protected function help(){
		echo '<div style="color: white; width: 100%; background-color: #33B5E5; padding: 20px;">';
		echo $this->helpText;
		echo '</div>';
	} 
	
	protected function head(){
		?>
		<style>
			body{
				margin: 0px;
				padding: 0px;
				font-family: Arial, Helvetica, sans-serif;
			}
		</style>
		<div style="color: white; width: 100%; background-color: #FF4444; padding: 20px;">
			<h1 >
				<?php echo $this->msg; ?>
			</h1>
			<h3>in <?php echo $this->getFile() ?> ( <?php echo $this->getLine() ?> )</h3>
		</div>
		<?php 
	}
}

 ?>