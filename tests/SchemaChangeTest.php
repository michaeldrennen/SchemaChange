<?php

namespace MichaelDrennen\SchemaChange\Tests;

use Illuminate\Database\QueryException;
use MichaelDrennen\SchemaChange\SchemaChange;
use PHPUnit\Framework\TestCase;

class SchemaChangeTest extends TestCase {

    public $testStrings = [
        'integrity_constraint' => "SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'ID_ISIN' cannot be null (SQL: insert into `f2_bloomberg_message_mtges` (`SECURITY`, `SENDERS_FIRM`, `MSG_ID`, `MSG_TIME_EPOCH_EST`, `MSG_DATETIME_EST`, `MSG_SOURCE`, `BBID`, `ID_BB_GLOBAL`, `PX_ASK`, `PX_BID`, `IS_PRICE_VERIFIED`, `CRNCY`, `MTG_DEAL_ORIG_FACE`, `QUOTE_INST`, `ID_CUSIP`, `ID_ISIN`, `SPREAD_BID`, `SPREAD_ASK`, `SPREAD_TYPE`, `BID_SIZE`, `ASK_SIZE`, `SECURITY_DES`, `SENDERS_NAME`, `ID_BB_COMPANY`, `updated_at`, `created_at`) values (CX2HA10C, GS  , 5D5BF1840142014401190104, 1566292292, 2019-08-20, MSG, BCC2L04T, BBG00PZS45W3, 100.125, ?, 0, USD, 40000000.000, OFFER, BCC2L04T5, ?, ?, ?, ?, ?, ?, GNR 2019-106 CF, GS AGENCY CMO, 700545, 2019-09-09 16:15:09, 2019-09-09 16:15:09))",
    ];

    /**
     * @test
     */
    public function testSchemaChange() {

        $exception      = new \Exception( $this->testStrings[ 'integrity_constraint' ], 23000, NULL );
        $sql            = "SOME * SQL";
        $bindings       = [ 'foo' => 'bar' ];
        $queryException = new QueryException($sql,$bindings,$exception);

        $schemaChange = new SchemaChange($queryException);

    }
}