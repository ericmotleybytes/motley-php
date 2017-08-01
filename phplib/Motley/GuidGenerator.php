<?php
/// Source code file for the Motley::GuidGenerator class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

/// Generate unique 128 bit GUIDs/UUIDs as 32 hex characters.
/// This class includes routines to generate GUIDs (Globally Unique
/// IDentifiers), also called UUIDs (Universally Unique IDentifiers)
/// in accordance with UUID V4 standards (see
/// <https://en.wikipedia.org/wiki/Universally_unique_identifier>).
class GuidGenerator {

    /// Global Motley::GuidGenerator instance variable.
    /// An application global variable which contains the default sharable
    /// global GuidGenerator object instance.
    protected static $GlobalGuidGenerator = NULL;
    protected $upperFlag  = FALSE;  ///< Output GUID hex characters in uppercase?
    protected $dashesFlag = FALSE;  ///< Embed dashes in output hex?

    /// Class instance constructor.
    /// @param $upFlag - True for uppercase hex letter, false for lower.
    /// @param $dashFlag - True for embedded dashes, false for no dashes.
    public function __construct(bool $upFlag=null, bool $dashFlag=null) {
        if($upFlag==null) {
            $this->upperFlag = FALSE;
        } else {
            $this->upperFlag = $upFlag;
        }
        if($dashFlag==null) {
            $this->dashesFlag = FALSE;
        } else {
            $this->dashesFlag = $dashFlag;
        }
    }

    /// Control if resultant hex characters upper or lower case.
    /// @param bool $flag - TRUE means upper case, FALSE lower case.
    /// @returns Previous value of $this->upperFlag.
    public function setUpperFlag(bool $flag) : bool {
        $oldUpperFlag = $this->upperFlag;
        $this->upperFlag = $flag;
        return $oldUpperFlag;
    }

    /// Return true if resultant hex characters upper case.
    /// @returns TRUE is using upper case, else FALSE.
    public function getUpperFlag() : bool {
        return $this->upperFlag;
    }

    /// Control if resultant hex characters include dash separators.
    /// @param boolean $flag - TRUE means embedded dashes, FALSE no dashes.
    /// @returns Previous value of $this->dashesFlag.
    public function setDashesFlag(bool $flag) : bool {
        $oldDashesFlag = $this->dashesFlag;
        $this->dashesFlag = $flag;
        return $oldDashesFlag;
    }

    /// Return true if resultant hex characters embed dashes.
    /// @returns TRUE if embedding dashes, else FALSE.
    public function getDashesFlag() : bool {
        return $this->dashesFlag;
    }

    /// Generate a new, unique V4 GUID/UUID.
    /// Modeled after example code
    ///   at <http://php.net/manual/en/function.com-create-guid.php>.
    /// @returns A string with a hex representation of the 128 bit GUID.
    public function generateGuid() : string {
        $result = NULL;
        // @codeCoverageIgnoreStart
        // Impossible to force all fallback logic.
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            // OSX/Linux generation method
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            if ($this->dashesFlag) {
                $result = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
            } else {
                $result = vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
            }
        } elseif (function_exists('com_create_guid') === true) {
            // use Windows compatible generation method
            $result = trim(com_create_guid(),'{}');
            if ($this->dashesFlag===FALSE) {
                $result = str_replace('-','',$result);
            }
        } else {
            // generic fallback method
            mt_srand((double)microtime() * 10000);
            $charid = strtolower(md5(uniqid(rand(), true)));
            if ($this->dashesFlag) {
                $hyphen = '-';
            } else {
                $hyphen = '';
            }
            $result = substr($charid,  0,  8) . $hyphen
                . substr($charid,  8,  4) . $hyphen
                . substr($charid, 12,  4) . $hyphen
                . substr($charid, 16,  4) . $hyphen
                . substr($charid, 20, 12);
        }
        if ($this->upperFlag) {
            $result = strtoupper($result);
        } else {
            $result = strtolower($result);
        }
        // @codeCoverageIgnoreEnd
        return $result;
    }

    /// Static function to get sharable, global, GuidGenerator instance.
    /// @return An application sharable instance of Motley::GuidGenerator.
    public static function getGlobalGuidGenerator() {
        if (self::$GlobalGuidGenerator==NULL) {
            self::$GlobalGuidGenerator = new GuidGenerator();
        }
        return self::$GlobalGuidGenerator;
    }

    #/// Static function designed to run from command line utility.
    #/// @return A status code, zero on success, non-zero on error.
    #public static function commandRunner() {
    #    $statusCode = 0;  # assume ok
    #    $guidGen = new GuidGenerator();
    #    # TBD
    #    return $statusCode;
    #}

}
?>
