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

	public function getFakeStdin( $numberOfLinesToSkip = 0 )
	{
		$bufferedStdin = new BufferedStdin( __DIR__ . '/_fixtures/test.txt' );

		for( $i = 0; $i < $numberOfLinesToSkip; $i++ ) {
			$bufferedStdin->next();
		}

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

	public function testNext()
	{
		$stdin = $this->getFakeStdin();
		//$stdin = new \SplFileObject( __DIR__ . '/_fixtures/test.txt' );
		//$stdin->setFlags( \SplFileObject::DROP_NEW_LINE );

		$this->assertEquals( '11111111', $stdin->current() );
		$this->assertEquals( 0, $stdin->key() );

		$stdin->next();

		$this->assertEquals( '22222222', $stdin->current() );
		$this->assertEquals( 1, $stdin->key() );

		$stdin->next();

		$this->assertEquals( '33333333', $stdin->current() );
		$this->assertEquals( 2, $stdin->key() );

		$stdin->next();

		$this->assertEquals( '44444444', $stdin->current() );
		$this->assertEquals( 3, $stdin->key() );

		$stdin->next();

		$this->assertEquals( '55555555', $stdin->current() );
		$this->assertEquals( 4, $stdin->key() );

		return $stdin;
	}

	public function testRewind()
	{
		$stdin = $this->getFakeStdin( 4 );

		$stdin->rewind();

		$this->assertEquals( 0, $stdin->key() );
		$this->assertEquals( '11111111', $stdin->current() );

		$stdin->next();
		$stdin->next();
		$stdin->next();
		$stdin->next();
	}

	public function testSeekToBegin()
	{
		$stdin = $this->getFakeStdin( 4 );

		$stdin->seek( 0 );

		$this->assertEquals( 0, $stdin->key() );
		$this->assertEquals( '11111111', $stdin->current() );
	}

	public function testSeekToLine7()
	{
		$stdin = $this->getFakeStdin( 4 );

		$stdin->seek( 6 );

		$this->assertEquals( 6, $stdin->key() );
		$this->assertEquals( '77777777', $stdin->current() );
	}
}
