<?php 

namespace killua;

class PasteBin{
		
	// this holds API Developer Key and it's mandatory
	public static $api_dev_key           = '';	
	
	// this holds the chosen option ( $OPTIONS )
	public static $api_option            = '';	
	
	// this holds the text that is to be pasted
	public static $api_paste_code        = '';	
	
	// this holds the paste key 
	public static $api_paste_key         = '';	
	
	// this holds the name / title of the paste
	public static $api_paste_name        = '';	
	
	// this holds the paste format ( syntax )
	// defaults to `text`
	public static $api_paste_format      = 'text';	
	
	// this holds any of the 3 paste privacy values ( 0, 1, 2 )
	// defaults to public
	public static $api_paste_private     = '0';	
	
	// this holds the expiration value of the paste ( 1Y, 1M, 6M,  etc. )
	// defaults to `never`
	public static $api_paste_expire_date = 'N';	
	
	// this holds the username which is mandatory for the creation of the `API User Key` (not API Developer Key!)
	public static $api_user_name         = '';	
	
	// this holds the user password which is mandatory for the creation of the `API User Key` (not API Developer Key!)
	public static $api_user_password     = '';	
	
	// this holds the api user key (assuming the api user key exists)
	public static $api_user_key          = '';	
	
	// this holds the limit of the pastes to be listed 
	// defaults to `50`
	public static $api_results_limit     = '50';	
	
	// this is used to store any error that might occur during request initialization (Bad API request, invalid api_option, ... etc.)
	// and its used to display static errors instead of waiting for the response errors
	public static $errors                = [];
	
	// This is used to store the parsed user-agents
	public static $agents                = [];
	
	// this holds the download url
	public static $download_url          = '';
	
	// this holds the save location for the downloaded content
	public static $download_path         = '';
	
	// This holds the HTTP Response Code for the requests
	public static $httpCode              = 0;

	// This holds the HTTP Response Text for the requests
	public static $httpResponseText      = '';

	// this holds all the available paste options
	public static $opts = [];
	
	// some regular expressions
	public static $REGEX = [ "api_paste"   => "/^([0-9a-zA-Z]+)$/", 
	                         "api_dev_key" => "/^([0-9a-z]+)$/", 
	                         "path"        => "/^(((\/)|())((.*\/.*)+)\/)+$/" ];
	
	// these are the urls that will be used
	public static $URLS  = [ "post"     => "https://pastebin.com/api/api_post.php", "login"   => "https://pastebin.com/api/api_login.php", 
			         "get_raw"  => "https://pastebin.com/api/api_raw.php",  "any_raw" => "https://pastebin.com/raw",
				 "download" => "https://pastebin.com/dl/"
	];

	/*
	 * This function is used to set the api_option.
	 * 
	 * @param string $option Holds the option value.
	 * @return void Returns no value.
	 *
	 */
	public function set_option( string $option ) : void {	
		self::$api_option = $option;
	}
	
	/*
	 * This function is used to check the format of the download path.
	 * Returns a boolean.
	 *
	 * @return bool Returns true on match, false otherwise.
	 */
	public function isValidPath( string $path ) : bool {
		return ( preg_match( ((object) self::$REGEX)->path, $path ) ) ? true : false ;
	}

	/*
	 * Sets the download path where the downloaded paste will be saved.
	 *
	 * @param string $download_path This is the save path for the downloaded paste.
	 * @param array $errors This is an array thats used to store errors.
	 * @return void Return nothing, but store an error in the errors array if the path is not valid or doesn't exist, otherwise store the chosen path string.
	 */
	public function set_download_path( string $download_path, array &$errors ) : void {
		if( !$this->isValidPath( $download_path ) || !is_dir( $download_path ) ){
			array_push( self::$errors, "[PATH ERROR] - Path is either not valid (doesn't end or start with /) or doesn't exist!" );
		}
		else{
			self::$download_path = $download_path;
		}
	}

	/*
	 * This function accepts no arguments and its used to download the paste.
	 * Returns a boolean.
	 *
	 * @return bool Returns true on success, false otherwise.
	 */
	public function download() : bool {	
		$req = self::init_req( ( (object) self::$URLS )->download );
		if( is_null( $req ) ){
			return false;
		}
		else{
			curl_exec( $req );
			self::$httpCode = curl_getinfo( $req, CURLINFO_HTTP_CODE );
			curl_close( $req );
			return true;
		}
	}

	/* 
	 * This function is used to create a new paste.
	 * The function returns a boolean.
	 * Accepts no arguments.
	 *
	 * @return bool Returns true on success, false otherwise.
	 */
	public function paste() : bool {
		$req = self::init_req( ( (object) self::$URLS )->post );
		if( is_null( $req ) ){
			return false;
		}
		else{
			self::$httpResponseText = curl_exec( $req );
			self::$httpCode         = curl_getinfo( $req, CURLINFO_HTTP_CODE );
			curl_close( $req );
			return true;
		}
	}

