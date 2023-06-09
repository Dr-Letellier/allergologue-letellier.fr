<?php $pma_http_url = ''; ?>
/* STYLE FOR THE RIGHT (MAIN) FRAME */

#mainFrameset, #leftFrameset, frameset, frame {
    margin:              0 !important;
    padding:             0 !important;
    border:              0 none #ffffff !important;
}
#leftFrameset {
    border-right:        1px solid #585880 !important;
}

/* basic font setup */
body,
th, td,
div, span,
p, h1, h2, h3, h4, h5, h6,
a:link, a:active, a:visited,
input, button, select, textarea, legend, fieldset, label, form {
    font-family:         Arial, Helvetica, Verdana, Geneva, sans-serif;
    font-size:           11px;
}
/* buttons in some browsers (eg. Konqueror) are block elements, this breaks design */
button { display: inline; }

/* body main style */
body {
    color:               #333333;
    background-color:    #ffffff;
    margin:              0;
    padding:             26px 3px 3px 3px;
    background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/wbg_right.jpg);
    background-position: 100% 100%;
    background-repeat:   no-repeat;
    background-attachment: fixed;
}

/* headlines */
h1, h1 a:link, h1 a:active, h1 a:visited {
    font-size:           18px;
    font-weight:         bold;
}
h2, h2 a:link, h2 a:active, h2 a:visited {
    font-size:           16px;
    font-weight:         bold;
}
h3, h3 a:link, h3 a:active, h3 a:visited {
    font-size:           14px;
    font-weight:         bold;
}

/* pre, code, tt */
pre, tt, code {
    font-family:         "Courier New", Courier, monospace;
    font-size:           11px;
    color:               #333333;
}

/* italic fonts */
i, em, address{
    font-style:          normal;
    color:               #666666;
}

/* links */
a:link, a:visited, a:active {
    text-decoration:     none;
    font-weight:         bold;
    color:               #696ab5;
}
a:hover {
    text-decoration:     none;
    color:               #585880;
}
a.drop:link, a.drop:visited, a.drop:active{
    color:               #aa0000;
}
a.drop:hover {
    color:               #ffffff;
    background-color:    #aa0000;
    text-decoration:     none;
}

/* horizontal ruler */
hr {
    color:               #585880;
    background-color:    #585880;
    border:              1px none #585880;
    height:              1px;
    margin-top:          5px;
    margin-bottom:       5px;
}

/* image */
img {
    vertical-align:      middle;
    margin:              0 2px 0 2px;
}

/* tables */
table {
    border: 1px none #000000;
}
th, .tblHeaders, tr.tblHeaders td, tr.tblHeaders th,
.tblFooters, tr.tblFooters td, tr.tblFooters th,
.tblHeadError, .tblHeadWarn {
    white-space:         nowrap;
    border-bottom:       1px solid #585880;
    border-top:          1px solid #cccccc;
    background-repeat:   repeat-x;
    background-position: 0 0;
}
th, .tblFooters {
    font-weight:         bold;
    color:               #585880;
    background-color:    #e5e5e5;
    border-top:          1px solid #cccccc;
    border-bottom:       1px solid #333333;
    white-space:         nowrap;
    background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/tbg_th1.png);
    background-repeat:   repeat-x;
    background-position: 0 0;
}
.tblFooters {
    text-align: right;
}
.tblHeaders, .tblHeaders td, .tblHeaders th, .tblHeaders a:link, .tblHeaders a:active, .tblHeaders a:visited {
    font-weight:         bold;
}
.tblHeaders, tr.tblHeaders td, tr.tblHeaders th {
    color:               #585880;
    background-color:    #b4cae9;
    background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/tbg_th0.png);
}
.tblcomment {
    font-size:           10px;
    font-weight:         normal;
    color:               #000000;
}
.tblFooters, .tblFooters a:link, .tblFooters a:active, .tblFooters a:visited {
    font-weight:         normal;
    color:               #585880;
}

.tblHeaders a:link, .tblHeaders a:active, .tblHeaders a:visited,
.tblFooters a:link, .tblFooters a:active, .tblFooters a:visited {
    text-decoration:     underline;
}
.tblHeaders a:hover, .tblHeadWarn a:hover, .tblHeadError a:hover, .tblFooters a:hover {
    text-decoration:     none;
}
th.td {
    font-weight:         normal;
    color:               transparent;
    background-color:    transparent;
    background-image:    none;
    border-top:          1px none #cccccc;
    border-bottom:       1px solid #333333;
}

