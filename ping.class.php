<?php
class ping {

	// ICMP ping packet with a pre-calculated checksum
	protected static $payload = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";

	/**
	 * Send a ping request to a host.
	 *
	 * @param  string  $host
	 * @param  int 	   $timeout
	 * @return bool
	 */
	public static function send($host, $timeout = 1) {
		if (extension_loaded('sockets')) {
			return self::socketSend($host, $timeout);
		} else {
			return self::execSend($host);
		}
	}

	/**
	 * Use sockets to ping a host.
	 *
	 * Will call function to use exec to send ping request if the socket request fails.
	 * Socket request will fail if it is unable to find the host.
	 *
	 * Using sockets under Windows requires that the Application Pool in IIS be running under an account with local admin rights.
	 *
	 * @param  string  $host
	 * @param  int 	   $timeout
	 * @return bool
	 */
	protected static function socketSend($host, $timeout) {
		try {
			$socket = socket_create(AF_INET, SOCK_RAW, 1);
			socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));
			socket_connect($socket, $host, null);

			socket_send($socket, self::$payload, strLen(self::$payload), 0);

			$result = socket_read($socket, 255) ? true : false;

			socket_close($socket);

			return $result;
		} catch (Exception $e) {
			return self::execSend($host);
		}
	}

	/**
	 * Use exec to ping a host.
	 *
	 * Ping command is specific to Windows host.
	 *
	 * @param  string  $host
	 * @return bool
	 */
	protected static function execSend($host) {
		$command = escapeshellcmd('ping -n 1 -w 1 ' . $host);
		exec($command, $result, $returnCode);
		
		return arr::search($result, 'received = 1') ? true : false;
	}
}