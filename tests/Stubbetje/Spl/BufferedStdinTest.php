<?php

namespace Stubbetje\Test\Spl;

use Stubbetje\Spl\BufferedStdin;

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

	public function getFakeStdin()
	{
		$bufferedStdin = new BufferedStdin( __DIR__ . '/_fixtures/test.txt' );

		return $bufferedStdin;
	}

	public function testNewInstanceCanUseFirstLine()
	{
		$stdin = $this->getFakeStdin();

		$this->assertEquals( '11111111', $stdin->current() );
	}

	public function testForEach()
	{
		$stdin = $this->getFakeStdin();

		$expectedLines = file( __DIR__ . '/_fixtures/test.txt', FILE_IGNORE_NEW_LINES );

		foreach( $stdin as $actual ) {
			$expected = array_shift( $expectedLines );

			$this->assertEquals( $expected, $actual );
		}

		$this->assertEmpty( $expectedLines );
	}
}