table tr.odd th,
table tr.odd,
table tr.even th,
table tr.marked th {
    background-image:    none;
    border:              none;
}

/* hovered table rows */
table tr.odd:hover th,
table tr.even:hover th,
table tr.hover th {
    background-color: <?php echo $GLOBALS['cfg']['BrowsePointerColor']; ?>;
}

/* -- FORM ELEMENTS */
form {
    padding:             0px 0px 0px 0px;
    margin:              0px 0px 0px 0px;
}
input, select {
    vertical-align:      middle;
    margin-top:          1px;
    margin-bottom:       1px;
}
select {
    color:               #585880;
}
select optgroup, select option {
    font-family:         Arial, Helvetica, Verdana, Geneva, sans-serif;
    font-size:           11px;
    font-style:          normal;
}
input[type=button], input[type=submit], input[type=reset] {
    color:               #585880;
    font-weight:         bold;
    padding-left:        2px;
    padding-right:       2px;
    border:              1px solid #585880;
    background-color:    #e5e5e5;
    cursor:              pointer;
}
input[type=text], input[type=file], input[type=password], input.textfield {
    color:               #000000;
    border:              1px solid #585880;
}
fieldset {
    border:              #585880 solid 1px;
    padding:             0.5em;
}
fieldset fieldset {
    padding:             0.5em;
}
legend {
    color:               #333333;
    font-weight:         bold;
    background-color:    #ffffff;
    padding:             2px;
}
button.mult_submit {
    border:              none;
    background-color:    transparent;
}

/* dfn */
dfn {
    font-style:          normal;
}


/* message boxes: warning, error, confirmation */
.notice {
    color: #000000;
    background-color: #FFFFDD;
}
h1.notice,
div.notice {
    margin: 0.5em 0 0.5em 0;
    border: 0.1em solid #FFD700;
    <?php if ( $GLOBALS['cfg']['ErrorIconic'] ) { ?>
    background-image: url(../phpmyadmin/themes/arctic_ocean/img/s_notice.png);
    background-repeat: no-repeat;
        <?php if ( $GLOBALS['text_dir'] === 'ltr' ) {?>
    background-position: 10px 50%;
    padding: 10px 10px 10px 36px;
        <?php } else {?>
    background-position: 99% 50%;
    padding: 10px 5% 10px 10px;
        <?php }?>
    <?php } else {?>
    padding: 0.5em;
    <?php }?>
}
.notice h1 {
    border-bottom: 0.1em solid #FFD700;
    font-weight: bold;
    text-align: <?php echo $left; ?>;
    margin: 0 0 0.2em 0;
}

.warning {
    color: #CC0000;
    background-color: #FFFFCC;
}
h1.warning,
div.warning {
    margin: 0.5em 0 0.5em 0;
    border: 0.1em solid #CC0000;
    <?php if ( $GLOBALS['cfg']['ErrorIconic'] ) { ?>
    background-image: url(../phpmyadmin/themes/arctic_ocean/img/s_warn.png);
    background-repeat: no-repeat;
        <?php if ( $GLOBALS['text_dir'] === 'ltr' ) {?>
    background-position: 10px 50%;
    padding: 10px 10px 10px 36px;
        <?php } else {?>
    background-position: 99% 50%;
    padding: 10px 5% 10px 10px;
        <?php }?>
    <?php } else {?>
    padding: 0.5em;
    <?php }?>
}
.warning h1 {
    border-bottom: 0.1em solid #cc0000;
    font-weight: bold;
    text-align: <?php echo $left; ?>;
    margin: 0 0 0.2em 0;
}

.error {
    background-color: #FFFFCC;
    color: #ff0000;
}
h1.error,
div.error {
    margin: 0.5em 0 0.5em 0;
    border: 0.1em solid #ff0000;
    <?php if ( $GLOBALS['cfg']['ErrorIconic'] ) { ?>
    background-image: url(../phpmyadmin/themes/arctic_ocean/img/s_error.png);
    background-repeat: no-repeat;
        <?php if ( $GLOBALS['text_dir'] === 'ltr' ) {?>
    background-position: 10px 50%;
    padding: 10px 10px 10px 36px;
        <?php } else {?>
    background-position: 99% 50%;
    padding: 10px 5% 10px 10px;
        <?php }?>
    <?php } else {?>
    padding: 0.5em;
    <?php }?>
}
div.error h1 {
    border-bottom: 0.1em solid #ff0000;
    font-weight: bold;
    text-align: <?php echo $left; ?>;
    margin: 0 0 0.2em 0;
}


