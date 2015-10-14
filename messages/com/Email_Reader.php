<?php
 
class Email_reader {
 
    // imap server connection
    public $conn;
 
    // inbox storage and inbox message count
    private $inbox;
    private $msg_cnt;
 
    // email login credentials
    private $server = '';
    private $user   = '';
    private $pass   = '';
    private $port   = 110; // adjust according to server settings
 
    // connect to the server and get the inbox emails
    function __construct($server, $username, $password, $port = 110) {
		$this->server = $server;
		$this->user = $username;
		$this->pass = $password;
		$this->port = $port;
		
        $this->connect();
        //$this->inbox();
    }
 
    // close the server connection
    function close() {
        $this->inbox = array();
        $this->msg_cnt = 0;
 
        imap_close($this->conn);
    }
 
    // open the server connection
    // the imap_open function parameters will need to be changed for the particular server
    // these are laid out to connect to a Dreamhost IMAP server
    function connect() {
        $this->conn = imap_open('{'.$this->server.':'.$this->port.'/pop3/novalidate-cert/notls}', $this->user, $this->pass) or die(print_r(imap_errors()));
        //$this->conn = imap_open('{'.$this->server.'/pop3:'.$this->port.'}INBOX', $this->user, $this->pass) or die(print_r(imap_errors()));
    }
 
    // move the message to a new folder
    function move($msg_index, $folder='INBOX.Processed') {
        // move on server
        imap_mail_move($this->conn, $msg_index, $folder);
        imap_expunge($this->conn);
 
        // re-read the inbox
        $this->inbox();
    }
	
	// mark the message for deletion
    function markRemove($msg_index) {
        imap_delete($this->conn, $msg_index);
    }
	
	// delete items marked for deletion
    function remove() {
        imap_expunge($this->conn);
    }
 
    // get a specific message (1 = first email, 2 = second email, etc.)
    function get($msg_index=NULL) {
        if (count($this->inbox) <= 0) {
            return array();
        }
        elseif ( ! is_null($msg_index) && isset($this->inbox[$msg_index])) {
            return $this->inbox[$msg_index];
        }
 
        return $this->inbox[0];
    }
 
    // read the inbox
    function inbox() {
        $this->msg_cnt = imap_num_msg($this->conn);
 
        $in = array();
        for($i = 1; $i <= $this->msg_cnt; $i++) {
			$errors = 0;
			
			$headerinfo = '';
			try {
				$headerinfo = imap_headerinfo($this->conn, $i);
			}
			catch(Exception $e) {
				$errors++;
			}
			
			$body = '';
			try {
				$body = imap_body($this->conn, $i);
			}
			catch(Exception $e) {
				$errors++;
			}
			
			$structure = '';
			try {
				$structure = imap_fetchstructure($this->conn, $i);
			}
			catch(Exception $e) {
				$errors++;
			}
			
			if(isset($structure->parts) && is_array($structure->parts) && isset($structure->parts[1])) {
				$part = $structure->parts[1];
				
				switch ($part->encoding) {
					# 7BIT
					case 0:
						break;
					# 8BIT
					case 1:
						$body = quoted_printable_decode(imap_8bit($body));
						break;
					# BINARY
					case 2:
						$body = imap_binary($body);
						break;
					# BASE64
					case 3:
						$body = imap_base64($body);
						break;
					# QUOTED-PRINTABLE
					case 4:
						$body = quoted_printable_decode($body);
						break;
					# OTHER
					case 5:
						break;
					# UNKNOWN
					default:
				}
			}
			
            $in[] = array(
                'index'     => $i,
                'header'    => $headerinfo,
                'body'      => $body,
                'structure' => $structure,
				'errors'	=> $errors
            );
        }
 
        $this->inbox = $in;
		
		return $this->inbox;
    }
 
}
 
?>