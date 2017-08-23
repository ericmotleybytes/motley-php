<?php
/// Source code file for the Motley::CommandIO class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

/// Wrapper class for common I/O functions.
class CommandIO {

    protected $filename = null;    ///< File name.
    protected $mode     = null;    ///< File mode.
    protected $handle   = null;    ///< File handle.

    /// Class instance constructor.
    /// @param $filename - File name/specification.
    /// @param $mode - File mode.
    /// @param $use_include_path - Use php include path.
    /// @param $context - Stream context.
    public function __construct(string $filename, string $mode) {
        $fh = fopen($filename,$mode);
        if ($fh===false) { throw new Exception("Could not open '$filename'."); }
        $this->filename = $filename;
        $this->mode     = $mode;
        $this->handle   = $fh;
    }

    // Class instance destructor to close open file.
    public function __destruct() {
        if(is_resource($this->handle)) {
            $status = fclose($this->handle);
        }
    }

    /// Get the associated filename.
    /// @returns The associated filename/specification.
    public function getFilename() {
        return $this->filename;
    }

    /// Get the associated file handle.
    /// @returns The associated file handle.
    public function getHandle() {
        return $this->handle;
    }

    /// Get the open file mode.
    /// @returns the file mode of the open file.
    public function getMode() {
        return $this->mode;
    }

    /// Reopen the associated file.
    /// @returns true on success, else false;
    public function reopen(string $filename,string $mode) {
        fflush($this->handle);
        fclose($this->handle);
        $fh = fopen($filename,$mode);
        if ($fh===false) { return false; }
        $this->filename = $filename;
        $this->mode     = $mode;
        $this->handle   = $fh;
        return true;
    }

    /// Force the file to close, may have side-effects.
    /// @returns TRUE on success, FALSE on failure.
    public function close() {
        $result = false;
        if(is_resource($this->handle)) {
            $result = fclose($this->handle);
        }
        return $result;
    }

    /// Test for end-of-file condition.
    /// @returns TRUE if at EOF or error (including timeoout); otherwise FALSE.
    public function eof() {
        $result = feof($this->handle);
        return $result;
    }

    /// Flush output.
    /// @returns TRUE on success, FALSE on failure.
    public function flush() {
        $result = fflush($this->handle);
        return $result;
    }

    /// Read a character.
    /// @returns Single character read or FALSE on error or EOF.
    public function getc() {
        $result = fgetc($this->handle);
        return $result;
    }

    /// Read a line and parse for CSV fields.
    /// @param $length - Max length of input line (0=unlimited).
    /// @param $delimiter - Field delimiter.
    /// @param $enclosure - Intra field enclosure character.
    /// @param $escape - Escape character.
    /// @returns Array with fields read, or NULL if bad handle, or FALSE on EOF, etc.
    public function getcsv(int $length=0, string $delimiter=",",
        string $enclosure='"', string $escape="\\") {
        $result = fgetcsv($this->handle,$length,$delimiter,$enclosure);
        return $result;
    }

    /// Read a line.
    /// @param $length - Max length of string/line (0=unlimited).
    /// @returns A string up to $length-1 bytes long. or FALSE on EOF or other error.
    public function gets(int $length=0) {
        $args = array($this->handle);
        if($length>0) { $args[] = $length; }
        $result = fgets(...$args);
        #$result = call_user_func_array("fgets",$args);
        return $result;
    }

    /// Read a line and strip HTML tags.
    /// @param $length - Max length of input line (0=unlimited).
    /// @param $allowable_tags - HTML tags to allow.
    /// @returns A string up to $length-1 bytes long. or FALSE on EOF or other error.
    public function getss(int $length=0, string $allowable_tags="") {
        $args = array($this->handle);
        if(strlen($allowable_tags)>0 and $length==0) { $length=5000; }
        if($length>0) { $args[] = $length; }
        if(strlen($allowable_tags)>0) { $args[] = $allowable_tags; }
        $result = fgetss(...$args);
        return $result;
    }

    /// Advisory file locking.
    /// @param $operation - LOCK_SH, LOCK_EX, or LOCK_UN.
    /// @param &$wouldblock - Variable passed by ref set to 1 if lock would block.
    /// @return TRUE on success, FALSE on failure.
    public function lock(int $operation, int &$wouldblock=null) {
        $wb = -1;
        $result = flock($this->handle,$operation,$wb);
        if(!is_null($wouldblock)) { $wouldblock = $wb; }
        return $result;
    }