.confirmation {
    background-color: #FFFFCC;
}
fieldset.confirmation {
    border: 0.1em solid #FF0000;
}
fieldset.confirmation legend {
    border-left: 0.1em solid #FF0000;
    border-right: 0.1em solid #FF0000;
    font-weight: bold;
    <?php if ( $GLOBALS['cfg']['ErrorIconic'] ) { ?>
    background-image: url(../phpmyadmin/themes/arctic_ocean/img/s_really.png);
    background-repeat: no-repeat;
        <?php if ( $GLOBALS['text_dir'] === 'ltr' ) {?>
    background-position: 5px 50%;
    padding: 0.2em 0.2em 0.2em 25px;
        <?php } else {?>
    background-position: 97% 50%;
    padding: 0.2em 25px 0.2em 0.2em;
        <?php }?>
    <?php }?>
}
/* end messageboxes */


/* -- PDF SCRATCHBOARD -- */
.pdflayout {
    overflow:            hidden;
    clip:                inherit;
    background-color:    #FFFFFF;
    display:             none;
    border:              1px solid #585880;
    position:            relative;
}

.pdflayout_table {
    background:          #b4cae9;
    color:               #333333;
    overflow:            hidden;
    clip:                inherit;
    z-index:             2;
    display:             inline;
    visibility:          inherit;
    cursor:              move;
    position:            absolute;
    font-size:           11px;
    border:              1px dashed #585880;
}

/* -- PRINT -- */
.print {
    color:               #000000;
}

/* -- MySQL PARSER -- */
.syntax {
    font-family:         sans-serif;
    font-size:           10px;
}
.syntax_comment {
    font-family:         sans-serif;
    font-size:           10px;
    color:               #999999;
    font-style:          italic;
    padding-left:        4px;
    padding-right:       4px;
}
.syntax_digit {
    font-family:         sans-serif;
    font-size:           10px;
    color:               #aa0000;
}
.syntax_digit_hex {
    font-family:         sans-serif;
    font-size:           10px;
    color:               #ff0000;
}
.syntax_digit_integer {
    font-family:         sans-serif;
    font-size:           10px;
    color:               #aa0000;
}
.syntax_digit_float {
    font-family:         sans-serif;
    font-size:           10px;
    color:               #aa0000;
}
.syntax_punct {
    font-family:         sans-serif;
    font-size:           10px;
    color:               #aa0000;
}
.syntax_alpha {
    font-family:         sans-serif;
    font-size:           10px;
}
.syntax_alpha_columnType {
    font-family:         sans-serif;
    font-size:           10px;
    color:               #006600;
    text-transform:      uppercase;
}
.syntax_alpha_columnAttrib {
    font-family:         sans-serif;
    font-size:           10px;
    color:               #006600;
    text-transform:      uppercase;
}
.syntax_alpha_reservedWord {
    font-family:         sans-serif;
    font-size:           10px;
    color:               #dc14a1;
    text-transform:      uppercase;
}
.syntax_alpha_functionName {
    font-family:         sans-serif;
    font-size:           10px;
    color:               #483d8b;
    text-transform:      uppercase;
}
.syntax_alpha_identifier {
    font-family:         sans-serif;
    font-size:           10px;
}
.syntax_alpha_charset {
    font-family:         sans-serif;
    font-size:           10px;
}
.syntax_alpha_variable {
    font-family:         sans-serif;
    font-size:           10px;
}
.syntax_quote {
    font-family:         sans-serif;
    font-size:           10px;
    color:               #483d8b;
    white-space:         pre;
}
.syntax_quote_backtick {
    font-family:         sans-serif;
    font-size:           10px;
}


