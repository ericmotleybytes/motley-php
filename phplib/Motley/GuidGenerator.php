<?php
/**
 * @file
 * @brief    File for Motley::GuidGenerator.
 * @details   This file has the source code for the Motley::GuidGenerator class.
 * @see       Motley::GuidGenerator
 * @author    Eric Alan Christiansen
 * @date      2017
 * @copyright Copyright (c) 2017, Eric Alan Christiansen.
 *            MIT License. See <https://opensource.org/licenses/MIT>.
 */
namespace Motley;

/**
 * @brief   Generate unique 128 bit GUIDs/UUIDs as 32 hex characters.
 * @details This class includes routines to generate GUIDs (Globally Unique
 *          IDentifies), also called UUIDs (Universally Unique IDentifiers)
 *          in accordance with UUID V4 standards
 *          (see <https://en.wikipedia.org/wiki/Universally_unique_identifier>).
 * @remark  This file uses Doxygen compatible comments.
 */
class GuidGenerator {

    /**
     * @brief   Global Motley::GuidGenerator instance variable.
     * @details An application global variable which contains the default sharable
     *          global GuidGenerator object instance.
     */
    protected static $GlobalGuidGenerator = NULL;
    protected $upperFlag  = FALSE;  ///< Output GUID hex characters in uppercase?
    protected $dashesFlag = FALSE;  ///< Embed dashes in output hex?

    /**
     * @brief class instance constructor.
     */
    public function __construct(boolean $upFlag=null, boolean $dashFlag=null) {
        if($upFlag!=null) {
            $this->upperFlag  = FALSE;
        }
        if($dashFlag!=null) {
            $this->dashesFlag = FALSE;
        }
    }

    /**
     * @brief Static function to get sharable, global, GuidGenerator instance.
     * @returns An application sharable instance of Motley::GuidGenerator.
     */
    public static function getGlobalGuidGenerator() {
        if (self::$GlobalGuidGenerator==NULL) {
            self::$GlobalGuidGenerator = new GuidGenerator();
        }
        return self::$GlobalGuidGenerator;
    }

    /**
     * @brief Control if resultant hex characters upper or lower case.
     */
    public function setUpperFlag(boolean $flag) {
        $oldUpperFlag = $this->upperFlag;
        $this->upperFlag = $flag;
        return $oldUpperFlag;
    }

    /**
     * @brief Control if resultant hex characters include dash separators.
     */
    public function setDashesFlag(boolean $flag) {
        $oldDashesFlag = $this->dashesFlag;
        $this->dashesFlag = $flag;
        return $oldDashesFlag;
    }

    /**
     * @brief Generate a new, unique V4 GUID/UUID.
     * @remark Modeled after example code
     *         at http://php.net/manual/en/function.com-create-guid.php
     * @returns A string with a hex representation of the 128 bit GUID.
     */
    public function generateGuid() {
        $result = NULL;
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
        return $result;
    }
}
?>
