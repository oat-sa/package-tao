<?php
/**
 * starting from php 5.4 a built in interface exists
 */

interface common_session_php_SessionHandler {
    public function open($savePath, $sessionName);
    public function close();
    public function read($id);
    public function write($id, $data);
    public function destroy($id);
    public function gc($maxlifetime);
}
?>