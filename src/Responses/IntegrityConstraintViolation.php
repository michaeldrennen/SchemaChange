<?php

namespace MichaelDrennen\SchemaChange\Responses;

use Illuminate\Database\QueryException;
use MichaelDrennen\SchemaChange\Helper;

class IntegrityConstraintViolation extends AbstractSchemaChange {

    // Sub Codes
    const COLUMN_CAN_NOT_BE_NULL      = 1048;
    const DUPLICATE_ENTRY_FOR_KEY     = 1062;

    /**
     * @return string
     */
    public function __toString(): string {
        $output = "\nIntegrity Constraint Violation";
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
            case self::COLUMN_CAN_NOT_BE_NULL:
                $this->processColumnCanNotBeNull( $this->exception );
                break;
            case self::DUPLICATE_ENTRY_FOR_KEY:
                $this->processDuplicateEntryForKey( $this->exception );
                break;
            default:
                throw new \Exception("The developer needs to add a new subCode to the switch in processBasedOnSubCode@IntegrityContraintViolation for " . $this->subCode );
        endswitch;
    }


    /**
     * @param QueryException $exception
     * @throws \Exception
     */
    protected function processColumnCanNotBeNull( QueryException $exception ) {

    }


    /**
     * @param QueryException $exception
     * @throws \Exception
     */
    protected function processDuplicateEntryForKey( QueryException $exception ) {
        $this->field          = $this->getKeyWithDuplicateEntry( $exception );
        $this->offendingValue = $this->getDuplicateValueForKey( $exception );

    }


    /**
     * @param QueryException $exception
     * @return string
     * @throws \Exception
     */
    private function getKeyWithDuplicateEntry( QueryException $exception ): string {
        $pattern = "/' for key '(.*)' /";
        $message = $exception->getMessage();
        return Helper::getFirstMatchFromRegEx( $pattern, $message );
    }


    /**
     * @param QueryException $exception
     * @return string
     * @throws \Exception
     */
    private function getDuplicateValueForKey( QueryException $exception ): string {
        $pattern = '/1062 Duplicate entry \'(.*)\' for key/';
        $message = $exception->getMessage();
        return Helper::getFirstMatchFromRegEx( $pattern, $message );
    }

}