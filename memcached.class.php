<?php
	class memcached
	{
		public function __construct ()
		{
			$this -> conn = 0;
			$this -> port = 11211;
			$this -> address = '127.0.0.1';
		}
		public function connect ()
		{
			$this -> conn = fsockopen (
				$this -> address,
				$this -> port
			);
		}
		public function set ($key, $value, $exptime = 'never')
		{
			fwrite ($this -> conn, "set ".$key." 0 ".$exptime.
			" ".strlen ($value)."\r\n".$value."\r\n");
		}
		public function get ($key)
		{
			fwrite ($this -> conn, "get ".$key."\r\n");

			$ret = trim (fgets ($this -> conn, 1024));
			if ($ret != 'STORED') return false;

			list ($ret, $key, $flags, $size) = explode (' ', trim (fgets ($this -> conn, 1024)));
			if ($ret != 'VALUE') return false;

			return fread ($this -> conn, $size);
		}
		public function disconnect ()
		{
			fclose ($this -> conn);
		}
	}

	$memcached = new memcached;
	$memcached -> connect ();
	$memcached -> set ('test', '123');
	echo $memcached -> get ('test');
	$memcached -> disconnect ();
?>