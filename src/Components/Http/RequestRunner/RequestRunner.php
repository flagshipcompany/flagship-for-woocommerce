<?php

namespace FS\Components\Http\RequestRunner;

class RequestRunner extends \FS\Components\AbstractComponent implements RequestRunnerInterface, RequestRunnerDriverAwareInterface
{
    protected $driver;

    public function run($configs)
    {
        try {
            return $this->getRequestRunnerDriver()->run($configs);
        } catch (\Exception $e) {
            return new \FS\Components\Http\Response(array(
                'errors' => array(array($e->getMessage())),
                'content' => array(),
            ), $e->getCode());
        }
    }

    public function setRequestRunnerDriver(RequestRunnerInterface $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    public function getRequestRunnerDriver()
    {
        return $this->driver;
    }
}
