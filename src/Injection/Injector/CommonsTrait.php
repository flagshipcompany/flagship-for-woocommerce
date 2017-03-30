<?php

namespace FS\Injection\Injector;

trait CommonsTrait
{
    protected $shouldResolve = true;
    protected $callback;

    public function withOptions(array $options = [])
    {
        if (isset($options['dependencies'])) {
            $this->withDependencies($options['dependencies']);
        }

        if (isset($options['guard'])) {
            $this->withGuard($options['guard']);
        }

        return $this;
    }

    public function withCallback(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    public function withDependencies($dependencies)
    {
        return $this->withGuard($this->checkDependencies($dependencies));
    }

    public function withGuard($shouldResolve = true)
    {
        $this->shouldResolve = $shouldResolve && $this->shouldResolve;

        return $this;
    }

    public function resolve()
    {
        if (!$this->shouldResolve) {
            throw new \Exception('Flagship Secure: missing dependencies or does not match the minimum condition to run the plugin');
        }
    }

    /**
     * check if all dependant plugins were activated.
     *
     * @param mixed $dependencies plugin_dir/plugin_name
     *
     * @return bool
     */
    public function checkDependencies($dependencies)
    {
        if (!is_array($dependencies)) {
            $dependencies = [$dependencies];
        }

        $network_active = false;

        if (!is_multisite()) {
            $plugins = \get_option('active_plugins');

            return array_reduce($dependencies, function ($activated, $dependency) use ($plugins) {
                return $activated && in_array($dependency, $plugins);
            }, true);
        }

        $plugins = \get_site_option('active_sitewide_plugins');

        return array_reduce($dependencies, function ($activated, $dependency) use ($plugins) {
            return $activated && iswith($plugins[$dependency]);
        }, true);
    }
}
