<?php
/**
 * Adapted from Squiz_Sniffs_Arrays_ArrayDeclarationSniff
 *
 * A test to ensure that arrays conform to the array coding standard.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * A test to ensure that arrays conform to the array coding standard.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.4
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Symfony_Sniffs_Arrays_ArrayKeysAreUnderscoredSniff implements PHP_CodeSniffer_Sniff
{


  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register()
  {
    return array(T_ARRAY);

  }//end register()


  /**
   * Processes this sniff, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
   * @param int                  $stackPtr  The position of the current token in the
   *                                        stack passed in $tokens.
   *
   * @return void
   */
  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
  {
    $tokens = $phpcsFile->getTokens();

    $arrayStart   = $tokens[$stackPtr]['parenthesis_opener'];
    $arrayEnd     = $tokens[$arrayStart]['parenthesis_closer'];

    $nextToken  = $stackPtr;
    $keyUsed    = false;
    $indices    = array();
    $maxLength  = 0;

    // Find all the double arrows that reside in this scope.
    while (($nextToken = $phpcsFile->findNext(array(T_DOUBLE_ARROW, T_COMMA, T_ARRAY), ($nextToken + 1), $arrayEnd)) !== false) {
      $currentEntry = array();

      if ($tokens[$nextToken]['code'] === T_ARRAY) {
        // Let subsequent calls of this test handle nested arrays.
        $indices[] = array(
          'value' => $nextToken,
        );
        $nextToken = $tokens[$tokens[$nextToken]['parenthesis_opener']]['parenthesis_closer'];
        continue;
      }

      if ($tokens[$nextToken]['code'] === T_COMMA) {
        $stackPtrCount = 0;
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
          $stackPtrCount = count($tokens[$stackPtr]['nested_parenthesis']);
        }

        if (count($tokens[$nextToken]['nested_parenthesis']) > ($stackPtrCount + 1)) {
          // This comma is inside more parenthesis than the ARRAY keyword,
          // then there it is actually a comma used to seperate arguments
          // in a function call.
          continue;
        }

        if ($keyUsed === false) {
          // Find the value, which will be the first token on the line,
          // excluding the leading whitespace.
          $valueContent = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($nextToken - 1), null, true);
          while ($tokens[$valueContent]['line'] === $tokens[$nextToken]['line']) {
            if ($valueContent === $arrayStart) {
              // Value must have been on the same line as the array
              // parenthesis, so we have reached the start of the value.
              break;
            }

            $valueContent--;
          }

          $valueContent = $phpcsFile->findNext(T_WHITESPACE, ($valueContent + 1), $nextToken, true);
          $indices[]    = array('value' => $valueContent);
        }//end if

        continue;
      }//end if

      if ($tokens[$nextToken]['code'] === T_DOUBLE_ARROW) {
        $currentEntry['arrow'] = $nextToken;
        $keyUsed               = true;

        // Find the start of index that uses this double arrow.
        $indexEnd   = $phpcsFile->findPrevious(T_WHITESPACE, ($nextToken - 1), $arrayStart, true);
        $indexStart = $phpcsFile->findPrevious(T_WHITESPACE, $indexEnd, $arrayStart);

        if ($indexStart === false) {
          $index = $indexEnd;
        } else {
          $index = ($indexStart + 1);
        }

        $currentEntry['index']         = $index;
        $currentEntry['index_content'] = $phpcsFile->getTokensAsString($index, ($indexEnd - $index + 1));

        $indexLength = strlen($currentEntry['index_content']);
        if ($maxLength < $indexLength) {
          $maxLength = $indexLength;
        }

        // Find the value of this index.
        $nextContent           = $phpcsFile->findNext(array(T_WHITESPACE), ($nextToken + 1), $arrayEnd, true);
        $currentEntry['value'] = $nextContent;
        $indices[]             = $currentEntry;
      }//end if
    }//end while

    foreach ($indices as $index)
    {
      if (!isset($index['index_content']) || '' == $index['index_content'] || 0 === strpos($index['index_content'], '$'))
      {
        continue;
      }
var_dump($index['index_content']);
      if (preg_match("/([0-9]|^'[0-9a-z_]*')$/", $index['index_content']) === 0)
      {
        $error = 'Key "%s" is not in underscored or does not use valid symbols (0-9a-z_)';
        $data  = $index['index_content'];
        $phpcsFile->addError($error, $index['index'], '', $data);
      }
    }
  }//end process()
}//end class

?>
