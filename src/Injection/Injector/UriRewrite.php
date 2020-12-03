<?php

namespace FS\Injection\Injector;

use FS\Injection\InjectorInterface;
use FS\Injection\I;
use FS\Injection\Route;

class UriRewrite implements InjectorInterface
{
    use CommonsTrait {
        withOptions as withOptionsCommon;
        resolve as resolveCommon;
    }

    const TYPE_REWRITING_URI = 4;
    const TYPE_REWRITING_REDIRECT = 5;

    protected $route;
    protected $redirectTo;
    protected $query;
    protected $template;
    protected $type;

    public function withOptions(array $options = [])
    {
        $this->withOptionsCommon($options);

        if (isset($options['redirectTo'])) {
            $this->withRedirectTo($options['redirectTo']);
        }

        if (isset($options['template'])) {
            $this->withTemplate($options['template']);
        }

        if (isset($options['query'])) {
            $this->withQuery($options['query']);
        }

        return $this;
    }

    public function withType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function withRoute(Route $route)
    {
        $this->route = $route;

        return $this;
    }

    public function withRedirectTo($url)
    {
        $this->redirectTo = $url;

        return $this;
    }

    public function withTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    public function withQuery(array $query)
    {
        $this->query = array_merge(isset($this->query) ? $this->query : [], $query);

        return $this;
    }

    public function resolve()
    {
        if (!$this->resolveCommon()) {
            return;
        }

        $cb = $this->callback;

        // define rewrite rules
        I::action('init', function () {
            add_rewrite_rule($this->route->getUriRegExp(), $this->route->getUriQueryMap());
            add_rewrite_tag('%'.$this->route->getUri().'%', '(.+)');

            // save rewrite rules
            flush_rewrite_rules();
        });

        // tell the injection instance the desired route is hitten
        I::action('parse_request', function (\WP $wp) {
            if (isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == $this->route->getUri()) {
                $this->route->hit();
            }
        });

        if ($this->type == self::TYPE_REWRITING_REDIRECT) {
            // perform redirect if and only if the injection instance's route is flagged hitten
            return I::action('template_redirect', function () use ($cb) {
                if (!$this->route->isHitten()) {
                    return;
                }

                $cb($this);

                wp_redirect(home_url($this->redirectTo.(isset($this->query) ? '?'.http_build_query($this->query) : '')));
                exit();
            });
        }

        if (isset($this->template)) {
            I::filter('template_include', function ($template) use ($cb) {
                if ($this->route->isHitten()) {
                    $template = $cb($this->template);
                }

                return $template;
            });
        }
    }
}
