<?php

namespace MichaelDrennen\SchemaChange\Responses;

use Illuminate\Database\QueryException;

class NumericValueOutOfRange extends AbstractSchemaChange {

    // Sub Codes
    const OUT_OF_RANGE_VALUE = 1264;

    /**
     * @return string
     */
    public function __toString(): string {
        $output = "\nNumeric Value Out Of Range";
        $output .= "\nMessage:         " . $this->message;
        $output .= "\nSub Code:        " . $this->subCode;
        $output .= "\nField/Key:       " . $this->field;
        $output .= "\nOffending Value: " . $this->offendingValue;
        $output .= "\n\n";
        return $output;
    }


    /**
     * @throws \Exception
     */
    protected function processBasedOnSubCode() {

        switch ( $this->subCode ):
            case self::OUT_OF_RANGE_VALUE:
                $this->processOutOfRangeValue( $this->exception );
                break;
            default:
                throw new \Exception( "The developer needs to add a new subCode to the switch in processBasedOnSubCode@NumericValueOutOfRange for " . $this->subCode );
        endswitch;
    }


    /**
     * @param QueryException $exception
     * @throws \Exception
     */
    protected function processOutOfRangeValue( QueryException $exception ) {

    }
}