<?php

namespace MichaelDrennen\SchemaChange\Tests;

use Illuminate\Database\QueryException;
use MichaelDrennen\SchemaChange\Responses\DataTooLongForColumn;
use MichaelDrennen\SchemaChange\Responses\IntegrityConstraintViolation;
use MichaelDrennen\SchemaChange\Responses\NumericValueOutOfRange;
use MichaelDrennen\SchemaChange\SchemaChange;
use PHPUnit\Framework\TestCase;

class SchemaChangeTest extends TestCase {

    public $testStrings = [
        'data_too_long_for_column_right_truncated'                  => [
            'code'   => 22001,
            'string' => "SQLSTATE[22001]: String data, right truncated: 1406 Data too long for column 'MSG_ID' at row 1 (SQL: insert into `some_table` (`SECURITY`) values (1234P8888))",
        ],
        'numeric_value_out_of_range'                                => [
            'code'   => 22003,
            'string' => "SQLSTATE[22003]: Numeric value out of range: 1264 Out of range value for column 'SOME_COLUMN' at row 1 (SQL: insert into `some_table` (`SECURITY`) values (1234P8888))",
        ],
        'integrity_constraint_can_not_be_not_null'                  => [
            'code'   => 23000,
            'string' => "SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'ID_ISIN' cannot be null (SQL: insert into `some_table` (`SECURITY`) values (1234P8888))",
        ],
        'integrity_constraint_duplicate_key'                        => [
            'code'   => 23000,
            'string' => "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '31232-123LKHJL982437' for key 'some_unique_key' (SQL: insert into `some_table` (`SECURITY`) values (1234P8888))",
        ],
        'INVALID_EXCEPTION_CODE'                                    => [
            'code'   => 99999,
            'string' => "SQLSTATE[99999]: FAKE: 9999 FAKE 'FAKE' at row 1 (SQL: SELECT FAKE FROM FAKE WHERE FAKE;)",
        ],
        'INVALID_SUB_CODE_data_too_long_for_column_right_truncated' => [
            'code'   => 22001,
            'string' => "SQLSTATE[22001]: String data, right truncated: 9999 Data too long for column 'MSG_ID' at row 1 (SQL: insert into `some_table` (`SECURITY`, `SENDERS_FIRM`, `MSG_ID`, `MSG_TIME_EPOCH_EST`, `MSG_DATETIME_EST`, `MSG_SOURCE`, `BBID`, `ID_BB_GLOBAL`, `PX_ASK`, `PX_BID`, `IS_PRICE_VERIFIED`, `CRNCY`, `MTG_DEAL_ORIG_FACE`, `QUOTE_INST`, `ID_CUSIP`, `ID_ISIN`, `SPREAD_BID`, `SPREAD_ASK`, `SPREAD_TYPE`, `BID_SIZE`, `ASK_SIZE`, `SECURITY_DES`, `SENDERS_NAME`, `ID_BB_COMPANY`, `updated_at`, `created_at`) values (1234P8888, CF  , 12345933016501D4011900E5999999999, 1566111163, 2001-01-20, MSG, F1111943, BBG00GM11165, 101.911, ?, 1, USD, 11112752.000, OFFER, 3128111Q0, US1118P8EQ07, ?, 11.406, PAYUP, ?, ?, FG C91111, BART SIMPOSON, 111984, 2001-01 18:22:42, 2001-01-10 18:22:42))",
        ],
        'INVALID_SUB_CODE_numeric_value_out_of_range' => [
            'code'   => 22003,
            'string' => "SQLSTATE[22003]: Numeric value out of range: 9999 Out of range value for column 'SOME_COLUMN' at row 1 (SQL: insert into `some_table` (`SECURITY`) values (1234P8888))",
        ],
        'INVALID_SUB_CODE_constraint_duplicate_key'                 => [
            'code'   => 23000,
            'string' => "SQLSTATE[23000]: Integrity constraint violation: 9999 Duplicate entry '1231232132-512312231231223123200E5' for key 'some_table' (SQL: insert into `some_table` (`SECURITY`, `SENDERS_FIRM`, `MSG_ID`, `MSG_TIME_EPOCH_EST`, `MSG_DATETIME_EST`, `MSG_SOURCE`, `BBID`, `ID_BB_GLOBAL`, `PX_ASK`, `PX_BID`, `IS_PRICE_VERIFIED`, `CRNCY`, `MTG_DEAL_ORIG_FACE`, `QUOTE_INST`, `ID_CUSIP`, `ID_ISIN`, `SPREAD_BID`, `SPREAD_ASK`, `SPREAD_TYPE`, `BID_SIZE`, `ASK_SIZE`, `SECURITY_DES`, `SENDERS_NAME`, `ID_BB_COMPANY`, `updated_at`, `created_at`) values (31202SCE8, CF  , 5D5BE933016501D0171900E5, 1566290813, 2001-01-01, MSG, FGC09069, BBG009INB570, 105.781, ?, 1, USD, 1010901.000, BID, 31292SCE8, US98792SCE81, ?, 1.125, PAYUP, ?, ?, FG C08769, BART SIMPSON, 782384, 2001-01-01 16:32:23, 2001-01-01 16:32:23))",
        ],

    ];