/* -- SERVER & DB INFO -- */

    /* for PMA version below 2.6.4 */
    .serverinfo, #serverinfo {
        font-size:           12px;
        font-weight:         bold;
        padding:             0px 0px 10px 0px;
        margin:              0px;
        white-space:         nowrap;
        vertical-align:      middle;
    }
    .serverinfo a:link, .serverinfo a:active, .serverinfo a:visited  {
        font-size:           12px;
        font-weight:         bold;
    }
    .serverinfo a img {
        vertical-align:      middle;
        margin:              0px 1px 0px 1px;
    }
    .serverinfo div {
        background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/item_ltr.png);
        background-repeat:   no-repeat;
        background-position: 50% 50%;
        width:               16px;
        height:              16px;
    }

    /* for PMA version 2.6.4 and higher */
    #serverinfo {
        background-color:    #ffffff;
        font-size:           12px;
        font-weight:         bold;
        padding:             5px 3px 5px 3px;
        margin-top:          5px;
        white-space:         nowrap;
        vertical-align:      middle;
        text-align:          center;
        border-bottom:       1px solid #333333;
/*
        position:            fixed;
        _position:           absolute;
        top:                 0px;
        _top:                expression(eval(document.body.scrollTop));
        left:                0px;
        _left:               expression(eval(document.body.scrollLeft));
        height:              50px;
        width:               100%;
        _width:               expression(eval(document.body.offsetWidth-18));
*/
    }
    #serverinfo a:link, #serverinfo a:active, #serverinfo a:visited  {
        font-size:           12px;
        font-weight:         bold;
    }
    #serverinfo a img {
        vertical-align:      middle;
        margin:              0px 1px 0px 1px;
    }
    #serverinfo .separator img {
        width:               9px;
        height:              11px;
        margin:              0px 2px 0px 2px;
        vertical-align:      middle;
    }


