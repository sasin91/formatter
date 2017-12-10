<?php

namespace CollabCorp\Formatter\Tests;

use CollabCorp\Formatter\FormatterServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
	protected function getPackageProviders($app)
	{
		return [FormatterServiceProvider::class];
	}

	protected function getPackageAliases($app)
	{
		return [
			//
		];
	}

	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{
		//
	}
}