    /// Format line as CSV and write it.
    /// @param $fields - Array of stuff to write.
    /// @param $delimiter - Field delimiter.
    /// @param $enclosure - Intra-field enclosure character.
    /// @param $escape - Escapre character.
    /// @returns Length of string written or FALSE on error.
    public function putcsv(array $fields, string $delimiter=",",
        string $enclosure='"', string $escape="\\") {
        $result = fputcsv($this->handle,$fields,$delimiter,$enclosure,$escape);
        return $result;
    }

    /// Write a string.
    /// @param $string - The string to write.
    /// @param $length - Max number of bytes to write (0=unlimited).
    /// @returns The number of bytes written or FALSE on error.
    public function puts(string $string, int $length=0) {
        $args = array($this->handle,$string);
        if($length>0) { $args[] = $length; }
        $result = fwrite(...$args);
        return $result;
    }

    /// Read some bytes.
    /// @param $length - Max number of bytes read.
    /// @returns The string read or FALSE on error.
    public function read(int $length) {
        $result = fread($this->handle,$length);
        return $result;
    }

    /// Parses line input from a file according to a format.
    /// @param $format - The input format.
    /// @returns An array of values parsed (or FALSE on error).
    public function scanf(string $format) {
        $result = fscanf($this->handle,$format);
        return $result;
    }

    /// Seeks on a file pointer.
    /// @param $offset - Byte offset in file.
    /// @param $whence - SEET_SET, SEEK_CUR, or SEEK_END.
    /// @returns 0 on success, else -1.
    public function seek(int $offset, int $whence=SEEK_SET) : int {
        $result = fseek($this->handle,$offset,$whence);
        return $result;
    }

    /// Gets information about a file using an open file pointer.
    /// @returns An array with the statistics of the file.
    public function stat() {
        $result = fstat($this->handle);
        return $result;
    }

    /// Get the current position of the file read/write pointer.
    /// @returns The current position of the file read/write pointer, false on error.
    public function tell() {
        $result = ftell($this->handle);
        return $result;
    }

    /// Truncates a file to a given length.
    /// @param $size - Size in bytes to truncate file to.
    /// @returns TRUE on success, FALSE on error.
    public function truncate(int $size) {
        $result = ftruncate($this->handle,$size);
        return $result;
    }

    /// Write a string.
    /// @param $string - The string to write.
    /// @param $length - Max number of bytes to write (0=unlimited).
    /// @returns The number of bytes written or FALSE on error.
    public function write(string $string, int $length=0) {
        $args = array($this->handle,$string);
        if($length>0) { $args[] = $length; }
        $result = fwrite(...$args);
        return $result;
    }

    /// Rewind the position of a file pointer.
    /// @returns TRUE on success, FALSE on failure.
    public function rewind() {
        $result = rewind($this->handle);
        return $result;
    }

    /// Echo write a string.
    /// @param $strings - String to print.
    public function echo(...$strings) {
        $result = 0;
        foreach($strings as $string) {
            $bytes = fwrite($this->handle,$string);
            if($bytes===false) { return false; }
            $result = $result + $bytes;
        }
        return $result;
    }

    /// Output a string.
    /// @param $arg - The string to output.
    /// @return 1.
    public function print(string $arg) {
        fwrite($this->handle,$arg);
        return 1;
    }

    /// Output human readable info about a variable.
    /// @param $expression - The expression to be output.
    public function print_r($expression) {
        $str = print_r($expression,true);
        $result = $this->write($str);
        if($result!==false) { $result = true; }
        return $result;
    }

    /// Output formatted data.
    /// @param $format - Output formatting string.
    /// @param $args - Output arguments.
    /// @returns The number of bytes written (or false on error).
    public function printf(string $format, ...$args) {
        $fargs = array($this->handle,$format);
        foreach($args as $arg) {
            $fargs[] = $arg;
        }
        $result = fprintf(...$fargs);
        return $result;
    }

    /// Output formatted data.
    /// @param $format - Output formatting string.
    /// @param $args - Array of output arguments.
    /// @returns The number of bytes written (or false on error).
    public function vprintf(string $format, array $args) {
        foreach($args as $arg) {
            $fargs[] = $arg;
        }
        $result = vfprintf($this->handle,$format,$args);
        return $result;
    }

}
?>
