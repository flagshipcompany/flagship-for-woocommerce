<?php

namespace FS\Injection\Pico;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Engine
{
    protected $delimiter;
    protected $pattern;

    public function __construct($delimiter = ['\{{2}', '\}{2}'])
    {
        $this->setDelimiter($delimiter);
    }

    public function setDelimiter(array $delimiter)
    {
        $this->delimiter = $delimiter;
        $this->pattern = "/{$delimiter[0]}\s*('?[\w\.]*'?)([\s?\|\w]*)\s?{$delimiter[1]}/";

        return $this;
    }

    public function render($content, array $payload = [])
    {
        $engine = $this;

        return preg_replace_callback($this->pattern, function ($matches) use ($payload, $engine) {
            preg_match("/^'.*'$/", $matches[1], $alphanum);

            if ($alphanum) {
                $filters = explode('|', trim($matches[2]));

                return $engine->applyFilters(trim($alphanum[0], '\''), $filters);
            }

            $keys = explode('.', trim($matches[1]));
            $filters = explode('|', trim($matches[2]));

            $value = $payload;

            while ($keys) {
                $key = array_shift($keys);
                if (!isset($value[$key])) {
                    return '';
                }
                $value = $value[$key];
            }

            return $engine->applyFilters($value, $filters);
        }, $content);
    }

    public function renderFile($filePath, array $payload = [])
    {
        return $this->render($this->getFileContent($filePath));
    }

    protected function getFileContent($filePath)
    {
        return file_get_contents($filePath);
    }

    protected function applyFilters($value, array $filters)
    {
        $filters = array_filter($filters);
        if (!$filters) {
            return $value;
        }
        foreach ($filters as $filter) {
            $filter = trim($filter);
            $value = $filter($value);
        }

        return $value;
    }
}
