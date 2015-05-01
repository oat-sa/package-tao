<?php

class UDPListener {

	private static $COLOR = array(
		'0' => '1;30', // dark grey
		'1' => '0;37', // light grey
		'2' => '0;32', // green
		'3' => '1;33', // yellow
		'4' => '1;31', // red
		'5' => '1;31', // red
	);

	private static $FILTER = array(
		"/pg_fetch_row\(\): Unable to jump to row -2 on PostgreSQL result index/"
	);

	private $socket;
	
	public function __construct($pUrl) {
		$this->socket = stream_socket_server($pUrl, $errno, $errstr, STREAM_SERVER_BIND);
		if (!$this->socket) {
			die("$errstr ($errno)");
		}
	}
	
	public function listen() {
		do {
			$received = stream_socket_recvfrom($this->socket, 33000, 0, $peer);
	
			$code = json_decode($received, true); // drop "[".$code['s']."] ".

			$blacklisted = false;	
			foreach (self::$FILTER as $pattern) {
				if (preg_match($pattern, $code['d']) > 0)
					$blacklisted = true;
			}
	
			if (!$blacklisted) {
				$this->render($code);
			}
	
		} while ($received !== false);
	}
	
	public function render($pData) {
	
		echo "\033[".self::$COLOR[$pData['s']].'m'.$pData['d']." (".implode(',',$pData['t']).")\033[0m\n";
		if (isset($pData['b']) && ($pData['s'] >= 3)) {
			$this->renderBacktrace($pData['b']);
		} elseif (in_array('DEPRECATED', $pData['t']) && isset($pData['b'][1])) {
			echo "\t".$pData['b'][1]['file'].'('.$pData['b'][1]['line'].")\n";
		}
	}
	
	public function renderBacktrace($pData) {
		$file		= array();
		$maxlen	= 0;
		foreach ($pData as $row)
			if (isset($row["file"])) {
				$file[] = $row["file"];
				if (strlen($row["file"]) > $maxlen)
					$maxlen = strlen($row["file"]);
		}
		$prefixlen = strlen($this->getCommonPrefix($file));
		
		foreach ($pData as $row) {
			$string = ''
				.(isset($row["file"]) ? substr($row["file"], $prefixlen) : '---')
				.(isset($row["line"]) ? "(".$row["line"].")" : '');
			$string = str_pad($string, $maxlen-$prefixlen+3+5);
			$string .= ''
				.(isset($row["class"]) ? $row["class"].'::' : '')
				.(isset($row["function"]) ? $row["function"]."()" : '---')
			;
			echo "\t".$string."\n";
		}
	}
	
	public function getCommonPrefix($pData) {
		$prefix = array_shift($pData);  // take the first item as initial prefix
		$length = strlen($prefix);
		// compare the current prefix with the prefix of the same length of the other items
		foreach ($pData as $item) {
			 // check if there is a match; if not, decrease the prefix by one character at a time
			 while ($length && substr($item, 0, $length) !== $prefix) {
				  $length--;
				  $prefix = substr($prefix, 0, -1);
			 }
			 if (!$length) {
				  break;
			 }
		}

		return $prefix;
	}
}

$raw = $argv;
array_shift($raw);
$parms = array();
while (!empty($raw)) {
	$current = array_shift($raw);
	if ($current == '--') {
		break;
	}
	if (substr($current, 0, 1) == '-') {
		$key = substr($current, 1);
	}
	if (empty($raw)) {
		echo 'missing param value for '.$key."\n";
		die();
	}
	$value = array_shift($raw);
	$parms[$key] = $value;
}
$url = 'udp://';
$url .= isset($parms['h']) ? $parms['h'] : '127.0.0.1';
$url .= ':'.(isset($parms['p']) ? $parms['p'] : '5775');
echo "Listening to $url\n";

$udr = new UDPListener($url);
$udr->listen();

?>
