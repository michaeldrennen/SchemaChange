<?php

namespace MichaelDrennen\SchemaChange\Tests;

use Illuminate\Database\QueryException;
use MichaelDrennen\SchemaChange\Helper;
use MichaelDrennen\SchemaChange\IntegrityConstraintViolation;
use MichaelDrennen\SchemaChange\SchemaChange;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase {


    /**
     * @test
     */
    public function testGetFirstMatchFromRexExShouldReturnString(){
        $pattern = '/ (quick) /';
        $subject = "The quick brown fox jumped over the lazy dog.";
        $match = Helper::getFirstMatchFromRegEx($pattern, $subject);
        $this->assertEquals('quick', $match);
    }


    /**
     * @test
     */
    public function testGetFirstMatchFromRexExWithBadPatternShouldThrowException(){
        $this->expectException(\Exception::class);
        $pattern = '/ (bobcat) /';
        $subject = "The quick brown fox jumped over the lazy dog.";
        Helper::getFirstMatchFromRegEx($pattern, $subject);
    }

    /**
     * @test
     */
    public function testGetFirstMatchFromRexExWithGoodPatternButNoMatchShouldThrowException(){
        $this->expectException(\Exception::class);
        $pattern = '/The quick (\d*)brown/';
        $subject = "The quick brown fox jumped over the lazy dog.";
        Helper::getFirstMatchFromRegEx($pattern, $subject);
    }

}