<?php

namespace Stubbetje\Spl;

use SplFileObject;
use SplTempFileObject;

class BufferedStdin implements \SeekableIterator, \RecursiveIterator
{
	/**
	 * @var SplFileObject
	 */
	private $_stdin;

	/**
	 * @var SplTempFileObject
	 */
	private $_temp;

	/**
	 * @var boolean
	 */
	private $_endOfStream = false;

	/**
	 * @var integer
	 */
	private $_currentLineNumber = 0;

	/**
	 * @var integer
	 */
	private $_numberOfLinesBuffered = 0;

	private static function debug()
	{
		$stack = debug_backtrace();
		$line  = $stack[ 0 ][ 'line' ];
		$stack = $stack[ 1 ];
		$fo    = $stack[ 'object' ];


		$n = function() { return "\033[48;5;16m\033[38;5;220m"; };
		$v = function( $t ) use ( $n ) { return "\033[38;5;160m{$t}" . $n(); };

		printf(
			implode( null, array(
				$n(),
				'DEBUG ∷ linenr=[',
				$v( '%3d' ),
				'] lines buffered=[',
				$v( '%3d' ),
				'] eof=[',
				$v( '%s' ),
				']    ',
				$v( '%20s'),
				"() line ",
				$v( '%3d' ),
				' ├─ ',
				"\033[K\033[0m\n",
			) ),

			//"\033[48;5;16m\033[38;5;220mDEBUG ∷ current=[%3d] bufferd=[%3d] eof=[%s]\t\t%s():%d]\033[K\033[0m\n",
			$fo->_currentLineNumber,
			$fo->_numberOfLinesBuffered,
			var_export( $fo->_endOfStream, true ),
			$stack[ 'function' ],
			$line
		);
		ob_flush();
	}

	public function __construct( $filename = 'php://stdin' )
	{
		static::debug();
		$this->_stdin = new SplFileObject( $filename, 'r' );
		$this->_temp  = new SplTempFileObject();

		$this->_stdin->setFlags( SplFileObject::DROP_NEW_LINE );
		$this->_temp->setFlags( SplFileObject::DROP_NEW_LINE );
	}

	/**
	 * Seek to specified line in the file
	 *
	 * @param integer $linePos  Zero based line number
	 *
	 * @return void
	 */
	public function seek( $linePos )
	{
		throw new \Exception( 'NEEDS IMPLEMENTATION' );
	}

	/**
	 * Retrieved the current line of the file.
	 *
	 * @return string
	 */
	public function current()
	{
		static::debug();

		if( $this->_currentLineNumber < $this->_numberOfLinesBuffered ) {
			return $this->_temp->current();
		}
		return $this->_stdin->current();
	}

	/**
	 * Moves ahead to the next line in the file.
	 *
	 * @return void
	 */
	public function next()
	{
		static::debug();

		if( $this->_currentLineNumber < $this->_numberOfLinesBuffered ) {
			$this->_currentLineNumber++;
			$this->_temp->seek( $this->_currentLineNumber );
			$this->_numberOfLinesBuffered++;
		} else {
			$this->_currentLineNumber++;
			$this->_stdin->next();

			$line = $this->_stdin->current();
			$this->_temp->seek( $this->_currentLineNumber );
			$this->_temp->fwrite( $line );
		}

		static::debug();
	}

	/**
	 * Gets the current line number
	 *
	 * @return integer
	 */
	public function key()
	{
		return $this->_currentLineNumber;
		//return $this->_stdin->key();
	}

	/**
	 * Check whether EOF has been reached
	 *
	 * @return bool
	 */
	public function valid()
	{
		return ! $this->eof();
	}

	/**
	 * Rewinds the file back to the first line.
	 *
	 * @return void
	 */
	public function rewind()
	{
		$this->_temp->rewind();
		$this->_currentLineNumber = 0;
	}

	/**
	 * An SplFileObject|BufferedStdin does not have children so this method always returns false.
	 *
	 * @return false
	 */
	public function hasChildren()
	{
		return false;
	}

	/**
	 * An SplFileObject|BufferedStdin does not have children so this method always returns null.
	 *
	 * @return null
	 */
	public function getChildren()
	{
		return null;
	}

	# -- These methods inherit from SplFileObject, but currently this class does not extends SplFileObject --

	/**
	 * Determine whether the end of a file has been reached.
	 *
	 * @return boolean
	 */
	public function eof()
	{
		$this->_endOfStream = $this->_stdin->eof();

		return $this->_endOfStream;
	}
}
