<?php

////////// BEGIN OF CONFIG AREA ///////////////////////////////////////////////////////////

defaults('USE_AUTHENTICATION',1);			// Use (internal) authentication - best choice if
// no other authentication is available
// If set to 0:
//  There will be no further authentication. You
//  will have to handle this by yourself!
// If set to 1:
//  You need to change ADMIN_PASSWORD to make
//  this work!
defaults('ADMIN_USERNAME','pzl'); 			// Admin Username
defaults('ADMIN_PASSWORD','tnBNyfwf8r95vbNT');  	// Admin Password - CHANGE THIS TO ENABLE!!!

// (beckerr) I'm using a clear text password here, because I've no good idea how to let
//           users generate a md5 or crypt password in a easy way to fill it in above

//defaults('DATE_FORMAT', "d.m.Y H:i:s");	// German
defaults('DATE_FORMAT', 'Y/m/d H:i:s'); 	// US

defaults('GRAPH_SIZE',200);					// Image size

//defaults('PROXY', 'tcp://127.0.0.1:8080');

////////// END OF CONFIG AREA /////////////////////////////////////////////////////////////

?>