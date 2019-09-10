<?php

namespace MichaelDrennen\SchemaChange\Responses;

use Illuminate\Database\QueryException;

class DataTooLongForColumn extends AbstractSchemaChange {

    // Sub Codes
    const STRING_DATA_RIGHT_TRUNCATED = 1406;

    /**
     * @return string
     */
    public function __toString(): string {
        $output = "\nData Too Long For Column";
        $output .= "\nMessage:         " . $this->message;
        $output .= "\nSub Code:        " . $this->subCode;
        $output .= "\nField/Key:       " . $this->field;
        $output .= "\nOffending Value: " . $this->offendingValue;
        $output .= "\n\n";
        return $output;
    }



    /**
     *
     */
    protected function processBasedOnSubCode() {
        switch ( $this->subCode ):

            case self::STRING_DATA_RIGHT_TRUNCATED:
                $this->processStringDataRightTruncated( $this->exception );
                break;
            default:
                throw new \Exception("The developer needs to add a new subCode to the switch in processBasedOnSubCode@DataTooLongForColumn for: " . $this->subCode );
        endswitch;
    }


    protected function processStringDataRightTruncated( QueryException $exception ) {

    }
}