<?php
namespace MichaelDrennen\SchemaChange;

use Illuminate\Database\QueryException;

class SchemaChange {

    protected $exception;
    protected $code;

    public function __construct(QueryException $exception) {
        $this->exception = $exception;
        $this->code = $exception->getCode();

        var_dump($exception);

        switch ( $this->code ):
            case 23000: // Integrity constraint violation
                $this->processIntegrityConstraintViolation( $exception );
                break;
            case 22003: // Numeric value out of range
                $this->processNumericValueOutOfRange( $exception );
                break;
            default:
                break;
        endswitch;
    }


    protected function processIntegrityConstraintViolation(QueryException $exception){

    }



    // Dont need
    public static function getErrorCodeFromQueryException(QueryException $exception): int {
        return $exception->getCode();
    }
}