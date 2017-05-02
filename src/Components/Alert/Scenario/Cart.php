<?php

namespace FS\Components\Alert\Scenario;

class Cart extends Native
{
    /**
     * silent logging: store log in options.
     *
     * @var bool
     */
    protected $silent = false;

    /**
     * Silent Logging Mode enable affect customer side.
     * by enable silent logging, customer no longer see the warning.
     * The store owner has logging console to browse 10 latest error/warning messages.
     */
    public function enableSilentLogging()
    {
        $this->silent = true;

        return $this;
    }

    public function view($viewer)
    {
        if ($this->silent) {
            $this->getApplicationContext()->option()->log($this->store);

            return $this;
        }

        \wc_clear_notices();

        foreach ($this->store as $type => $notifications) {
            if (!$notifications) {
                continue;
            }

            $html = implode('<br/>', $notifications);
            \wc_add_notice($html, $type);
        }

        return $this;
    }
}
