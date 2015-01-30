<?php

namespace Stubbetje\Spl;

use SplFileObject;
use SplTempFileObject;

class BufferedStdin extends \SplTempFileObject
{
	public function __construct( $filename = 'php://stdin' )
	{
		parent::__construct();
		
		$this->setFlags( SplFileObject::DROP_NEW_LINE );
		
		$stdin = new SplFileObject( $filename, 'r' );

		while( ! $stdin->eof() ) {
			$line = $stdin->current();
			$this->fwrite( $line );
			$stdin->next();
		}

		$this->rewind();
	}
}
