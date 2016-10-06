<?php

namespace FS\Context\Event;

abstract class AbstractApplicationEvent implements \FS\Context\ApplicationEventInterface
{
	protected $inputs = null;

	public function setInputs($inputs)
	{
		$this->inputs = $inputs;

		return $this;
	}

	public function getInputs()
	{
		return $this->inputs;
	}
}