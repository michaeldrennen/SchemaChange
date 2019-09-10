<?php

namespace MichaelDrennen\SchemaChange\Responses;

use Illuminate\Database\QueryException;
use MichaelDrennen\SchemaChange\Helper;

abstract class AbstractSchemaChange implements SchemaChangeInterface {
    protected $exception;
    public    $subCode;
    public    $field;
    public    $offendingValue;
    public    $message;


    /**
     * AbstractSchemaChange constructor.
     * @param QueryException $exception
     * @throws \Exception
     */
    public function __construct( QueryException $exception ) {
        $this->exception = $exception;
        $this->subCode   = $this->getSubCodeFromException( $exception );
        $this->message   = $this->getMessageFromException( $exception );
        $this->field     = $this->getFieldOrKeyWithTheIssue( $exception );
        $this->processBasedOnSubCode( $this->subCode );
    }


    /**
     * @param QueryException $exception
     * @return int
     * @throws \Exception
     */
    protected function getSubCodeFromException( QueryException $exception ): int {
        $pattern = '/SQLSTATE\[\d*\]: .*: (\d*) /';
        $message = $exception->getMessage();
        return Helper::getFirstMatchFromRegEx( $pattern, $message );
    }


    /**
     * @param QueryException $exception
     * @return string
     * @throws \Exception
     */
    protected function getMessageFromException( QueryException $exception ): string {
        $pattern = '/(SQLSTATE.*) \(SQL/';
        $message = $exception->getMessage();
        return Helper::getFirstMatchFromRegEx( $pattern, $message );
    }


    /**
     * @param QueryException $exception
     * @return string
     * @throws \Exception
     */
    protected function getFieldOrKeyWithTheIssue( QueryException $exception ): string {
        $pattern = "/SQLSTATE\[\d*\]: .* '(.*)' .*/";
        $message = $exception->getMessage();
        return Helper::getFirstMatchFromRegEx( $pattern, $message );
    }

}