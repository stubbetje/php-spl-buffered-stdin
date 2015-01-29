<?php

namespace Stubbetje\Test\Spl;

class BufferedStdinTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage Cannot rewind file php://stdin
	 */
	public function testPHPCannotRewindStdin()
	{
		$file = new \SplFileObject( 'php://stdin', 'r' );

		$file->rewind();
	}
}
