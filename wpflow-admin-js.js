/*
+----------------------------------------------------------------+
|				
|	WordPress 2.6 Plugin: wordpress-flowplayer 0.3			
|	Copyright (c) 2008 Lee Ferrett			
|			
|	File Written By:					
|	- Lee Ferrett
|	- http://jpegserv.com				
|								
|	File Information:							
|	- wordpress flowplayer Admin Javascript File			
|	- wp-content/plugins/wordpress-flowplayer/wpflow-admin-js.js	 			
|								
+----------------------------------------------------------------+
*/

// Function: Insert wordpress flow Quick Tag
function insertFlow(where, myField) {
	var wpflow_href = prompt(flow_admin_text_enter_flow_href);
	var wpflow_width = prompt(flow_admin_text_enter_flow_width);
	var wpflow_height = prompt(flow_admin_text_enter_flow_height);
		if(where == 'code') {
			edInsertContent(myField, '[flow href="' + wpflow_id + '" width="' + wpflow_width + '" height="' + wpflow_height + '"]');
		} else {
			return '[flow href="' + wpflow_href + '" width="' + wpflow_width + '" height="' + wpflow_height + '"]';
		}
}