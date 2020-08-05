/*
 * SimpleModal mojo Style Modal Dialog
 * http://www.ericmmartin.com/projects/simplemodal/
 * http://code.google.com/p/simplemodal/
 *
 * Copyright (c) 2009 Eric Martin - http://ericmmartin.com
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Revision: $Id: mojo.css 214 2009-09-17 04:53:03Z emartin24 $
 *
 */

body {
	height:100%;
}

/*
#mojo-modal-content, #mojo-modal-data {display:none;}
*/

/* Overlay */
#mojo-overlay {
	background-color:#000;
}

/* Container */
#mojo-container {
	background-color: #3c3c3c;
	color: #737373;
	font-size:13px;
	font-family:"Lucida Grande",Arial,sans-serif;
	width: 475px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	border-radius: 6px;
	border: 5px solid #000;
	min-width: 475px;
	min-height: 350px;
}

#mojo-container .error {
	color: #F5C062;
}

#mojo_login_error {
	margin: 5px 0 10px 0;
}

#mojo-modal-data {
	padding: 0;
	text-align: center;
	margin: 0 40px;
}

#mojo-container a {
	color:#ddd;
}

#mojo-container #mojo-modal-title {
	background: #222 url({cp_img_path}mojobar_bg.jpg);
	padding: 0 0 0 10px;
	line-height: 71px;
	height: 71px;
}

#mojo-container .close {
	display:none;
}

#mojo-container h2 {
	font-size: 36px;
	color: #000;
	text-shadow:0 1px 0 #666;
	font-weight: bold;
	line-height: 60px;
	margin: 0;
	padding: 0;
	border: 0;
	font-family: sans-serif;
}

#mojo-container p {
	font-size: 14px;
	font-family: sans-serif;
}

#mojo-modal-content button::-moz-focus-inner { border: 0;}

#mojo-modal-content .button {
	background: #F5C062 url({cp_img_path}button_back.png) repeat-x center center;
	font-weight: bold;
	color: #000;
	text-shadow: 0 0 0.7em #F5C062;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius: 6px;
	border: 1px solid #F5C062;
	padding: 6px 12px;
	font-size: 0.9em;
	cursor: pointer;
	text-transform: uppercase;
	margin-right: 26px;
}

#mojo_submit {
	float: right;
}

.mojo_login_field label {
	float: left;
	width: 7em;
	text-align: right;
	padding-top: 10px;
}

.mojo_login_field input {
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	border-radius: 6px;
	border: 1px solid #000;
	padding: 10px 6px;
	margin-left: 1em;
	width: 246px;
	color: #202020;
	background: #FFF;
	font-weight: bold;
}

.mojo_login_field {
	margin-bottom: 15px;
	text-align: left;
}

.mojo_submit_holder {
	text-align: left;
	line-height: 27px;
	margin-top: 15px;
	margin-left: 7.5em;
}