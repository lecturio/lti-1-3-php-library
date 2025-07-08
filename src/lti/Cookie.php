<?php
namespace IMSGlobal\LTI;

/**
 * Class Cookie
 * Handles getting and setting cookies with SameSite and legacy fallback support.
 *
 * @package IMSGlobal\LTI
 */
class Cookie
{
    /**
     * Get a cookie value by name, with legacy fallback.
     *
     * @param string $name Cookie name
     * @return string|false Cookie value or false if not set
     */
    public function get_cookie($name)
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        // Look for backup cookie if same site is not supported by the user's browser.
        if (isset($_COOKIE["LEGACY_" . $name])) {
            return $_COOKIE["LEGACY_" . $name];
        }
        return false;
    }

    /**
     * Set a cookie with SameSite=None and Secure, plus a legacy fallback.
     *
     * @param string $name Cookie name
     * @param string $value Cookie value
     * @param int $exp Expiration time in seconds (default 3600)
     * @param array<string, mixed> $options Additional options for setcookie
     * @return $this
     */
    public function set_cookie($name, $value, $exp = 3600, $options = [])
    {
        $cookie_options = [
            'expires' => time() + $exp
        ];

        // SameSite none and secure will be required for tools to work inside iframes
        $same_site_options = [
            'samesite' => 'None',
            'secure' => true
        ];

        setcookie($name, $value, array_merge($cookie_options, $same_site_options, $options));

        // Set a second fallback cookie in the event that "SameSite" is not supported
        setcookie("LEGACY_" . $name, $value, array_merge($cookie_options, $options));
        return $this;
    }
}
?>