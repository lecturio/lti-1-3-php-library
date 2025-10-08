<?php
// Import all
foreach (glob(__DIR__ . "/*.php") as $filename) {
    require_once $filename;
}
// Replace with proxy
use IMSGlobal\LTI\JWT_Proxy;
define("TOOL_HOST", ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: $_SERVER['REQUEST_SCHEME']) . '://' . $_SERVER['HTTP_HOST']);
JWT_Proxy::setLeeway(5);
?>