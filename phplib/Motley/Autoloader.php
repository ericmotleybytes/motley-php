<?php
/// Class source code file and autoload registration.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
///   MIT License. See <https://opensource.org/licenses/MIT>.
namespace Motley;

/// Static routines to support Motley autoloading.
class Autoloader {
    const AutoloadClassName    = "Motley\Autoloader";
    const AutoloadFunctionName = "autoload";

    /// Attempt to find source file from class name, and if found, include the file.
    /// @param $class_name - The string class name of a class.
    /// @return Returns false if no good file match found.
    public static function autoload(string $class_name) {
        // first replace back slashes with forward slashes.
        $file_name = str_replace("\\","/",$class_name);
        // add the expected file suffix.
        $file_name = $file_name . ".php";
        // look in parent directory of this source file, but using a sub-directory
        // path driven my namespaces.
        $candidate = __DIR__ . '/../' . $file_name;
        $status = false;
        if(file_exists($candidate)) {
            $status = include_once($candidate);
        } else {
            // try to find something in the include_path.
            $candidate = stream_resolve_include_path($file_name);
            if($candidate!==false) {
                // @codeCoverageIgnoreStart
                $status = include_once($candidate);
                // @codeCoverageIgnoreEnd
            }
        }
        return $status;
    }

    /// Registers the Motley\Autoloader\autoload routine as an autoloader.
    /// @return TRUE on success, else FALSE.
    public static function register() : bool {
        if(self::isRegistered()===false) {
            $status = spl_autoload_register(
                array(self::AutoloadClassName,self::AutoloadFunctionName));
        }
        return self::isRegistered();
    }

    /// Un-registers the Motley\Autoloader\autoload routine as an autoloader.
    /// @return TRUE on success, else FALSE.
    public static function unregister() : bool {
        if(self::isRegistered()===true) {
            $status = spl_autoload_unregister(
                array(self::AutoloadClassName,self::AutoloadFunctionName));
        }
        return self::isRegistered();
    }

    public static function isRegistered() : bool {
        $result = false;
        $callable = array(self::AutoloadClassName,self::AutoloadFunctionName);
        $autoloaders = spl_autoload_functions();
        if(is_array($autoloaders)) {
            foreach($autoloaders as $autoloader) {
                if(is_array($autoloader)) {
                    if($autoloader[0]==$callable[0]) {
                        if($autoloader[1]==$callable[1]) {
                            $result = true;
                            break;
                        }
                    }
                }
            }
        }
        return $result;
    }

}  // end of class Motley\Autoloader
// register this autoload function
\Motley\Autoloader::register();
?>
