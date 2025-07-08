<?php
namespace IMSGlobal\LTI;

/**
 * Handles HTTP and JS-based redirects for LTI flows.
 * @package IMSGlobal\LTI
 */
class Redirect
{
    /**
     * @var string
     */
    private $location;
    /**
     * @var string|null
     */
    private $referer_query;
    /**
     * @var string
     */
    private static $CAN_302_COOKIE = 'LTI1p3_302_Redirect';

    /**
     * @param string $location
     * @param string|null $referer_query
     */
    public function __construct($location, $referer_query = null)
    {
        $this->location = $location;
        $this->referer_query = $referer_query;
    }

    /**
     * Perform a 302 redirect.
     * @return void
     */
    public function do_redirect()
    {
        header('Location: ' . $this->location, true, 302);
        die;
    }

    /**
     * Redirect with cookie fallback for browsers.
     * @param Cookie|null $cookie
     * @return void
     */
    public function do_hybrid_redirect(Cookie $cookie = null)
    {
        if ($cookie == null) {
            $cookie = new Cookie();
        }
        if (!empty($cookie->get_cookie(self::$CAN_302_COOKIE))) {
            return $this->do_redirect();
        }
        $cookie->set_cookie(self::$CAN_302_COOKIE, "true");
        $this->do_js_redirect();
    }

    /**
     * Get the redirect URL.
     * @return string
     */
    public function get_redirect_url()
    {
        return $this->location;
    }

    /**
     * Perform a JS-based redirect with fallback UI.
     * @return void
     */
    public function do_js_redirect()
    {
        ?>
        <a id="try-again" target="_blank">If you are not automatically redirected, click here to continue</a>
        <script>
            document.getElementById('try-again').href = <?php
            if (empty($this->referer_query)) {
                echo 'window.location.href';
            } else {
                echo "window.location.origin + window.location.pathname + '?" . $this->referer_query . "'";
            }
            ?>;
            var canAccessCookies = function () {
                if (!navigator.cookieEnabled) {
                    return false;
                }
                try {
                    if (!document.cookie || document.cookie == "" || document.cookie.indexOf('<?php echo self::$CAN_302_COOKIE; ?>') === -1) {
                        return false;
                    }
                } catch (e) {
                    return false;
                }
                return true;
            };
            if (canAccessCookies()) {
                window.location = '<?php echo $this->location ?>';
            } else {
                var opened = window.open(document.getElementById('try-again').href, '_blank');
                if (opened) {
                    document.getElementById('try-again').innerText = "New window opened, click to reopen";
                } else {
                    document.getElementById('try-again').innerText = "Popup blocked, click to open in a new window";
                }
            }
        </script>
        <?php
    }

}