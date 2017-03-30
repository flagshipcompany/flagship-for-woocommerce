<?php

namespace FS\Injection;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use FS\Injection\Injector\Group;
use FS\Injection\Injector\Hook;
use FS\Injection\Injector\UriRewrite;
use FS\Injection\Injector\Asset;
use FS\Injection\Pico\Engine;
use FS\Security\TokenAccess;

class I
{
    protected static $data = [
        'debug' => false,
    ];

    protected static $instance;

    public static function action(string $hook, callable $cb = null, array $options = [])
    {
        $i = new Hook();
        $i
            ->withHook($hook)
            ->withType(Hook::TYPE_ACTION)
            ->withCallback($cb)
            ->withOptions($options);

        return $i->resolve();
    }

    public static function filter($hook, callable $cb = null, array $options = [])
    {
        if (!is_array($hook)) {
            $hook = [$hook];
        }

        foreach ($hook as $h) {
            $i = new Hook();
            $i
                ->withHook($h)
                ->withType(Hook::TYPE_FILTER)
                ->withCallback($cb)
                ->withOptions($options);

            $i->resolve();
        }
    }

    public static function group(callable $cb, array $options)
    {
        $group = new Group();

        $group
            ->withCallback($cb)
            ->withOptions($options);

        return $group->resolve();
    }

    public static function route(string $uri, callable $cb, array $options = [])
    {
        $route = new Route();

        $route
            ->withUri($uri)
            ->withCallback($cb);

        $i = new UriRewrite();
        $i
            ->withRoute($route)
            ->withCallback($cb)
            ->withType(UriRewrite::TYPE_REWRITING_URI)
            ->withOptions($options);

        return $i->resolve();
    }

    public function redirect(string $uri, string $redirectTo, callable $cb, array $options = [])
    {
        $options['redirectTo'] = $redirectTo;

        $route = new Route();

        $route
            ->withUri($uri)
            ->withCallback($cb);

        $i = new UriRewrite();
        $i
            ->withRoute($route)
            ->withCallback($cb)
            ->withType(UriRewrite::TYPE_REWRITING_REDIRECT)
            ->withOptions($options);

        return $i->resolve();
    }

    public static function translation(string $path)
    {
        self::action('init', function () use ($path) {
            load_plugin_textdomain(self::get('TEXT_DOMAIN'), false, $path);
        });
    }

    public static function script(string $path, array $options = [])
    {
        $i = new Asset();
        $i
            ->withType(Asset::TYPE_SCRIPT)
            ->withPath(self::get('PUBLIC_URL_DIR').ltrim(self::get('ASSETS_DIR'), '/').$path)
            ->withOptions($options);

        return $i->resolve();
    }

    public static function stylesheet(string $path, array $options = [])
    {
        $i = new Asset();
        $i
            ->withType(Asset::TYPE_STYLESHEET)
            ->withPath(self::get('PUBLIC_URL_DIR').ltrim(self::get('ASSETS_DIR'), '/').$path)
            ->withOptions($options);

        return $i->resolve();
    }

    public static function unaction(string $hook, string $name = null)
    {
        self::action('init', function () use ($hook, $name) {
            if (!$name) {
                return remove_all_actions($hook);
            }

            remove_action($hook, $name);
        });
    }

    public static function render(string $view, array $payload = [])
    {
        $engine = new Engine();

        return $engine->render($view, $payload);
    }

    public static function view(string $path, array $payload = [])
    {
        return self::render(file_get_contents(self::get('PLUGIN_DIR').ltrim(self::get('VIEWS_DIR'), '/').$path), $payload);
    }

    public static function set($key, $value)
    {
        self::$data[$key] = $value;
    }

    public static function get($key)
    {
        if (isset(self::$data[$key])) {
            return self::$data[$key];
        }
    }

    public static function option($keys)
    {
        $keys = explode('.', $keys);

        if (count($keys) == 0) {
            return;
        }

        $option = get_option(array_shift($keys));

        while ($keys) {
            $key = array_shift($keys);

            if (!isset($option[$key])) {
                return;
            }

            $option = $option[$key];
        }

        return $option;
    }

    public static function token($source = 'COOKIE')
    {
        return new TokenAccess(
            self::option('flagship_secure_options.developer_mode') ?
                self::get('SAMPLE_TOKEN') :
                ($source == 'COOKIE' ? $_COOKIE['flagship_web_sso_token'] : $_POST['web_sso_token'])
        );
    }

    public static function boot(string $baseDir)
    {
        $config = require_once $baseDir.'/injection.php';

        if (isset($config['autoload'])) {
            spl_autoload_register(function ($class) use ($config, $baseDir) {
                $hit = false;

                foreach ($config['autoload']['psr-4'] as $prefix => $dir) {
                    $len = strlen($prefix);

                    if (strncmp($prefix, $class, $len) !== 0) {
                        // no, move to the next registered autoloader
                        continue;
                    }

                    $hit = true;

                    // get the relative class name
                    $relativeClass = substr($class, $len);

                    // replace the namespace prefix with the base directory, replace namespace
                    // separators with directory separators in the relative class name, append
                    // with .php
                    $file = $baseDir.'/'.$dir.str_replace('\\', '/', $relativeClass).'.php';

                    // if the file exists, require it
                    if (file_exists($file)) {
                        require $file;

                        return;
                    }
                }
            });
        }

        if (isset($config['debug']) && $config['debug']) {
            self::$data['debug'] = true;
        }

        if (isset($config['extra']) && $config['extra']) {
            foreach ($config['extra'] as $key => $value) {
                self::set($key, $value);
            }
        }
    }

    public static function isDebugMode()
    {
        return self::$data['debug'];
    }

    public static function __($var)
    {
        $home = exec('echo ~');
        $text = $var;
        if (!is_string($var) && !is_array($var)) {
            ob_start();
            var_dump($var);
            $text = strip_tags(ob_get_clean());
        }
        if (is_array($var)) {
            $text = json_encode($var, JSON_PRETTY_PRINT);
        }
        file_put_contents($home.'/Desktop/data', date('Y-m-d H:i:s')."\t".print_r($text, 1)."\n", FILE_APPEND | LOCK_EX);
    }

    protected static function getInstance()
    {
        if (self::$instance) {
            return self::$instance;
        }

        self::$instance = new self();

        return self::$instance;
    }
}
