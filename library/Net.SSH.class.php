<?php

// ssh protocols
// note: once openShell method is used, cmdExec does not work

class ssh2 {

  public $host = 'host';
  public $user = 'user';
  public $port = '22';
  public $password = 'password';
  public $con = null;
  public $shell_type = 'xterm';
  public $shell = null;
  public $log = '';
  

  function __construct($host='', $port='') {

  	error_reporting(E_ERROR | E_PARSE);
  	if( $host!='' ) $this->host  = $host;
    if( $port!='' ) $this->port  = $port;

    $this->con  = ssh2_connect($this->host, $this->port);
    if( !$this->con ) {
       $this->log .= "Не удается подключиться к серверу ".$this->host.":".$this->port;
    }
    error_reporting(E_WARN);
  }

  function authPassword( $user = '', $password = '' ) {
	
     if( $user!='' ) $this->user  = $user;
     if( $password!='' ) $this->password  = $password;

     if( !ssh2_auth_password( $this->con, $this->user, $this->password ) ) {
       $this->log .= "В авторизации отказано ".$this->user."@".$this->host;
     }

  }

  function openShell( $shell_type = '' ) {

    if ( $shell_type != '' ) $this->shell_type = $shell_type;
    	$this->shell = ssh2_shell( $this->con,  $this->shell_type );
    if( !$this->shell ) $this->log .= " Shell connection failed !";

  }

  function writeShell( $command = '' ) {

    fwrite($this->shell, $command."\n");

  }

  function cmdExec( ) {

        $argc = func_num_args();
        $argv = func_get_args();

    $cmd = '';
    for( $i=0; $i<$argc ; $i++) {
        if( $i != ($argc-1) ) {
          $cmd .= $argv[$i]." && ";
        }else{
          $cmd .= $argv[$i];
        }
    }
    
    if ($this->con) {
    	$stream = ssh2_exec( $this->con, $cmd );
    	stream_set_blocking( $stream, true );
    
    	$stderr_stream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
    	stream_set_blocking( $stderr_stream, true );
    
    	$_stream = stream_get_contents($stream);
    	$_stderr_stream = stream_get_contents($stderr_stream);
    	
    	if ($_stream) {
    		return $_stream;
    	}
    	else {
    		return $_stderr_stream;
    	}
    }
  }

  function getLog() {

     return $this->log;

  }

}

?>