	/* 
	 * This function is used to delete a certain paste.
	 * The function returns a boolean.
	 *
	 * @param string $paste_key This is the paste's key, which is mandatory.
	 * @return bool Returns true on success, false otherwise.
	 */
	public function delete() : bool {
		$req = self::init_req( ( (object) self::$URLS )->post );
		if( is_null( $req ) ){
			return false;
		}
		else{
			self::$httpResponseText = curl_exec( $req );
			self::$httpCode         = curl_getinfo( $req, CURLINFO_HTTP_CODE );
			curl_close( $req );
			return true;
		}
	}

	/* 
	 * This function is used to list pastes created by a user.
	 * The function returns no value.
	 * @param string $paste_key This is the paste's key, which is mandatory.
	 */
	public function listt() : bool {
		$req = self::init_req( ( (object) self::$URLS )->post );
		if( is_null( $req ) ){
			return false;
		}
		else{
			self::$httpResponseText = curl_exec( $req );
			self::$httpCode         = curl_getinfo( $req, CURLINFO_HTTP_CODE );
			curl_close( $req );
			return true;
		}
	}

	/* 
	 * This function is used to create a user api key.
	 * The function returns a boolean.
	 * Accepts no arguments.
	 *
	 * @return bool Returns true on success, false otherwise.
	 */
	public function get_user_info() : bool {
		$req = self::init_req( ( (object) self::$URLS )->post );
		if( is_null( $req ) ){
			return false;
		}
		else{
			self::$httpResponseText = curl_exec( $req );
			self::$httpCode         = curl_getinfo( $req, CURLINFO_HTTP_CODE );
			curl_close( $req );
			return true;
		}
	}

	/* 
	 * This function is used to get info and settings about a user.
	 * The function returns a boolean.
	 * Accepts no arguments.
	 *
	 * @return bool Returns true on success, false otherwise.
	 */
	public function create_user_key() : bool {
		$req = self::init_req( ( (object) self::$URLS )->post );
		if( is_null( $req ) ){
			return false;
		}
		else{
			self::$httpResponseText = curl_exec( $req );
			self::$httpCode         = curl_getinfo( $req, CURLINFO_HTTP_CODE );
			curl_close( $req );
			return true;
		}
	}

	/* 
	 * This function is used to get raw paste output of any public and unlisted pastes.
	 * The function returns no value.
	 *
	 * @param string $paste_key This is the paste's key, which is mandatory.
	 */
	public function get_raw( string $paste_key = "" ) : bool {
		$req = self::init_req( ( (object) self::$URLS )->get_raw );
		if( is_null( $req ) ){
			return false;
		}
		else{
			self::$httpResponseText = curl_exec( $req );
			self::$httpCode         = curl_getinfo( $req, CURLINFO_HTTP_CODE );
			curl_close( $req );
			return true;
		}
	}

	/*
	 * This function parses a user-agents file.
	 *
	 * @param string $agents_file This is the user-agents file path.
	 * @return array Returns an array containing the parsed user-agents.
	 */
	public function parse_agents( string $agents_file ) : array {	
		$file = __DIR__."/".$agents_file;
		if( @file_exists( $file ) ){
			return @explode( "\x0a", @file_get_contents( $file ) );
		}
	}
	
