/*
Note: {cp_img_path} will generate the path for you. For example background: url({cp_img_path}image.jpg)
*/

/* Site wide styles */
	body {height:100%;}
	button::-moz-focus-inner { border: 0; }
	input::-moz-focus-inner { border: 0; }
	a::-moz-focus-inner { border: 0; }

#mojo-container img {
	border: 0;
	margin: 0;
	display: inline;
}

.mojo_clear {
	clear: both;
}

.mojo_shift_right {
	text-align: right;
}

/* Modal "OS X style" dropdown */

/* Global JS styles */

	/* Overlay */
	#mojo-overlay {background-color:#000;}


/* Full Page Styles */

	/* Container */
	#mojo-container {
		background: #222 url({cp_img_path}mojobar_bg.jpg);
		color: #A0A0A0;
		font-family: "Lucida Grande",Arial,sans-serif;
		padding-bottom: 0px;
		font-size: 13px;
		z-index: 998;
		border-bottom: 1px solid #000;
		width: 100%;
		min-width: 1014px;
		/*
		Since the bar is subject to the stylings of the page, these are some resets
		*/
		text-align: left;
		display: block;
	}

	/* Mojo is still subject to the CSS of the site its used on, so we'll do some
	further resetting of font-styles as appropriate */
	#mojo-container p, #mojo-container li, #mojo-container div {
		font-size: 13px;
	}

	#mojo-container p {
		font-size: 100%;
		margin: 0;
		color: #A0A0A0;
	}

	#mojo-container em {
		color: #F5C062;
	}

	#mojo-container ul {
		list-style: disc;
		margin: 0;
		padding: 0;
	}

	#mojo-container .mojo_page_refresh_trigger {
		cursor: pointer;
	}

	#mojo-container table {
		color: #A0A0A0;
	}

	#mojo-container a, #mojo-container > a > span, .ui-dialog a {
		color: #fff;
		border: 0;
		padding: 0;
		display: inline;
	}

	#mojo-container #mojo-modal-title {
		color:#000;
		background-color:#ddd;
		text-shadow:0 1px 0 #f4f4f4;
	}

	/* some of these styles have !important to over-ride jquery ui styles */
	#mojo-container .button, .ui-dialog button, .cke_skin_mojo a.cke_dialog_ui_button {
		background: #F5C062 url({cp_img_path}button_back.png) repeat-x center center!important;
		font-weight: bold;
		color: #000!important;
		text-shadow: 0 0 0.7em #F5C062;
		-moz-border-radius:6px;
		-webkit-border-radius:6px;
		border-radius: 6px;
		border: 1px solid #F5C062!important;
		padding: 6px 12px!important;
		font-size: 0.9em;
		cursor: pointer;
		text-transform: uppercase;
		text-decoration: none;
		display: inline;
	}

	#mojo-container #mojo_pagination .button {
		background: #202020 url({cp_img_path}button_back_dark.png) repeat-x center center!important;
		border: 1px solid #202020!important;
		text-shadow: 0 0 0.7em #E3E3E3;
		color: #E3E3E3!important;
	}

	#mojo-container #mojo_pagination .button:hover {
		color: #FFF!important;
	}

	#mojo-container #mojo_pagination .button_inactive {
		background: #D3D3D3 url({cp_img_path}button_back_inactive.png) repeat-x center center!important;
		border: 1px solid #202020!important;
		cursor: default;
		text-shadow: 0 0 0.7em #E3E3E3;
		color: #6E6E69!important;
	}

	#mojo-container table a, #mojo-container #mojo_sub_bar a, #mojo-container #mojo_main_bar a {
		text-decoration: none;
	}

	#mojo-container table a:hover {
		text-decoration: underline;
	}

	.mojo_submit_ajax {
		background: #ddd url({cp_img_path}ajax-loader.gif) no-repeat center center!important;
	}


