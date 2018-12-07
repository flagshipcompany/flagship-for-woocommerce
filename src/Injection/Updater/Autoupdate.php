<?php

namespace FS\Injection\Updater;

use FS\Injection\I;
use FS\Injection\Http\Client;

class Autoupdate
{
    private $slug; // plugin slug
    private $pluginData; // plugin data
    private $username; // GitHub username
    private $repo; // GitHub repo name
    private $pluginFile; // __FILE__ of our plugin
    private $accessToken; // GitHub private repo token
    protected $release = [];
    protected $github;

    public function __construct($pluginFile, $username, $repo, $accessToken = null)
    {
        add_filter('pre_set_site_transient_update_plugins', [$this, 'presetTransient']);
        add_filter('plugins_api', [$this, 'setPluginInfo'], 10, 3);
        add_filter('upgrader_post_install', [$this, 'postInstall'], 10, 3);

        $this->pluginFile = $pluginFile;
        $this->username = $username;
        $this->repo = $repo;

        $this->accessToken = $accessToken;
        $this->github = new Client();
    }

    // Get information regarding our plugin from WordPress
    private function initPluginData()
    {
        $this->slug = \plugin_basename($this->pluginFile);
        $this->pluginData = \get_plugin_data($this->pluginFile);
    }

    // Push in plugin version information to get the update notification
    public function presetTransient($transient)
    {
        // If we have checked the plugin data before, don't re-check
        if (empty($transient->checked)) {
            return $transient;
        }

        // Get plugin & GitHub release information
        $this->initPluginData();
        $this->resolveRelease();

        if (!$this->release) {
            return $transient;
        }

        // Check the versions if we need to do an update
        $doUpdate = version_compare($this->release['tag_name'], $transient->checked[$this->slug], '>');

        // Update the transient to include our updated plugin data
        if ($doUpdate) {
            $obj = new \stdClass();

            $obj->slug = $this->slug;
            $obj->new_version = $this->release['tag_name'];
            $obj->url = $this->pluginData['PluginURI'];
            $obj->package = $this->release['zipball_url'];

            $transient->response[$this->slug] = $obj;
        }

        return $transient;
    }

    // Push in plugin version information to display in the details lightbox
    public function setPluginInfo($false, $action, $response)
    {
        // Get plugin & GitHub release information
        $this->initPluginData();
        $this->resolveRelease();

        // If nothing is found, do nothing
        if (empty($response->slug) || $response->slug != $this->slug) {
            return false;
        }

        // Add our plugin information
        $response->last_updated = $this->release['published_at'];
        $response->slug = $this->slug;
        $response->plugin_name = $this->pluginData['Name'];
        $response->version = $this->release['tag_name'];
        $response->author = $this->pluginData['AuthorName'];
        $response->homepage = $this->pluginData['PluginURI'];
        $response->download_link = $this->release['zipball_url'];

        // Create tabs in the lightbox
        $response->sections = [
            'description' => $this->pluginData['Description'],
            'changelog' => Parsedown::instance()->parse($this->release['body']),
        ];

        // Gets the required version of WP if available
        $matches = null;
        preg_match("/requires:\s([\d\.]+)/i", $this->release['body'], $matches);
        if (!empty($matches)) {
            if (is_array($matches)) {
                if (count($matches) > 1) {
                    $response->requires = $matches[1];
                }
            }
        }

        // Gets the tested version of WP if available
        $matches = null;
        preg_match("/tested:\s([\d\.]+)/i", $this->release['body'], $matches);
        if (!empty($matches)) {
            if (is_array($matches)) {
                if (count($matches) > 1) {
                    $response->tested = $matches[1];
                }
            }
        }

        return $response;
    }

    // Perform additional actions to successfully install our plugin
    public function postInstall($true, $hook_extra, $result)
    {
        // Get plugin information
        $this->initPluginData();

        // Remember if our plugin was previously activated
        $wasActivated = \is_plugin_active($this->slug);

        // Since we are hosted in GitHub, our plugin folder would have a dirname of
        // reponame-tagname change it to our original one:
        global $wp_filesystem;

        $pluginFolder = WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.dirname($this->slug);

        $wp_filesystem->move($result['destination'], $pluginFolder);
        $result['destination'] = $pluginFolder;

        // Re-activate plugin if needed
        if ($wasActivated) {
            $activate = \activate_plugin($this->slug);
        }

        return $result;
    }

    /**
     * Get all releases from github.
     *
     * @param string $domain
     * @param string $repo
     *
     * @return array
     */
    protected function resolveRelease()
    {
        // Only do this once
        if (!empty($this->release)) {
            return;
        }

        $response = $this->github->get("https://api.github.com/repos/{$this->username}/{$this->repo}/releases");

        if (!$response->isSuccessful()) {
            return;
        }

        $releases = $response->getBody();

        $this->release = $this->getLatestRelease($releases ?: []);

        return $this;
    }

    /**
     * filter out relevant release for wc v2.6 & v3.0.
     *
     * @param array $releases
     *
     * @return array
     */
    protected function getLatestRelease($releases)
    {
        $v3 = (bool) version_compare(wc()->version, '3.0', '>=');
        
        $v3Releases = array_filter($releases, function ($release) {
            $tagName = preg_replace("/[^0-9.]/", "", $release['tag_name']);

            return version_compare($tagName, '2.0', '>=');
        });

        $v2Releases = array_filter($releases, function ($release) {
            $tagName = preg_replace("/[^0-9.]/", "", $release['tag_name']);

            return version_compare($tagName, '2.0', '<');
        });

        if ($v3 && count($v3Releases) > 0 ) {
            return $this->getLatestReleaseByWcVersion($v3Releases);
        }

        if ($v2 && count($v2Releases) > 0 ) {
            return $this->getLatestReleaseByWcVersion($v2Releases);
        }
    }

    protected function getLatestReleaseByWcVersion($releases)
    {
        $latestRelease = reset($releases);

        array_walk($releases, function ($release) use (&$latestRelease) {
            $tagName = preg_replace("/[^0-9.]/", "", $release['tag_name']);
            $latestTagName = preg_replace("/[^0-9.]/", "", $latestRelease['tag_name']);

            if (version_compare($tagName, $latestTagName, '>')) {
                $latestRelease = $release;
            }
        });

        return $latestRelease;
    }
}
