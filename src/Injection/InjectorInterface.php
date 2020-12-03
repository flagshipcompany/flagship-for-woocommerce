<?php

namespace FS\Injection;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

interface InjectorInterface
{
	public function withOptions(array $options = []);

	public function resolve();
}