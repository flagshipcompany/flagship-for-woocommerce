<?php

namespace FS\Injection\Injector;

use FS\Injection\InjectorInterface;

class Asset implements InjectorInterface
{
    use CommonsTrait {
        resolve as resolveCommon;
        withOptions as withOptionsCommon;
    }

    const TYPE_SCRIPT = 7;
    const TYPE_STYLESHEET = 8;

    protected $requires = [];
    protected $version = null;
    protected $path;
    protected $type = 7;

    public function withOptions(array $options = [])
    {
        $this->withOptionsCommon($options);

        if (isset($options['requires'])) {
            $this->withRequires($options['requires']);
        }

        if (isset($options['version'])) {
            $this->withVersion($options['version']);
        }

        return $this;
    }

    public function withRequires(array $requires = [])
    {
        $this->requires = array_merge($this->requires, $requires);

        return $this;
    }

    public function withVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    public function withType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function withPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    public function resolve()
    {
        $this->resolveCommon();

        if ($this->type == self::TYPE_SCRIPT) {
            return wp_enqueue_script(md5($this->path), $this->path, $this->requires, $this->version);
        }

        wp_enqueue_style(md5($this->path), $this->path, $this->requires, $this->version);
    }
}
