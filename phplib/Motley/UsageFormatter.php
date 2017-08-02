<?php
/// Source code file for the Motley::UsageFormatter class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

/// Text formatter especially suited for usage explanations.
/// For example, man pages and --help screens.
/// This class implements a capability to wrap plain text not by words,
/// but rather by user-defined chunks of text. A "wrap" may occur between
/// chunks, but never within one. However a "cut" may happen if absolutely
/// needed, such as if the chunk length is longer than display width.
/// More traditional word wrap is still available.
/// It is also possible to place text at (or beyond) certain columns.
/// Additional features are also implemented, just as left and right
/// indenting, continuation indenting, multi-line column alignment,
/// maximum total length, automatic display column width detection,
/// and so forth.
class UsageFormatter {
    
    const DEFAULT_COLS  = 80; ///< Column width if it cannot be figured out.
    const COLUMNS       = "COLUMNS";      ///< Param name for column width.
    const MAX_LENGTH    = "MAX_LENGTH";   ///< Param name for max buf length.
    const LEFT_INDENT   = "LEFT_INDENT";  ///< Param name for left indent.
    const CONT_INDENT   = "CONT_INDENT";  ///< Param name for continuation indent.
    const RIGHT_INDENT  = "RIGHT_INDENT"; ///< Param name for right indent.
    const EOL           = "EOL";          ///< Param name for end-of-line chars.

    protected $params  = array();  ///< Associative array of formatting params.
    protected $paramNames = array(); ///> Valid param names.
    protected $eol   = PHP_EOL;    ///< End of line char(s).
    protected $textBuf  = "";      ///< Formatted text buffer.
    protected $newBreak = true;    ///< Is first line of a continuation group?

    /// Class instance constructor.
    public function __construct() {
        $this->params[self::COLUMNS]      = 0;   // Auto set column width later.
        $this->params[self::MAX_LENGTH]   = 0;   // No limit on buffer length.
        $this->params[self::LEFT_INDENT]  = 0;   // No indent.
        $this->params[self::CONT_INDENT]  = 0;   // No additional continuation indent.
        $this->params[self::RIGHT_INDENT] = 0;   // No additional continuation indent.
        $this->params[self::EOL]          = PHP_EOL; // End-of-line chars.
        $this->paramNames = array_keys($this->params); // Valid param key names.
    }

    /// Clear the text buffer.
    public function clear() {
        $this->textBuf = "";
        $this->newBreak = true;
    }

    /// add a line break
    public function formatBreak() {
        $this->textBuf .= $this->getParam(self::EOL);
        $this->newBreak = true;
    }

    /// Format some wrapable text.
    /// @param $text - some wrapable text. Should not generally
    ///   have any embedded line-feeds, carriage returns, or tabs.
    /// @param $col - column number (starting at 1) at which the
    ///   chunk is desired to be placed. If $col is too small or two
    ///   large for current indentations and display widths it will
    ///   be automatically adjusted. Zero means any column is ok.
    /// @param $isMinCol - false (default) means chunk placed at exactly $col,
    ///   true means at of after.
    public function formatText(
        string $text, int $col=0, bool $isMinCol=false) {
        $words = explode(" ",$text);
        $wordCnt = 0;
        foreach($words as $word) {
            $wordCnt++;
            if($wordCnt==1) {
                $this->formatChunk($word,$col,$isMinCol);
            } else {
                $this->formatChunk($word,$col,true);
            }
        }
    }

