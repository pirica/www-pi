<?php
class Subsonic
{
	protected $_serverUrl;
	protected $_serverPort;
	protected $_creds;
	protected $_commands;
	
	// http://www.subsonic.org/pages/api.jsp 
	
	function __construct($username, $password, $serverUrl, $port="4040", $client="SubPHP")
	{
		$this->setServer($serverUrl, $port);
		$this->_creds = array(
			'u' => $username,
			'p' => $password,
			'v' => '1.12.0', //REST API version (Will need to change if subsonic updates
			'c' => $client, //client name
			'f' => 'json' // tells the subsonic server to respond in json rather than xml
		);
		$this->_commands = array(
			'ping',
			'getLicense',
			'getMusicFolders',
			'getNowPlaying',
			'getIndexes',
			'getMusicDirectory',
			'search',
			'search2',
			'getPlaylists',
			'getPlaylist',
			'createPlaylist',
			'deletePlaylist',
			'download',
			'stream',
			'getCoverArt',
			'scrobble',
			'changePassword',
			'getUsers',
			'getUser',
			'createUser',
			'deleteUser',
			'getChatMessages',
			'addChatMessage',
			'getAlbumList',
			'getRandomSongs',
			'getLyrics',
			'jukeboxControl',
			'getPordcasts',
			'createShare',
			'updateShare',
			'deleteShare',
			'setRating',
		);
	}
	
	public function querySubsonic($action, $o=array(), $rawAnswer=false)
	{
		return $this->_querySubsonic($action, $o, $rawAnswer);
	}
	
	protected function _querySubsonic($action, $o=array(), $rawAnswer=false)
	{
		if ($this->isCommand($action)) // Make sure the command is in the list of commands
		{
			$params = array_merge($this->_creds, $o);
			$url = $this->getServer() . "/rest/" . $action . ".view?" . http_build_query($params);
			$options = array(
				CURLOPT_URL => $url,
				CURLOPT_HEADER => 0,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_CONNECTTIMEOUT => 8,
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_PORT => intval($this->_serverPort)
			);
			$ch = curl_init();
			curl_setopt_array($ch, $options);
			$answer = curl_exec($ch);
			curl_close($ch);
			
			if($rawAnswer)
			{
				return $answer;
			}
			else
			{
				return $this->parseResponse($answer);
			}
		}
		else
		{
			return $this->error("Error: Invalid subsonic command: " . $action);
		}
	}
	
	public function setServer($server, $port=null)
	{
		$server = preg_replace("/^\w{1,6}\:\/\//", "http://", $server);
		if(!preg_match("/^http\:\/\//", $server))
		{
			$server = "http://". $server;
		}
		
		// If theres a port on the url, remove it and save it for later use.
		preg_match("/\:\d{1,6}$/", $server, $matches);
		if(count($matches))
		{
			$server = str_replace($matches[0], "", $server);
			$_port = str_replace(":", "", $matches[0]);
		}
		
		// If port parameter not set but there was one on the url, use the one from the url.
		if($port == null && isset($_port))
		{
			$port = $_port;
		}
		else if($port == null)
		{
			$port = "4040";
		}
		
		$this->_serverUrl = $server;
		$this->_serverPort = $port;
	}
	
	public function getServer()
	{
		return $this->_serverUrl . ":" . $this->_serverPort;
	}
	
	protected function error($error, $data=null)
	{
		error_log($error ."\n". print_r($data, true));
		return (object) array("success"=>false, "error"=>$error, "data"=>$data);
	}
	
	protected function parseResponse($response)
	{
		$object = json_decode($response);
		$object = is_object($object) ? $object : new stdClass();
		
		if(property_exists($object, "subsonic-response"))
		{
			$response = (array)$object->{'subsonic-response'};
			//$data = array_shift($response);
			$data = $response;
			//if(property_exists($data, "status"))
			//{
				//if($data->status == 'ok')
				//{
					return (object) array("success"=>true, "data"=>$data);
				/*}
				else
				{
					return $this->error("Invalid response from server!", $object);
				}*/
			/*}
			else
			{
				return $this->error("Invalid response from server!", $object);
			}*/
		}
		else
		{
			return $this->error("Invalid response from server!", $object);
		}	
	}
	
	public function isCommand($command)
	{
		return in_array($command, $this->_commands);
	}
	
	public function __call($action, $arguments)
	{
		$o = count($arguments) ? (array) $arguments[0] : array();
		return $this->_querySubsonic($action, $o);
	}
	
	
	// method implementations
	
	public function getPlaylists()
	{
		return $this->_querySubsonic('getPlaylists')->data['playlists']->playlist;
	}
	
	public function getPlaylist($playlistId)
	{
		return $this->_querySubsonic('getPlaylist', array('id' => $playlistId))->data['playlist']->entry;
	}
	
	
	public function updatePlaylistAdd($playlistId, $songId)
	{
		return $this->_querySubsonic('updatePlaylist', array('playlistId' => $playlistId, 'songIdToAdd' => $songId))->status == 'ok';
	}
	
	public function updatePlaylistRemove($playlistId, $playlistSongIndex)
	{
		return $this->_querySubsonic('updatePlaylist', array('playlistId' => $playlistId, 'songIndexToRemove' => $playlistSongIndex))->status == 'ok';
	}
	
	
	public function getIndexes()
	{
		return $this->_querySubsonic('getIndexes')->data['indexes']->index;
	}
	
	public function getMusicDirectory($indexId)
	{
		$data = $this->_querySubsonic('getMusicDirectory', array('id' => $indexId))->data['directory'];
		if(property_exists($data, "child")){
			return $data->child;
		}
		else {
			return [];
		}
	}
	
	
	/**
		"username" : "wikke",
		"email" : "subsonic@wikke.net",	// NOT ALWAYS RETURNED!
		"scrobblingEnabled" : false,
		"adminRole" : true,
		"settingsRole" : true,
		"downloadRole" : true,
		"uploadRole" : true,
		"playlistRole" : true,
		"coverArtRole" : true,
		"commentRole" : true,
		"podcastRole" : true,
		"streamRole" : true,
		"jukeboxRole" : true,
		"shareRole" : true,
		"folder" : [ 0, 1 ]		// NOT ALWAYS RETURNED!
	*/
	public function getUsers()
	{
		return $this->_querySubsonic('getUsers')->data['users']->user;
	}
	
	
	
	/**
		"id" : "3337", // song
		"parent" : "15",
		"isDir" : false,
		"title" : "Rammstein - Rammstein",
		"artist" :   // NOT ALWAYS RETURNED!
		"album" : "r",
		"size" : 4291087,
		"contentType" : "audio/mpeg",
		"suffix" : "mp3",
		"duration" : 268,
		"bitRate" : 128,
		"path" : "r/Rammstein - Rammstein.mp3",
		"isVideo" : false,
		"created" : "2013-02-11T19:31:00.000Z",
		"type" : "music",
		"username" : "wikke",
		"minutesAgo" : 0,
		"playerId" : 2,
		"playerName" : "android"
	*/
	public function getNowPlaying()
	{
		return $this->_querySubsonic('getNowPlaying')->data['nowPlaying']->entry;
	}
	
}

?>