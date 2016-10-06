<?php

namespace FS\Context;

interface ApplicationEventInterface
{
	public function setInputs($inputs);

	public function getInputs();
}