    /// Format some non-wrapable text.
    /// @param $chunk - a non-wrapable chunk of text. Should not generally
    ///   have any embedded line-feeds, carriage returns, or tabs.
    /// @param $col - column number (starting at 1) at which the
    ///   chunk is desired to be placed. If $col is too small or two
    ///   large for current indentations and display widths it will
    ///   be automatically adjusted. Zero means any column is ok.
    /// @param $isMinCol - false (default) means chunk placed at exactly $col,
    ///   true means at of after.
    public function formatChunk(
        string $chunk, int $col=0, bool $isMinCol=false) {
        $lIndent   = $this->getParam(self::LEFT_INDENT);
        $rIndent   = $this->getParam(self::RIGHT_INDENT);
        $cIndent   = $this->getParam(self::CONT_INDENT);
        $maxLen    = $this->getParam(self::MAX_LENGTH);
        $width     = $this->getColumnWidth();
        # effective width is width less right indent, but it still
        # must be at least left indent plus continue indent plus 1.
        $effWidth  = $width - $rIndent;
        $effWidth  = max($effWidth,$lIndent+$cIndent+1);
        $eol       = $this->getParam(self::EOL);
        $space     = " ";
        # column position (if not 0) must be > total indent and
        # also <= effective width
        if ($col>0) {
            $col = max($col,$lIndent+$cIndent+1);
            $col = min($col,$effWidth);
        }
        $continued = false;
        $effDelim = $space;
        $lines = explode($eol,$this->textBuf);
        $savedChunk = $chunk;
        $sameCount = 0;
        $chunk = str_replace("\n","",$chunk);
        $chunk = str_replace("\r","",$chunk);
        $chunk = str_replace("\t"," ",$chunk);
        while(strlen($chunk)>0) {
            // add defensive code to prevent accidental infinite looping
            // @codeCoverageIgnoreStart
            if($savedChunk==$chunk) {
                $sameCount++;
                if ($sameCount>3) {
                    $msg = "Infinite formatting loop detected.";
                    trigger_error($msg,E_USER_WARNING);
                    break;
                }
            } else {
                $sameCount = 0;
            }
            // @codeCoverageIgnoreEnd
            # begin processing
            $last = count($lines) - 1;  # last line index
            // handle indenting
            if($this->newBreak and strlen($lines[$last])==0) {
                # very first chunk in continuation group
                $lines[$last] .= str_repeat($space,$lIndent);
                $effDelim = "";  # no need for delimiter
                $this->newBreak = false;
            } elseif(strlen($lines[$last])==0) {
                # starting a continuation line
                $lines[$last] .= str_repeat($space,$lIndent);
                $lines[$last] .= str_repeat($space,$cIndent);
                $effDelim = "";  # no need for delimiter
            }
            // handle delimiter
            $charsRemaining = $effWidth - strlen($lines[$last]);
            if($charsRemaining>=strlen($effDelim)) {
                $lines[$last] .= $effDelim;
            } else {
                # add new line and loop again
                $lines[] = "";
                continue;
            }
            # if mincol is true, but not at col yet, turn off min flag
            if($col>0 and $isMinCol===true) {
                if(strlen($lines[$last])<=$col) {
                    $isMinCol = false;
                }
            }
            if($col>0 and $isMinCol===false) {
                # output at specific column requested
                # if past column, add new line and loop.
                if ( strlen($lines[$last]) >= $col) {
                    $lines[] = "";
                    continue;
                }
                # pad to column if needed
                $padCharsNeeded = ($col-1) - strlen($lines[$last]);
                if($padCharsNeeded>0) {
                    $lines[$last] .= str_repeat($space,$padCharsNeeded);
                }
                # add as much of $chunk as possible, cutting if needed.
                $charsRemaining = $effWidth - strlen($lines[$last]);
                if(strlen($chunk)<=$charsRemaining) {
                    # entire chunk fits
                    $lines[$last] .= $chunk;
                    $chunk = "";   # meets while loop exit condition
                } else {
                    # force to do cut
                    $portion = substr($chunk,0,$charsRemaining);
                    $lines[$last] .= $portion;
                    $chunk = substr($chunk,$charsRemaining);
                    $lines[] = "";  # add another line and loop
                    continue;
                }
            } elseif($col>0 and $isMinCol===true) {
                # output at or past specific column requested
                $charsRemaining = $effWidth - strlen($lines[$last]);
                if(strlen($chunk)<=$charsRemaining) {
                    $lines[$last] .= $chunk;
                    $chunk = "";   # meets while loop exit condition
                } else {
                    $isMinCol = false;  # now in at col mode
                    $lines[] = "";  # add another line and loop
                    continue;
                }
            } else {
                # no specific output column requested
                $charsRemaining = $effWidth - strlen($lines[$last]);
                $contWidth = $effWidth - $this->getParam(self::CONT_INDENT);
                $contWidth = $effWidth - $cIndent;
                if(strlen($chunk)<=$charsRemaining) {
                    $lines[$last] .= $chunk;
                    $chunk = "";  # while exit condition
                } elseif (strlen($chunk)<=$contWidth) {
                    # add new line and loop
                    $lines[] = "";
                    continue;
                } else {
                    # forced to do cut
                    $portion = substr($chunk,0,$charsRemaining);
                    $lines[$last] .= $portion;
                    $chunk = substr($chunk,$charsRemaining);
                    $lines[] = "";  # add another line and loop
                    continue;
                }
            }
        }   # end of while loop
        # trim possible trailing delim from lines
        for($idx=0; $idx<count($lines); $idx++) {
            $lines[$idx] = rtrim($lines[$idx]);
        }
        # convert line array into one big string and save it
        $this->textBuf = implode($eol,$lines);
    }

