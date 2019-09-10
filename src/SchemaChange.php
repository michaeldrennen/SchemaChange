<?php

namespace MichaelDrennen\SchemaChange;

use Illuminate\Database\QueryException;
use MichaelDrennen\SchemaChange\Responses\DataTooLongForColumn;
use MichaelDrennen\SchemaChange\Responses\IntegrityConstraintViolation;
use MichaelDrennen\SchemaChange\Responses\NumericValueOutOfRange;

class SchemaChange {

    protected $exception;
    protected $code;


    public static function instantiate( QueryException $exception ) {


        switch ( $exception->getCode() ):
            case 22001:
                return new DataTooLongForColumn( $exception );
                break;

            case 23000: // Integrity constraint violation
                return new IntegrityConstraintViolation( $exception );
                break;

            case 22003: // Numeric value out of range
                return new NumericValueOutOfRange( $exception );
                break;
            default:
                throw new \Exception( "The developer needs to add a new exception code to SchemaChange::instantiate for " . $exception->getCode() . " with message " . $exception->getMessage() );
                break;
        endswitch;
    }


}