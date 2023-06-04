=== Plugin Security Scanner ===
Contributors: glen_scott
Tags: plugins,security,scanner,vulnerabilities,secure
Tested up to: 5.2.2
Stable tag: 2.0.2
License: GPLv2 or later

This plugin alerts you if any of your plugins have security vulnerabilities.  It does this by utilising the WPScan Vulnerability Database once a day.

== Description ==

This plugin determines whether any of your plugins or themes have security vulnerabilities.  It does this by looking up details in the WPScan Vulnerability Database.

It will run a scan once a day, and e-mail the administrator if any vulnerable plugins or themes are found.

*Please note:* As from version 2.0.0, you will need to <a href="https://wpvulndb.com/users/sign_up">register on the WPScan Vulnerability Database</a> site in order to get an API token.  This token is required before any security scans can be performed.  Once you have your token, it can be added to the Plugin Security Scanner settings page.

You can also register a webhook for notifications. The webhook will trigger daily, even if no vulnerabilities found. The webhook is a post request, with JSON payload containing the vulnerabilities.

You can enable the webhook under Settings\General tab - see the Plugin Security Scanner settings.

It also adds a new menu option to the admin tools menu called "Plugin Security Scanner".  Clicking this runs a scan.  If the scan finds any problems, it shows you a list of plugins or themes that have vulnerabilities, along with a description of the issue.

The WPScan Vulnerability Database API, which this plugin uses, is free for non-commercial use. However, any commercial usage will require that you purchase a commercial license from WPScan. If you are using the API for your own site then you will not need a commercial license. However, if you are a hosting company and install the plugin systematically across all of your clients sites, then you will need to purchase a commercial license. If you are making heavy use of the API, it is likely that you will need to purchase a commercial license. To enquire about a commercial license, please contact team@wpvulndb.com

Icons made by <a href="http://www.flaticon.com/authors/alessio-atzeni" title="Alessio Atzeni">Alessio Atzeni</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0">CC BY 3.0</a>

== Screenshots ==

1. Example run of the security scanner that has found two vulnerable plugins.
2. E-mail alert to administrator when vulnerable plugins have been found.

== Changelog ==

= 2.0.2 =
* Clarified 403 error

= 2.0.1 =
* Clarified error message in daily email

= 2.0.0 =
* Use WPScan Vulnerability Database API V3
* Important notice:  to use this plugin, you now need to register a user and get an API token from https://wpvulndb.com/users/sign_up
* Improved error handling

= 1.6.0 =
* Moved settings to dedicated page
* Added option to ignore unpatched issues

= 1.5.2 =
* Fix: Allow scanning if you are running WordPress nightly or release candidates

= 1.5.1 =
* Added option to ignore 'WordPress 2.3-4.8.3 - Host Header Injection in Password Reset' vulnerability

= 1.5.0 =
* Checks vulnerabilities in WordPress core files
* Added ability to send an HTTP request when vulnerabilities are found (webhook)

= 1.4.1 =
* Fix issue with theme version checking

= 1.4 =
* Themes as well as plugins are now scanned for vulnerabilities

= 1.3.1 =
* Added check to make sure the WPVulnDb API has returned a valid response

= 1.3 =
* Added option under "Settings / General / Plugin Security Scanner" to disable the email notification

= 1.2.1 =
* Moved to WPScan Vulnerability Database API v2

= 1.2.0 =
* Added i18n support

= 1.1.9 =
* Fix: Removed unecessary ob_flush calls
* Fix: If vulnerability does not have a "fixed in" version number, report it as a vulnerability

= 1.1.8 =
* Fix: corrected links to WPScan Vulnerability Database

= 1.1.7 =
* Add link to WPScan Vulnerability Database details page

= 1.1.6 =

* Conditionally include plugin.php include in case it is not already included

= 1.1.5 =
* Escape output in HTML report to prevent XSS

= 1.1.4 =
* Added blog title to email subject

= 1.1.3 =
* Fixed bug that prevented admin email being sent

= 1.1 =
* Email admin daily if any vulnerabilities are found

= 1.0 =
* Initial release

