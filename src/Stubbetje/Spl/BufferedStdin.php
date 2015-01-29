<?php

namespace Stubbetje\Spl;

use SplFileObject;
use SplTempFileObject;

class BufferedStdin extends \SplFileObject
{
	/**
	 * @var SplTempFileObject
	 */
	private $_temp;

	public function __construct( $filename = 'php://stdin' )
	{
		$this->_stdin = new SplFileObject( $filename, 'r' );
		$this->_temp  = new SplTempFileObject();

		$this->_temp->setFlags( SplFileObject::DROP_NEW_LINE );

		foreach( $this->_stdin as $line ) {
			$this->_temp->fwrite( $line );
		}

		$this->_temp->rewind();

		parent::__construct( 'php://memory' ); // fake it, till you make it!
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
		return $this->_temp->seek( $linePos );
	}

	/**
	 * Retrieved the current line of the file.
	 *
	 * @return string
	 */
	public function current()
	{
		return $this->_temp->current();
	}

	/**
	 * Moves ahead to the next line in the file.
	 *
	 * @return void
	 */
	public function next()
	{
		return $this->_temp->next();
	}

	/**
	 * Gets the current line number
	 *
	 * @return integer
	 */
	public function key()
	{
		return $this->_temp->key();
	}

	/**
	 * Check whether EOF has been reached
	 *
	 * @return bool
	 */
	public function valid()
	{
		return $this->_temp->valid();
	}

	/**
	 * Rewinds the file back to the first line.
	 *
	 * @return void
	 */
	public function rewind()
	{
		return $this->_temp->rewind();
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
		return $this->_temp->eof();
	}
}
