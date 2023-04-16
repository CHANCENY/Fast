<?php
@session_start();

/**
 * This file contain some of php.ini overrides if you want to make php.in changes do it here
 */

/**
 * This is for errors to be displaying on page if enabled NOTE: alwalys comment this code when site is going
 * live
 */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

/**
 * Enabling this setting will help your website in performance and also will make sure header dont get set without
 * full completion of execution
 * Function like GlobalFunctions::redirect will work properly if this is enabled
 */
ob_start();

/**
 * This is for display server configuration when enabled please comment it out when done with it
 *
 */
//print_r(phpinfo());

/**
 * This will secure you routing
 */
global $THIS_SITE_ACCESS_LOCK;
$THIS_SITE_ACCESS_LOCK = TRUE;

global $ROOT;
$_SERVER['DOCUMENT_ROOT'] = str_replace('\\','/',__DIR__);
$ROOT = $_SERVER['DOCUMENT_ROOT'];


/**
 * This is for changing the execution time
 */
ini_set('max_execution_time', '90');

/**
 * This for post request data it can carry
 */
ini_set('post_max_size','200');

/**
 * This is for max filesize can be uploaded
 */
ini_set('upload_max_filesize', '200');

/**
 * home host
 */
global $HOME;
$HOME = '/developement';

/**
 * add sitemap file name to SITEMAP global variable below for example sitemap.xml
 */
global $SITEMAP;
$SITEMAP = "";

/**
 * Robot file will be added here and its database configuration will be made only when database connection made
 * Note: that all views set to administrator mode can not be added. you can pass @true below function to
 * skip also private views
 */
\Robot\Robot::robotFileCreation();

/**
 * Total size of file can be uploaded per upload
 */
global $MAXFILESIZE;
$MAXFILESIZE = 2000000;

/**
 * Url of home page by default index in used
 */
global $HOMEPAGE;
$HOMEPAGE = 'index';