    /**
     * A helper function to prepare a QueryException for unit testing.
     * @param string $testStringType An index from the $testStrings property used to generate the exception.
     * @return QueryException
     */
    protected function getQueryExceptionOfType( string $testStringType ): QueryException {
        $exception = new \Exception( $this->testStrings[ $testStringType ][ 'string' ], $this->testStrings[ $testStringType ][ 'code' ], NULL );
        $sql       = "SOME * SQL";
        $bindings  = [ 'foo' => 'bar' ];
        return new QueryException( null, $sql, $bindings, $exception );
    }


    public function testSchemaChangeWithInvalidExceptionCodeShouldThrowException() {
        $this->expectException( \Exception::class );
        $queryException = $this->getQueryExceptionOfType( 'INVALID_EXCEPTION_CODE' );
        SchemaChange::instantiate( $queryException );
    }

    /**
     * @test
     */
    public function testSchemaChangeShouldReturnIntegrityConstraintViolationObject() {
        $queryException = $this->getQueryExceptionOfType( 'integrity_constraint_can_not_be_not_null' );
        $schemaChange   = SchemaChange::instantiate( $queryException );
        $this->assertInstanceOf( IntegrityConstraintViolation::class, $schemaChange );
    }

    /**
     * @test
     * @group toolong
     */
    public function testSchemaChangeShouldReturnDataTooLongForColumnObject() {
        $queryException = $this->getQueryExceptionOfType( 'data_too_long_for_column_right_truncated' );
        $schemaChange   = SchemaChange::instantiate( $queryException );
        $this->assertInstanceOf( DataTooLongForColumn::class, $schemaChange );
    }

    /**
     * @test
     */
    public function testSchemaChangeShouldReturnNumericValueOutOfRangeObject() {
        $queryException = $this->getQueryExceptionOfType( 'numeric_value_out_of_range' );
        $schemaChange   = SchemaChange::instantiate( $queryException );
        $this->assertInstanceOf( NumericValueOutOfRange::class, $schemaChange );
    }

    /**
     * @test
     */
    public function testPrintingDataTooLongForColumnSchemaChangeShouldEchoText() {
        $queryException = $this->getQueryExceptionOfType( 'data_too_long_for_column_right_truncated' );
        $schemaChange   = SchemaChange::instantiate( $queryException );
        echo $schemaChange;
        $pattern = '/Data Too Long For Column/';
        $this->expectOutputRegex( $pattern );
    }

    /**
     * @test
     */
    public function testPrintingIntegrityConstraintViolationSchemaChangeShouldEchoText() {
        $queryException = $this->getQueryExceptionOfType( 'integrity_constraint_can_not_be_not_null' );
        $schemaChange   = SchemaChange::instantiate( $queryException );
        echo $schemaChange;
        $pattern = '/Integrity Constraint Violation/';
        $this->expectOutputRegex( $pattern );
    }


    /**
     * @test
     */
    public function testPrintingNumericValueOutOfRangeSchemaChangeShouldEchoText() {
        $queryException = $this->getQueryExceptionOfType( 'numeric_value_out_of_range' );
        $schemaChange   = SchemaChange::instantiate( $queryException );
        echo $schemaChange;
        $pattern = '/Numeric Value Out Of Range/';
        $this->expectOutputRegex( $pattern );
    }


    /**
     * @test
     */
    public function testSchemaChangeShouldReturnDuplicateKeyError() {
        $queryException = $this->getQueryExceptionOfType( 'integrity_constraint_duplicate_key' );
        $schemaChange   = SchemaChange::instantiate( $queryException );
        $this->assertEquals( IntegrityConstraintViolation::DUPLICATE_ENTRY_FOR_KEY, $schemaChange->subCode );
    }




    /**
     * @test
     * @group invalid
     */
    public function testInvalidSubCodeForNumericValueOutOfRangeShouldThrowException() {
        $this->expectException( \Exception::class );
        $queryException = $this->getQueryExceptionOfType( 'INVALID_SUB_CODE_numeric_value_out_of_range' );
        SchemaChange::instantiate( $queryException );
    }

    /**
     * @test
     */
    public function testInvalidSubCodeForConstraintDuplicateKeyShouldThrowException() {
        $this->expectException( \Exception::class );
        $queryException = $this->getQueryExceptionOfType( 'INVALID_SUB_CODE_constraint_duplicate_key' );
        SchemaChange::instantiate( $queryException );
    }


    /**
     * @test
     */
    public function testInvalidSubCodeForDataTooLongForColumnShouldThrowException() {
        $this->expectException( \Exception::class );
        $queryException = $this->getQueryExceptionOfType( 'INVALID_SUB_CODE_data_too_long_for_column_right_truncated' );
        SchemaChange::instantiate( $queryException );
    }


}