/* -- NAVIGATION -- */

    /* backwards compatibility for PMA version below 2.6.4 */
    td.tab {
        border-top:          1px solid #585880;
        border-right:        1px solid #585880;
        border-left:         1px solid #585880;
        border-bottom:       none;
        border-radius:       2px;
        -moz-border-radius:  2px;
    }
    table.tabs {
        border-top:          none;
        border-right:        none;
        border-left:         none;
        border-bottom:       1px solid #585880;
    }
    .nav {
        color:               #000000;
        background-color:    #b4cae9;
        background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/tbg_nav0.png);
        background-repeat:   repeat-x;
        background-position: center;
        height:              24px;
        padding:             0px 0px 0px 0px;
        margin:              0px;
    }
    .nav a:link, .nav a:visited, .nav a:active{
        color:               #000000;
        text-decoration:     none;
    }
    .nav a:hover{
        color:               #585880;
        text-decoration:     none;
    }
    .navNormal, .navActive, .navDrop {
        color:               #666666;
        font-weight:         bold;
        height:              24px;
        padding:             0px 5px 0px 5px;
        background-repeat:   repeat-x;
       background-position: 0px 0px;
    }
    .navNormal img, .navDrop img {
        filter:              progid:DXImageTransform.Microsoft.Alpha(opacity=50);
        -moz-opacity:        0.5;
        opacity:             0.5;
    }
    .navNormal a img, .navDrop a img {
        filter:              progid:DXImageTransform.Microsoft.Alpha(opacity=99);
        -moz-opacity:        0.99;
        opacity:             0.99;
   }
    .navNormal {
        background-color:    #b4cae9;
        background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/tbg_nav0.png);
    }
    .navActive {
        background-color:    #ffffff;
        background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/tbg_nav2.png);
        border:              none;
        margin:              0px;
        padding:             5px;
    }
    .navDrop {
        background-color:    #e9c7b4;
        background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/tbg_nav3.png);
    }
    .navActive a:link, .navActive a:active, .navActive a:visited {
        color:               #585880;
    }
    .navActive a:hover {
        color:               #000000;
    }
    .navNormal a:link, .navNormal a:active, .navNormal a:visited {
        color:               #000000;
    }
    .navNormal a:hover {
        color:               #585880;
    }
    .navDrop a:link, .navDrop a:active, .navDrop a:visited {
        color:               #000000;
    }
    .navDrop a:hover {
        color:               #aa0000;
    }
    .navSpacer {
        width:               1px;
        background-color:    #333333;
        background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/tbg_nav1.png);
        background-repeat:   no-repeat;
        background-position: center top;
    }
    .navSpacer img {
        margin:              0px;
        padding:             0px;
    }

    /* for PMA version 2.6.4 and greater */
    ul#topmenu {
        list-style-type: none;
        background-color:    #ffffff;
        background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/tbg_nav0.png);
        background-repeat:   repeat-x;
        background-position: center bottom;
        border:              none;
        color:               #000000;
        font-weight:         bold;
        margin:              0;
        padding:             8px 3px 0 3px;
        white-space:         nowrap;
        position:            fixed;
        _position:           absolute;
        top:                 0;
        _top:                expression(eval(document.body.scrollTop-4));
        _left:               expression(eval(document.body.scrollLeft));
        left:                0;
        width:               100%;
        _width:               expression(eval(document.body.offsetWidth-18));
    }
    
    ul#topmenu li {
        float:               left;
        margin:              0;
        padding:             0;
        vertical-align:      middle;
        border:              none;
    }
    
    /* default tab styles */
    span.tab, span.tab:hover,
    span.tabcaution, span.tabcaution:hover,
    a.tab, a.tab:hover,
    a.tabcaution, a.tabcaution:hover,
    a.tabactive, a.tabactive:hover,
    a.tab:link,        a.tab:active,        a.tab:visited,        a.tab:hover,
    a.tabactive:link,  a.tabactive:active,  a.tabactive:visited,  a.tabactive:hover,
    a.tabcaution:link, a.tabcaution:active, a.tabcaution:visited, a.tabcaution:hover {
        background-color:    transparent;
        background-repeat:   repeat-x;
        background-position: center top;
        border:              none;
        margin:              0;
        padding:             4px 2px 4px 2px;
        border-radius-topleft:       0;
        border-radius-topright:      0;
        -moz-border-radius-topleft:  0;
        -moz-border-radius-topright: 0;
    }



    /* enabled drop/empty tabs */
    a.tab:link, a.tab:active, a.tab:visited, a.tab:hover,
    a.tabactive:link,  a.tabactive:active,  a.tabactive:visited,  a.tabactive:hover  {
        color: #585880;
    }
    a.tab:hover  {
        color:               #ffffff;
        background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/tbg_nav4.png);
        background-position: center bottom;
    }
    a.tabactive:link,  a.tabactive:active,  a.tabactive:visited,  a.tabactive:hover  {
        background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/tbg_nav2.png);
        background-color:    #ffffff;
        border-left:         1px solid #333333;
        border-right:        1px solid #333333;
        border-bottom:       1px solid #ffffff;
    }
    a.tabcaution:hover {
        background-image:    url(<?php echo ( (isset($pma_http_url) && !empty($pma_http_url)) ? $pma_http_url : '../' ); ?>phpmyadmin/themes/arctic_ocean/img/tbg_nav3.png);
        background-position: center bottom;
    }
    a.tabcaution:link, a.tabcaution:active, a.tabcaution:visited, a.tabcaution:hover, span.tabcaution  {
        color: #aa0000;
    }

    /* disabled tabs */
    span.tab {
        color:               #333333;
    }
    span.tab, span.tabcaution {
        filter:              progid:DXImageTransform.Microsoft.Alpha(opacity=55);
        -moz-opacity:        0.5;
        opacity:             0.5;
        cursor:              default;
    }


/* -- DISABLED ELEMENTS -- */
.disabled, .disabled a:link, disabled a:active, .disabled a:visited {
    color:               #333333;
}
tr.disabled, td.disabled, .disabled td {
    filter:              progid:DXImageTransform.Microsoft.Alpha(opacity=50);
    -moz-opacity:        0.60;
    opacity:             0.60;
}

#TooltipContainer {
    font-size:           inherit;
    color:               #ffffff;
    background-color:    #9eb1cc;
    position:            absolute;
    z-index:             99;
    width:               25em;
    height:              auto;
    overflow:            auto;
    visibility:          hidden;
    border:              1px solid #333333;
    padding:             0.5em;
    filter:              progid:DXImageTransform.Microsoft.Alpha(opacity=95);
    -moz-opacity:        0.95;
    opacity:             0.95;
}

#buttonNo {
    color:               #aa0000;
    font-weight:         bold;
}
#buttonYes {
    color:               #006600;
    font-weight:         bold;
}
#buttonGo {
    color:               #58580;
    font-weight:         bold;
}
#listTable {
    width:               260px;
}
#textSqlquery {
    width:               450px;
}
#textSQLDUMP {
   width:                95%;
   height:               95%;
   font-family:          "Courier New", Courier, mono;
   font-size:            11px;
}