	/*
	 * This function performs the request initialization and pushes any errors that might occur during the process to the $errors array.
	 * The function returns no value.
	 *
	 * @param string $url This is the specified rul to use when initializing the request.
	 */
	public function init_req( string $url ) {	
		$curl         = null;
		self::$agents = self::parse_agents( 'u-agents.dat' );
		switch ( $url ) 
		{
			case ((object) self::$URLS)->download:
				if( (strcmp( self::$api_option, "download" ) === 0) && (strcmp( self::$download_path, "" ) !== 0) && (strcmp( self::$api_paste_key, "" ) !== 0) )
				{
					self::$download_url = $url . self::$api_paste_key;
					$curl               = curl_init();
					$curl_opts          = array(
								CURLOPT_FILE       => @fopen( self::$download_path . self::$api_paste_key, "w" ),
								CURLOPT_USERAGENT  => self::$agents[ mt_rand( 0, sizeof( self::$agents ) - 1 ) ],
								CURLOPT_TIMEOUT    => 28800,
								CURLOPT_HTTPHEADER => array("Referer: https://pastebin.com/".self::$api_paste_key),
								CURLOPT_URL        => self::$download_url
					);
					curl_setopt_array( $curl, $curl_opts );
				}
				else{
					array_push( self::$errors, "[DOWNLOAD-INITIALIZATION-ERROR] - There was an error initializing the request! Maybe some parameters are either missing, invalid or expired!" );
				}
				break;

			case ((object) self::$URLS)->post:
				if( (strcmp( self::$api_option, "delete" ) === 0) && (strcmp( self::$api_paste_key, "" ) !== 0) )
				{	
					$query = http_build_query( [
								'api_option'    => self::$api_option, 
								'api_user_key'  => self::$api_user_key, 
								'api_dev_key'   => self::$api_dev_key,
								'api_paste_key' => self::$api_paste_key ] );
					$curl      = curl_init();
					$curl_opts = array(
							CURLOPT_POST           => true,
							CURLOPT_POSTFIELDS     => $query,
							CURLOPT_RETURNTRANSFER => 1,
							CURLOPT_NOBODY         => 0,
							CURLOPT_USERAGENT      => self::$agents[ mt_rand( 0, sizeof( self::$agents ) - 1 ) ],
							CURLOPT_TIMEOUT        => 28800,
							CURLOPT_URL            => ((object) self::$URLS)->post
					);
					curl_setopt_array( $curl, $curl_opts );
				}
				else if( strcmp( self::$api_option, "paste" ) === 0 )
				{
					$query = http_build_query( [
								'api_option'            => self::$api_option, 
								'api_dev_key'           => self::$api_dev_key,
								'api_user_key'          => self::$api_user_key,
								'api_paste_code'        => urlencode( self::$api_paste_code ),
								'api_paste_name'        => urlencode( self::$api_paste_name ),
								'api_paste_private'     => self::$api_paste_private,
								'api_paste_expire_date' => self::$api_paste_expire_date,
								'api_paste_format'      => self::$api_paste_format ] );

					$curl      = curl_init();
					$curl_opts = array(
							 CURLOPT_POST           => true,
							 CURLOPT_POSTFIELDS     => $query,
							 CURLOPT_RETURNTRANSFER => 1,
							 CURLOPT_NOBODY         => 0,
							 CURLOPT_USERAGENT      => self::$agents[ mt_rand( 0, sizeof( self::$agents ) - 1 ) ],
							 CURLOPT_TIMEOUT        => 28800,
							 CURLOPT_URL            => ((object) self::$URLS)->post
					);
					curl_setopt_array( $curl, $curl_opts );
				}
				else if( strcmp( self::$api_option, "list" ) === 0 )
				{
					$query = http_build_query( [
								'api_option'        => self::$api_option, 
								'api_dev_key'       => self::$api_dev_key,
								'api_user_key'      => self::$api_user_key,
								'api_results_limit' => self::$api_results_limit ] );

					$curl      = curl_init();
					$curl_opts = array(
							 CURLOPT_POST           => true,
							 CURLOPT_POSTFIELDS     => $query,
							 CURLOPT_RETURNTRANSFER => 1,
							 CURLOPT_NOBODY         => 0,
							 CURLOPT_USERAGENT      => self::$agents[ mt_rand( 0, sizeof( self::$agents ) - 1 ) ],
							 CURLOPT_TIMEOUT        => 28800,
							 CURLOPT_URL            => ((object) self::$URLS)->post
					);
					curl_setopt_array( $curl, $curl_opts );
				}
				else if( strcmp( self::$api_option, "userdetails" ) === 0 )
				{
					$query = http_build_query( [
								'api_option'        => self::$api_option, 
								'api_dev_key'       => self::$api_dev_key,
								'api_user_key'      => self::$api_user_key ] );

					$curl      = curl_init();
					$curl_opts = array(
							 CURLOPT_POST           => true,
							 CURLOPT_POSTFIELDS     => $query,
							 CURLOPT_RETURNTRANSFER => 1,
							 CURLOPT_NOBODY         => 0,
							 CURLOPT_USERAGENT      => self::$agents[ mt_rand( 0, sizeof( self::$agents ) - 1 ) ],
							 CURLOPT_TIMEOUT        => 28800,
							 CURLOPT_URL            => ((object) self::$URLS)->post
					);
					curl_setopt_array( $curl, $curl_opts );
				}
				else{
					array_push( self::$errors, "[REQUEST-INITIALIZATION-ERROR] - There was an error initializing the request! Make sure that api_option or other important parameters are set!" );
				}
				break;

			case ((object) self::$URLS)->get_raw:
				if( (strcmp( self::$api_option, "show_paste" ) === 0) && ( strcmp( self::$api_paste_key, "" ) !== 0 ) )
				{
					$query = http_build_query([
								'api_option'    => self::$api_option,
								'api_dev_key'   => self::$api_dev_key,
								'api_user_key'  => self::$api_user_key,
								'api_paste_key' => self::$api_paste_key
					]);
					$curl      = curl_init();
					$curl_opts = array(
							 CURLOPT_POST           => true,
							 CURLOPT_POSTFIELDS     => $query,
							 CURLOPT_RETURNTRANSFER => 1,
							 CURLOPT_NOBODY         => 0,
							 CURLOPT_USERAGENT      => self::$agents[ mt_rand( 0, sizeof( self::$agents ) - 1 ) ],
							 CURLOPT_TIMEOUT        => 28800,
							 CURLOPT_URL            => ((object) self::$URLS)->get_raw
					);
					curl_setopt_array( $curl, $curl_opts );
				}
				else{
					array_push( self::$errors, "[GET-RAW-INITIALIZATION-ERROR] - There was an error initializing the request! Make sure that api_option or other important parameters are set!" );
				}
				break;

			case ((object) self::$URLS )->login:
					$query = http_build_query([
								'api_dev_key'       => self::$api_dev_key,
								'api_user_name'     => self::$api_user_name,
								'api_user_password' => self::$api_user_password
	
					]);
					$curl      = curl_init();
					$curl_opts = array(
							 CURLOPT_POST           => true,
							 CURLOPT_POSTFIELDS     => $query,
							 CURLOPT_RETURNTRANSFER => 1,
							 CURLOPT_NOBODY         => 0,
							 CURLOPT_USERAGENT      => self::$agents[ mt_rand( 0, sizeof( self::$agents ) - 1 ) ],
							 CURLOPT_TIMEOUT        => 28800,
							 CURLOPT_URL            => ((object) self::$URLS)->login
					);
					curl_setopt_array( $curl, $curl_opts );
				break;
		}
		return $curl;
	}
	