/* Generic Bar Styles */

	#mojo_bar_logo {
		float: left;
		width: 156px;
		line-height: 77px;
		padding-left: 28px;
		overflow: hidden;
		height: 77px;
		color: #ddd;
		font-style: italic;
	}

	#mojo_bar_logo p {
		margin: 0;
	}

	#mojo_bar_logo:hover {
		text-shadow: 0 0 0.9em #fff;
		color: #fff;
	}

	#mojo_main_bar {
		width: 535px;
		padding-right: 85px;
		margin: auto;
	}

	#mojo_main_bar ul {
		list-style: none;
	}

	#mojo_main_bar ul li {
		display: inline;
		margin: 0;
		padding: 0
	}

	#mojo_main_bar ul li a {
		display: block;
		width: 107px;
		float: left;
		text-align: center;
		height: 27px;
		cursor: pointer;
		padding-top: 48px;
		font-weight: normal;
		font-size: 13px;
		outline: none;
		overflow: hidden;
		background-image: url({cp_img_path}mojobar_menu_sprite.jpg);
		background-repeat: no-repeat;
		border: 0;
	}

	#mojo_main_bar ul li a:hover {
		text-shadow: 0 0 0.9em #fff;
		text-decoration: none;
	}

	#mojo_admin_layouts a {
		background-position: 0 0;
	}

	.mojo_admin_layouts_active a {
		background-position: 0 -154px!important;
	}

	#mojo_admin_layouts a:hover {
		background-position: 0 -77px;
	}

	#mojo_admin_pages a {
		background-position: -107px 0;
	}

	.mojo_admin_pages_active a {
		background-position: -107px -154px!important;
	}

	#mojo_admin_pages a:hover {
		background-position: -107px -77px;
	}

	#mojo_admin_members a {
		background-position: -214px 0;
	}

	.mojo_admin_members_active a {
		background-position: -214px -154px!important;
	}

	#mojo_admin_members a:hover {
		background-position: -214px -77px;
	}

	#mojo_admin_settings a {
		background-position: -321px 0;
	}

	.mojo_admin_settings_active a {
		background-position: -321px -154px!important;
	}

	#mojo_admin_settings a:hover {
		background-position: -321px -77px;
	}

	#mojo_admin_utilities a {
		background-position: -428px 0;
	}

	.mojo_admin_utilities_active a {
		background-position: -428px -154px!important;
	}

	#mojo_admin_utilities a:hover {
		background-position: -428px -77px;
	}

	#mojo_sub_bar {
		width: 280px;
		height: 77px;
		float: right;
	}

	#mojo_sub_bar #mojo_welcome {
		font-size: small;
		margin: 7px 0 -2px 0;
		text-shadow: 0 0 0.7em #202020;
		overflow: hidden;
	}

	#mojo_sub_bar p {
		margin: 0;
		padding: 0;
		line-height: 30px;
		font-size: 90%;
		color: #A0A0A0;
		font-family: Arial,sans-serif;
	}

	#mojo_sub_bar a {
		font-weight: normal;
		display: inline;
	}

	#mojo_sub_bar a:hover {
		text-decoration: underline;
	}

	#mojo_sub_bar .mojo_logout_icon {
		margin-bottom: -2px;
		margin-right: 4px;
	}

	#mojo_bar_view_mode {
		font-size: x-small;
		width: 44px;
		background: url({cp_img_path}view_mode_back.jpg) no-repeat top left;
		color: #A0A0A0;
		text-align: center;
		float: left;
		height: 100%;
		line-height: 12px;
		margin-right: 20px;
		cursor: pointer;
		font-size: x-small;
		padding-left: 6px;
	}

	#mojo_bar_view_mode p {
		line-height: 135%;
		text-transform: lowercase;
		padding-top: 7px;
		font-size: x-small;
		letter-spacing: 1px;
		font-family: "Lucida Grande",Arial,sans-serif;
		color: #FFF;
	}

	#mojo_bar_view_mode:hover {
		color: #FFF;
	}

	#mojo_site_structure #mojo_dirst_drop_target{
		background: none;
	}

	#mojo_site_structure li.mojo_page_hidden > .ie_fix {
		background: url({cp_img_path}page_background_hidden.png) repeat-x top left;
	}

	#mojo_site_structure li .ie_fix {
		background: url({cp_img_path}page_background.png) repeat-x top left;
	}

	#mojo_site_structure li .ie_fix_hover {
		background: url({cp_img_path}page_add_hover.png) repeat-x left top;
		-webkit-border-radius: 4px;
		-moz-border-radius: 4px;
		border-radius: 4px;
	}

	#mojo_site_structure li {
		color: #333;
		line-height: 29px;
		display: block;
		float: left;
		padding: 0px 0 0px 40px;
		clear: both;
		-webkit-user-select: none;
	}

	#mojo_site_structure li > div {
		cursor: move;
		padding: 0 0px 0 10px;
	}

	#mojo_site_structure li.mojo_page_hidden > .ie_fix .mojo_add_page_inline {
		background:  url({cp_img_path}page_add_sm_button_hidden.png) no-repeat left top;
	}

	#mojo_site_structure .mojo_add_page_inline {
		background:  url({cp_img_path}page_add_sm_button.png) no-repeat left top;
		width: 29px;
		overflow: hidden;
		text-indent: -5000px;
		display: block;
		float: right;
	}

	#mojo_site_structure li > .ie_fix:hover > .mojo_page_edit_delete > .mojo_page_link_inline {
		width: 21px;
		border: 0;
	}

	#mojo_site_structure .mojo_page_link_inline {
		background: #202020 url({cp_img_path}page_link.png) no-repeat left top;
		width: 21px;
		text-indent: -5000px;
		display: block;
		float: right;
	}

	#mojo_site_structure .mojo_add_page_inline:hover, #mojo_site_structure .mojo_page_link_inline:hover, #mojo_site_structure li.mojo_page_hidden > .ie_fix .mojo_add_page_inline:hover {
		background-position: left bottom;
	}

	#mojo_site_structure li a {
		color: #333;
	}

	.mojo_site_structure_placeholder {
		-moz-border-radius: 4px;
		-webkit-border-radius: 4px;
		border-radius: 4px;
		height: 10px;
		width: 176px;
		background: #F5C062;
		opacity: 0;
		visibility: hidden;
	}

	.mojo_page_edit_delete {
		float: right;
		margin-left: 25px;
	}

	.mojo_page_delete {
		margin-right:-3px;
	}

	#collapse_tab {
		padding: 3px 0;
		text-align: center;
		-moz-border-radius-bottomleft: 6px;
		-webkit-border-bottom-left-radius: 6px;
		-moz-border-radius-bottomright: 6px;
		-webkit-border-bottom-right-radius: 6px;
		border-radius: 0 0 6px 6px;
		background: #333 url({cp_img_path}button_close.gif) no-repeat center center;
		cursor: pointer;
		position: absolute;
		color: #86959E;
		right: 229px;
		width: 45px;
		height: 11px;
	}

	#mojo-container .shun, .cke_skin_mojo .shun {
		margin-bottom: 15px;
	}

