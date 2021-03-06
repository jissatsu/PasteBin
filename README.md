# PasteBin
* A wrapper for the PasteBin API

## API
* In order to use the api you need to register and you will automatically get an api key.
* [API](https://pastebin.com/api)

# Documentation

## Creating a paste

* Required parameters: 
```
\killua\PasteBin::$api_dev_key    -> This is your Api Developer Key
\killua\PasteBin::$api_paste_code -> This is the code that you want to paste
```

* Optional parameters: 
```
\killua\PasteBin::$api_user_key          -> This key allows you to create your paste as a logged in user instead of as a guest
\killua\PasteBin::$api_paste_name        -> This is the name of your paste. If you omit it, the paste will be leaved as Untitled
\killua\PasteBin::$api_paste_format      -> This is the pastes format ( syntax highlighting ). It defaults to `text`
\killua\PasteBin::$api_paste_expire_date -> This is the pastes expire date. It defaults to `never`
\killua\PasteBin::$api_paste_private     -> This indicates whether you want your paste to be public(0), unlisted(1) or private(2)
```

* Creating it:
```
$paste = new \killua\PasteBin();
$paste->set_option( 'paste' );

\killua\PasteBin::$api_dev_key           = "YOUR API_DEVELOPER_KEY";
\killua\PasteBin::$api_user_key          = "YOUR API_USER_KEY";
\killua\PasteBin::$api_paste_name        = "what?";
\killua\PasteBin::$api_paste_format      = "php";
\killua\PasteBin::$api_paste_private     = "1";
\killua\PasteBin::$api_paste_expire_date = "10M";
\killua\PasteBin::$api_paste_code        = "what is that?";

if( $paste->paste() ){
	echo \killua\PasteBin::$httpCode . "\n";
	echo \killua\PasteBin::$httpResponseText . "\n";
}
else{
	// use this to see what errors have occurred if the paste doesnt work 
	// the boolean argument indicates whether you want to display the errors as html (<span>)
	\killua\PasteBin::display_errors( bool );
}

```

* View all available formats:
```
$paste->show_available_formats( boolean ); // use false if you dont want the formats to be displayed as html (<span>)

```

* View all available options:
```
$paste->show_options();

```

## Deleting a paste

* Deleting it:
```
$paste = new \killua\PasteBin();
$paste->set_option( 'delete' );

\killua\PasteBin::$api_dev_key   = "YOUR API_DEVELOPER_KEY";
\killua\PasteBin::$api_user_key  = "YOUR API_USER_KEY";
\killua\PasteBin::$api_paste_key = "YOUR PASTE'S KEY";

if( $paste->delete() ){
	echo \killua\PasteBin::$httpCode . "<br>";
	echo \killua\PasteBin::$httpResponseText . "<br>";
}
else{
	// use this to see what errors have occurred if the paste doesnt work 
	// the boolean argument indicates whether you want to display the errors as html (<span>)
	\killua\PasteBin::display_errors( bool );
}
```

## Getting raw paste output of users pastes including 'private' pastes

* Getting the output:
```
$paste = new \killua\PasteBin();
$paste->set_option( 'show_paste' );

\killua\PasteBin::$api_dev_key   = "YOUR API_DEVELOPER_KEY";
\killua\PasteBin::$api_user_key  = "YOUR API_USER_KEY";
\killua\PasteBin::$api_paste_key = "YOUR PASTE'S KEY";

if( $paste->get_raw() ){
	echo \killua\PasteBin::$httpCode . "<br>";
	echo \killua\PasteBin::$httpResponseText . "<br>";
}
else{
	// use this to see what errors have occurred if the paste doesnt work 
	// the boolean argument indicates whether you want to display the errors as html (<span>)
	\killua\PasteBin::display_errors( bool );
}
```
## Listing pastes created by a user

* Listing the pastes:
```
$paste = new \killua\PasteBin();
$paste->set_option( 'list' );

\killua\PasteBin::$api_dev_key       = "THIS IS YOUR API_DEVELOPER KEY";
\killua\PasteBin::$api_user_key      = "THIS IS YOU API USER_KEY";
\killua\PasteBin::$api_results_limit = "A NUMBER BETWEEN 1 AND 1000"; // this indicates the number of pastes you want to list

if( $paste->listt() ){
	echo \killua\PasteBin::$httpCode . "<br>";
	echo \killua\PasteBin::$httpResponseText . "<br>";
}
else{
	// use this to see what errors have occurred if the paste doesnt work 
	// the boolean argument indicates whether you want to display the errors as html (<span>)
	\killua\PasteBin::display_errors( bool );
}
```

## Downloading a paste

```
$paste = new \killua\PasteBin();
$paste->set_option( 'download' );
$paste->set_download_path( "/home/user/folder/", \killua\PasteBin::$errors );

\killua\PasteBin::$api_paste_key = "THIS IS THE PASTE'S KEY";

if( $paste->download() ){
	echo \killua\PasteBin::$httpCode . "\n";
	echo \killua\PasteBin::$httpResponseText . "\n";
}
else{
	\killua\PasteBin::display_errors( bool );
}
```

## Embedding a paste

```
$paste = new \killua\PasteBin();
\killua\PasteBin::$api_paste_key = "THE PASTE'S KEY";

$embed_js    = $paste->embed( \killua\PasteBin::$api_paste_key )->js;
$embed_frame = $paste->embed( \killua\PasteBin::$api_paste_key )->frame;
```

## Creating An 'api_user_key' Using The API Member Login System

```
$paste = new \killua\PasteBin();

\killua\PasteBin::$api_dev_key       = "THIS IS YOUR API_DEVELOPER KEY";
\killua\PasteBin::$api_user_name     = "YOUR USERNAME";
\killua\PasteBin::$api_user_password = "YOUR PASSWORD";

if( $paste->create_user_key() ){
	echo \killua\PasteBin::$httpCode . "\n";
	echo \killua\PasteBin::$httpResponseText . "\n";
}
else{
	\killua\PasteBin::display_errors( bool );
}

```

## Getting users info and settings

```
$paste = new \killua\PasteBin();
$paste->set_option( 'userdetails' );

\killua\PasteBin::$api_dev_key  = "THIS IS YOUR API_DEVELOPER KEY";
\killua\PasteBin::$api_user_key = "THIS IS YOUR API USER KEY";

if( $paste->get_user_info() ){
	echo PasteBin::$httpCode  . "\n";
	echo PasteBin::$httpResponseText . "\n";
}
else{
	PasteBin::display_errors( bool );
}
```
