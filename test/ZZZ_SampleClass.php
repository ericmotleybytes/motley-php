<?php
/// Sample class for unit testing.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.

use PHPUnit\Framework\Testcase;
use Motley\UnitTestSupport;

/// A class with protected and private methods.
/// This allows other classes which use reflection to be more thoroughly
/// tested.
class ZZZ_SampleClass {
    const PUBFUN    = "Public Function";        ///< Public function result.
    const PROFUN    = "Protected Function";     ///< Protected function result.
    const PRIFUN    = "Private Function";       ///< Private function result.
    const STAPUBFUN = "Static Public Function"; ///< Static public result.
    const STAPROFUN = "Protected Function";     ///< Static protected result.
    const STAPRIFUN = "Private Function";       ///< Static private result.

    /// Class constructor.
    public function __construct() {}

    /// A sample public function.
    public function publicFunction() : string {
        return self::PUBFUN;
    }

    /// A sample protected function.
    public function protectedFunction() : string {
        return self::PROFUN;
    }

    /// A sample private function.
    public function privateFunction() : string {
        return self::PRIFUN;
    }

    /// A sample static public function.
    public static function staticPublicFunction() : string {
        return self::STAPUBFUN;
    }

    /// A sample static protected function.
    public function staticProtectedFunction() : string {
        return self::STAPROFUN;
    }

    /// A sample static private function.
    public function staticPrivateFunction() : string {
        return self::STAPRIFUN;
    }

}
?>
