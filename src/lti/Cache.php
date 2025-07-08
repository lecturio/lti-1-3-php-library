<?php
namespace IMSGlobal\LTI;

/**
 * Class Cache
 * Handles caching of LTI launch data and nonces using a file-based cache.
 */
class Cache
{
    /**
     * @var array<string, mixed> $cache In-memory cache storage
     */
    private $cache;

    /**
     * Get launch data for a given key.
     *
     * @param string $key
     * @return mixed|null
     */
    public function get_launch_data($key)
    {
        $this->load_cache();
        return $this->cache[$key] ?? null;
    }

    /**
     * Cache launch data for a given key.
     *
     * @param string $key
     * @param mixed $jwt_body
     * @return $this
     */
    public function cache_launch_data($key, $jwt_body)
    {
        $this->cache[$key] = $jwt_body;
        $this->save_cache();
        return $this;
    }

    /**
     * Cache a nonce value.
     *
     * @param string $nonce
     * @return $this
     */
    public function cache_nonce($nonce)
    {
        $this->cache['nonce'][$nonce] = true;
        $this->save_cache();
        return $this;
    }

    /**
     * Check if a nonce exists in the cache.
     *
     * @param string $nonce
     * @return bool
     */
    public function check_nonce($nonce)
    {
        $this->load_cache();
        if (!isset($this->cache['nonce'][$nonce])) {
            return false;
        }
        return true;
    }

    /**
     * Load cache from file into memory.
     *
     * @return void
     */
    private function load_cache()
    {
        $cache = file_get_contents(sys_get_temp_dir() . '/lti_cache.txt');
        if (empty($cache)) {
            file_put_contents(sys_get_temp_dir() . '/lti_cache.txt', '{}');
            $this->cache = [];
        }
        $this->cache = json_decode($cache, true);
    }

    /**
     * Save in-memory cache to file.
     *
     * @return void
     */
    private function save_cache()
    {
        file_put_contents(sys_get_temp_dir() . '/lti_cache.txt', json_encode($this->cache));
    }
}
?>