	/*
	 * A function to embed the paste.
	 * 
	 * @param string $paste_key This is the key of the paste.
	 * @return object Returns an object, the first element of which is a string representation of the js embeddable paste key.
	 * The second element is also a string representation of the frame embeddable paste_key.
	 */
	public function embed( string $paste_key ) : object {	
		if( $paste_key !== "" ){
			$embedJS    = '<script src="https://pastebin.com/embed_js/'.trim( $paste_key ).'"></script>';
			$embedFrame = '<iframe src="https://pastebin.com/embed_iframe/'.trim( $paste_key ).'" style="border:none;width:100%"></iframe>';
			return (object) 
       	 	[ 
				"js"    => $embedJS, 
				"frame" => $embedFrame 
       	 	];
		}
	}

	/*
	 * This function displays all available paste formats ( syntax ).
	 *
	 * @param bool $html This parameter is used to indicate whether you want the formats to be displayed as html (<span>).
	 * @return void Returns no value. Only displays the formats.
	 */
	public function show_available_formats( bool $html ) : void {	
		$i = -1;
		$file = __DIR__."/syntax.dat";
		if( @file_exists( $file ) ){
			$file_data = @explode( "\x0a", @file_get_contents( $file ) );
		}
		if( is_array( $file_data ) ){
			while( ($i++) < sizeof( $file_data ) - 2 ){
				$format = @trim( @explode( '=', $file_data[ $i ] )[0] );
				echo ( $html === true ) ? "<span>".$format."</span><br>" : $format . "\x0a" ;
			}
		}
	}

	/*
	 * This function parses the options file called `opts.json`. This file must reside in the same directory as the script.
	 * 
	 * @return array Returns an array, the first element of which is an object containing the paste options such as `delete, paste, list`, etc. 
	 * The second element is also an object containing the expire paste options, such as `1M, 1Y, 2W`, etc.
	 */
	public function parse_opts() : array {
		self::$opts = (object) json_decode( @file_get_contents( __DIR__."/opts.json" ) );
		self::$opts = [ self::$opts->paste, self::$opts->expire ];
		return self::$opts;
	}

	/*
	 * This function displays every available option.
	 * Returns no value.
	 *
	 * @param array $opts An array that holds all the available options.
	 * @return void 
	 */
	public function show_options( array $opts ) : void {	
		$i = -1;
		while( ($i++) < sizeof( $opts ) - 1 ){
			if( $i == 1 )
				echo "<br><br>";
			foreach( $opts[ $i ] as $optn => $optv ){
				echo "<span style='position: absolute;'>".$optn." -> ".$optv."</span><br>";
			}
		}
	}
    
    /*
     * This function displays any errors that might have occured.
     * 
	 * @param bool $html This parameter is used to indicate whether you want the errors to be displayed as html (<span>).
     * @return void Returns no value, just prints the error messages.
     */
	public static function display_errors( bool $html ) : void {	
		$i = -1;
		if( !empty( self::$errors ) ){
			while( ($i++) < sizeof( self::$errors ) - 1 ){
				$error = self::$errors[$i];
				echo ( $html === true ) ? "<span>".$error."</span><br>" : $error . "\x0a" ;
			}
		}
	}
}

?>
