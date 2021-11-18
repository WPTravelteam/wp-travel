<?php
/**
 *  PHP-PayPal-IPN Example
 *
 *  This shows a basic example of how to use the IPNListener() PHP class to
 *  implement a PayPal Instant Payment Notification (IPN) listener script.
 *
 *  This package is available at GitHub:
 *  https://github.com/WadeShuler/PHP-PayPal-IPN/
 *
 *  @package    WP_Travel
 *  @link       https://github.com/WadeShuler/PHP-PayPal-IPN
 *  @forked     https://github.com/Quixotix/PHP-PayPal-IPN
 *  @author     Wade Shuler
 *  @copyright  Copyright (c) 2015, Wade Shuler
 *  @license    http://choosealicense.com/licenses/gpl-2.0/
 *  @version    2.2.0
 */

// TODO: I hate 'ini_set', fix this later
ini_set( 'log_errors', true );
ini_set( 'error_log', dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'ipn_errors.log' );

// include the IPNListener Class
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'IPNListener.php';

$listener              = new IPNListener();      // NOTICE new upper-casing of the class name
$listener->use_sandbox = true;      // Only needed for testing (sandbox), else omit or set false
