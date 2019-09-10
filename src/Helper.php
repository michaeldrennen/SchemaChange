<?php

namespace MichaelDrennen\SchemaChange;

use Illuminate\Database\QueryException;

class Helper {


    /**
     * @param string $pattern
     * @param string $subject
     * @return mixed
     * @throws \Exception
     */
    public static function getFirstMatchFromRegEx( string $pattern, string $subject ) {

        $found = preg_match( $pattern, $subject, $matches );

        if ( 1 !== $found ):
            $exception = new \Exception( "Unable to find pattern in $pattern for subject: $subject" );
            throw $exception;
        endif;

        if ( FALSE === isset( $matches[ 1 ] ) || empty($matches[ 1 ]) ):
            $exception = new \Exception( "Unable to find match for the pattern $pattern" );
            throw $exception;
        endif;

        return $matches[ 1 ];
    }

}