    /// Get the chunk buffer as a (possibly multiline) string.
    /// @return The resultant textual string.
    public function getFormattedText() : string {
        $result = $this->textBuf;
        $maxlen = $this->params[self::MAX_LENGTH];
        if($maxlen>0) {
            if (strlen($result) > $maxlen) {
                $result = substr($result,0,$maxlen);
            }
        }
        return $result;
    }

    /// Output the chunk buffer to standard output.
    public function outputFormattedText() {
        echo($this->getFormattedText());
    }

    /// Set output column width to be used by the formatter.
    /// @param $cols - The number of text character columns.
    ///   A value of zero means the column width will be automatically
    ///   determined.
    public function setColumnWidth(int $cols=0) {
        $this->params[self::COLUMNS] = $cols;
    }

    /// Get the output column width being used by formatter.
    /// This width, if too low for indentations, might be adjusted
    /// internally at run time.
    /// @returns column width being used by the formatter.
    public function getColumnWidth() : int {
        $cols = $this->getParam(self::COLUMNS);
        if($cols<=0) {
            # try 'tput cols', although this does not work on all platforms
            $out = exec('tput cols',$output,$stat);
            if($stat==0) {
                $cols=(int) $out;
            } else {
                // @codeCoverageIgnoreStart
                // Code ignore because tput always works on my linux.
                $cols = self::DEFAULT_COLS;
                // @codeCoverageIgnoreEnd
            }
            $this->setParam(self::COLUMNS,$cols);
        }
        return $cols;
    }

    /// Set the left indentation.
    /// @pamam $numCols - number of columns to indent on the left.
    public function setLeftIndent(int $numCols) {
        $this->params[self::LEFT_INDENT] = $numCols;
    }

    /// Get the left indentation.
    /// @return - number of columns to indent on the left.
    public function getLeftIndent() : int {
        return $this->params[self::LEFT_INDENT];
    }

    /// Set the right indentation.
    /// @pamam $numCols - number of columns to indent on the right.
    public function setRightIndent(int $numCols) {
        $this->params[self::RIGHT_INDENT] = $numCols;
    }

    /// Get the right indentation.
    /// @return - number of columns to indent on the right.
    public function getRightIndent() : int {
        return $this->params[self::RIGHT_INDENT];
    }

    /// Set the continue indentation.
    /// @pamam $numCols - number of additional columns to left indent continuation lines.
    public function setContinueIndent(int $numCols) {
        $this->params[self::CONT_INDENT] = $numCols;
    }

    /// Get the continue indentation.
    /// @return - number of additional columns to left indent continuation lines.
    public function getContinueIndent() : int {
        return $this->params[self::CONT_INDENT];
    }

    /// Set the maximum length of the total formatted text.
    /// @param $maxlen - The maximum length in number of text characters.
    public function setMaxLength(int $maxlen) {
        $this->params[self::MAX_LENGTH] = $maxlen;
    }

    /// Get the maximum length of the total formatted text.
    /// @return The maximum length in number of text characters.
    public function getMaxLength() : int {
        return $this->params[self::MAX_LENGTH];
    }

    /// Set the end-of-line character(s). By default the
    /// platform specific value from PHP_EOL is used.
    /// @param $eol - The end-of-line character(s) to use. The
    ///   default is PHP_EOL.
    public function setEOL(string $eol=PHP_EOL) {
        $this->params[self::EOL] = $eol;
    }

    /// Get the end-of-line character(s).
    /// @return The end-of-line character(s) in use.
    public function getEOL() {
        return $this->params[self::EOL];
    }

    /// Set a parameter value.
    /// @param $name - The parameter name.
    /// @param $value - The parameter value.
    protected function setParam(string $name, $value) {
        if (!in_array($name,$this->paramNames)) {
            $msg = "'$name' is not a valid parameter name.";
            trigger_error($msg,E_USER_WARNING);
        } else {
            $this->params[$name] = $value;
        }
    }

    /// Get a parameter value.
    /// @param $name - The parameter name.
    /// @return The parameter value.
    protected function getParam(string $name) {
        return $this->params[$name];
    }

    /// Get all parameters.
    /// @return The parameter associative array.
    protected function getParams() : array {
        return $this->params;
    }

}
?>