/* Editor Mode Styles */

	#mojo_editor_overlay {
		display: none!important;
	}

	.mojo_editable_layer {
		background: #FFEB72;
		border: 3px solid #000;
		position:absolute;
		margin: -3px -6px;
		padding: 0px;
		cursor: pointer;
		-moz-border-radius: 6px;
		-webkit-border-radius: 6px;
		border-radius: 6px;
		opacity: 0.4;
	}

	.mojo_editable_layer_header {
		cursor: pointer;
		opacity: .99;
		margin: -52px 0 0 0;
		padding: 0 0 3px 0;
		float: right;
		background:  url({cp_img_path}mojo_editable_layer_header_background.png) no-repeat center bottom;
	}

	.mojo_global {
		border-color: #c00;
	}

	.mojo_editable_layer_header > p {
		position: relative;
		border: 7px solid #333;
		width: auto;
		background: none repeat scroll 0 0 #474747 !important;
		color: #cbcbcb!important; /* this element is commonly in site-styled areas, and needs !important */
		-moz-border-radius: 4px;
		-webkit-border-radius: 4px;
		border-radius: 4px;
		margin: 5px !important;
		padding: 3px !important;
		text-align: center;
		line-height: 20px;
		font-size: 12px !important;
		text-transform: capitalize;
	}

	.mojo_edit_in_place {
		z-index: 1003;
	}

	#mojo_reveal_page {
		border-bottom:1px solid #202020;
		background: #202020 url({cp_img_path}mojo_reveal_page_background.png) repeat-x left bottom;
		padding: 0 50px 17px 50px;
	}

	#mojo_reveal_page h3 {
		color: #fff;
		font-weight: normal;
	}

	#mojo_reveal_page_notice {
		background: #f9e6af;
		-moz-border-radius: 4px;
		-webkit-border-radius: 4px;
		border-radius: 4px;
		width: auto;
		margin: 0;
		position:absolute;
		float:left;
		top:77px;
		left:50%;
		max-width: 300px;
		padding: 5px 10px 5px 36px;
		font-weight: bold;
		line-height: inherit;
	}

	#mojo_reveal_page_notice.success {
		background: #f9e6af url({cp_img_path}notice_success.png) no-repeat 10px center;
		color: #080;
	}

	.layout_edit_form_dialog p {
		background: #f9e6af url({cp_img_path}notice_error.png) no-repeat 10px center;
		color: #c00;
		-moz-border-radius: 4px;
		-webkit-border-radius: 4px;
		border-radius: 4px;
		width: auto;
		margin: 0;
		padding: 5px 10px 5px 36px;
		font-weight: bold;
	}

	#mojo_reveal_page_notice.error {
		background: #f9e6af url({cp_img_path}notice_error.png) no-repeat 10px center;
		color: #c00;
	}

	#mojo_reveal_page_notice.error {
		padding: 5px 5px 5px 36px;
		background: #f9e6af url({cp_img_path}notice_error.png) no-repeat 10px center;
		color: #B82E28;
		border: 1px solid #B82E28;
		-moz-border-radius: 4px;
		-webkit-border-radius: 4px;
		border-radius: 4px;
	}

	.ui-dialog-content .error {
		margin: 0 25px;
		padding: 5px 5px 5px 36px;
		background: #f9e6af url({cp_img_path}notice_error.png) no-repeat 10px center;
		color: #B82E28;
		border: 1px solid #B82E28;
		-moz-border-radius: 4px;
		-webkit-border-radius: 4px;
		border-radius: 4px;
	}

	#mojo_reveal_page_notice.notice {
		background: #f9e6af url({cp_img_path}notice_notice.png) no-repeat 10px center;
		color: #333;
	}

	#mojo_reveal_page_notice.system_notice {
		background: #202020 url({cp_img_path}notice_notice.png) no-repeat 10px center;
		-webkit-border-radius: 4px;
		-webkit-border-top-left-radius: 0px;
		-webkit-border-top-right-radius: 0px;
		-moz-border-radius: 4px;
		-moz-border-radius-topleft: 0px;
		-moz-border-radius-topright: 0px;
		border-radius: 4px;
		border-top-left-radius: 0px;
		border-top-right-radius: 0px;
		margin-top: -1px;
		border: 1px solid #000;
		border-top: 0;
	}

	#mojo_breadcrumbs {
		margin: 0;
		padding: 0 35px;
		line-height: 30px;
		height: 30px;
		background: #202020;
	}

	#mojo_breadcrumbs #mojo_ajax_page_loader {
		text-align: center;
		position: absolute;
		width: 100%;
		left: 0;
		top: 77px;
	}

	#mojo_reveal_page_content .mojo_diagnostic {
		width: 150px;
		float: right;
	}

	.mojo_diagnostic #mojo_copyright {
		font-size: 80%;
		margin-top: 15px;
	}

	#mojo_reveal_page_content .mojo_diagnostic_supplement {
		margin-right: 200px;
	}

	#mojo_reveal_page_content .mojo_diagnostic_supplement p {
		margin-bottom: 15px;
	}

	#mojo_reveal_page_content > div, #mojo_reveal_page_content > form > div {
		background: #404040;
		color: #A0A0A0;
		padding: 10px;
		-moz-border-radius: 6px;
		-webkit-border-radius: 6px;
		border-radius: 6px;
		margin: 0px auto 10px auto;
	}

	#mojo_reveal_page_content > div.hidden, #mojo_reveal_page_content > form > div.hidden {
		display: none;
	}

	#mojo_reveal_page_content div.shrinkwrap {
		width: 800px;
		padding: 15px;
	}

	#mojo-container div.shrinkwrap > a.shrinkwrap_submit {
		text-decoration: underline;
		font-size: 105%;
	}

	#mojo_pagination {
		float:right;
	}

	.mojo_reveal_page_close {
		color: #A0A0A0;
		font-size: small;
		text-decoration: none;
		display: block;
		margin: -22px auto 0 auto;
		position: absolute;
		height: 30px;
		width: 30px;
		overflow: hidden;
		text-indent: -5000px;
		background: url({cp_img_path}mojo_reveal_page_close.png) no-repeat top center;
	}

	.mojo_reveal_page_close:hover {
		background-position: bottom center;
	}

	#mojo_reveal_page_back {
		margin-left: 25px;
	}

	#mojo_reveal_current_page {
		display: none;
		margin-left: 10px;
		padding-left: 16px;
		background: url({cp_img_path}arrow_right.png) no-repeat center left;
	}

	#mojo_reveal_page_content table.mojo_table {
		width: 830px;
		border-collapse: collapse;
		border: 0;
		margin: 0 auto 10px auto;
		font-size: 13px;
		background: #202020 url({cp_img_path}table_head_830.png) no-repeat top center;
	}

	#mojo_reveal_page_content table tbody {
		background: #2b2b2b url({cp_img_path}table_body_830.png) no-repeat bottom center;
	}

	#mojo_reveal_page_content table thead th {
		line-height: 44px;
		color: #fff;
		text-shadow: 0 0 0.7em #000;
		text-transform: uppercase;
		font-weight: bold;
		text-align: left;
		padding: 0 20px;
		font-size: 12px;
		background: inherit;
		border: 0;
	}

	#mojo_reveal_page_content table td {
		margin: 0;
		padding: 5px 20px;
		background: inherit;
		border: 0;
		color: #A0A0A0;
		font-size: inherit;
		text-align: inherit;
	}

	table .mojo_form_controls {
		width: 50%;
	}

	table .mojo_table_edit {
		text-align: right;
	}

	#mojo-container label {
		margin: 0;
		color: #a0a0a0;
		padding-right: 10px;
		float: left;
		width: 200px;
		text-align: right;
		line-height: 35px;
	}

	#mojo-container form p {
		line-height: 35px;
		clear: left;
	}

	#mojo-container form fieldset p, #mojo-container form fieldset label {
		line-height: 100%;
		margin: 0;
	}

	#mojo-container fieldset {
		clear: left;
		border: 0;
		margin-bottom: 15px;
	}

	#mojo-container fieldset legend {
		color: #a0a0a0; /* IE... wth? */
	}

	#mojo-container fieldset label {
		float: none;
		width: auto;
		color: #a0a0a0;
	}

	.mojo_textbox {
		-moz-border-radius: 6px;
		-webkit-border-radius: 6px;
		border-radius: 6px;
		border: 1px solid #000;
		padding: 6px;
		background: #fff;
		min-width: 250px;
	}

	#mojo_partial_url_title input {
		max-width: 150px;
	}

	.mojo_textbox input, .mojo_textbox textarea, .mojo_textbox select {
		border: 0;
		font-weight: bold;
		color: #333;
	}

	.mojo_textbox span {
		font-style: italic;
	}

	#mojo_layout_edit_form #layout_content {
		width: 99%;
		height: 250px;
	}
	
	#mojo-container button a {
		color: black;
		text-decoration: none
	}

	#mojo_reveal_page_content table h4 {
		padding: 15px 0 0 0;
		font-weight: bold;
	}

	#mojo_reveal_page_content table tr.odd td {
		background: #333
	}

	#mojo_reveal_page_content table tr:hover td {
		background: #535353;
		color: #C2C2C2;
	}

	#mojo_reveal_page_content textarea, #mojo_reveal_page_content input[type=text], #mojo_reveal_page_content input[type=password] {
		min-width: 250px;
		padding: 3px;
	}

	.cke_skin_mojo .cke_button_mojoimage .cke_icon{
		background-position:0 -576px!important;
	}

/* jQuery UI overrides */

	div.ui-widget-header {
		border: 0;
		background: #222 url({cp_img_path}mojobar_bg.jpg);
		color: #FFF;
		line-height: 33px;
		text-transform: uppercase;
		-moz-border-radius:0px!important;
		-webkit-border-radius:0px!important;
		border-radius: 0px!important;
	}

	.ui-dialog {
		border: 5px solid #000!important;
		padding: 0;
	}

	.ui-widget-content {
		background-image: none;
		background-color: #333;
		border: 0;
	}

	.ui-button .ui-button-text {
		padding: 0;
		font-family:"Lucida Grande",Arial,sans-serif!important;
		font-size: 12px!important; /* computed size of other buttons */
	}

	.ui-widget-overlay {
		z-index: 1002! important; /* this little important override is needed for jquery libs to play nicely */
	}

	.ui-dialog {
		z-index: 1003 !important;
	}
