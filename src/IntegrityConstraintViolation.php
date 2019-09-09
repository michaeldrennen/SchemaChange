<?php

namespace MichaelDrennen\SchemaChange;

use Illuminate\Database\QueryException;

class IntegrityConstraintViolation {

    protected $exception;
    protected $subCode;

    const COLUMN_CAN_NOT_BE_NULL = 1048;

    public function __construct( QueryException $exception ) {
        $this->exception = $exception;
        $this->subCode   = $this->getSubCodeFromException( $exception );

        $this->processBasedOnSubCode( $this->subCode );

    }


    /**
     * @param QueryException $exception
     * @return int
     * @throws \Exception
     */
    protected function getSubCodeFromException( QueryException $exception ): int {
        $pattern = '/Integrity constraint violation: (\d*) /';
        $message = $exception->getMessage();
        $found   = preg_match( $pattern, $message, $matches );

        if ( 1 !== $found ):
            $exception = new \Exception( "Unable to find column in getFieldNameFromIntegrityConstraintException()" );
            throw $exception;
        endif;

        if ( FALSE === isset( $matches[ 1 ] ) ):
            $exception = new \Exception( "Unable to find match for the field name in getFieldNameFromIntegrityConstraintException()" );
            throw $exception;
        endif;

        return $matches[ 1 ];
    }


    protected function processBasedOnSubCode() {
        switch ( $this->subCode ):

            case self::COLUMN_CAN_NOT_BE_NULL:
                $this->processColumnCanNotBeNull( $this->exception );
                break;
        endswitch;
    }


    protected function processColumnCanNotBeNull( QueryException $exception ) {
        $column
    }


    protected function processIntegrityConstraintViolation( QueryException $exception ) {

    }


    // Dont need
    public static function getErrorCodeFromQueryException( QueryException $exception ): int {
        return $exception->getCode();
    }
}