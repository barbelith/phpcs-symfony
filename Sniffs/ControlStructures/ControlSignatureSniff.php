<?php

if (class_exists('PHP_CodeSniffer_Standards_AbstractPatternSniff', true) === false) {
  throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractPatternSniff not found');
}

/**
 * Verifies that control statements conform to their coding standards.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Pedro Palmero <pedropalmero@gmail.com>
 */
class Symfony_Sniffs_ControlStructures_ControlSignatureSniff extends PHP_CodeSniffer_Standards_AbstractPatternSniff
{

  /**
   * A list of tokenizers this sniff supports.
   *
   * @var array
   */
  public $supportedTokenizers = array(
    'PHP'
  );


  /**
   * Returns the patterns that this test wishes to verify.
   *
   * @return array(string)
   */
  protected function getPatterns()
  {
    return array(
      'tryEOL...{...}EOL...catch (...)EOL...{',
      'doEOL...{...} while (...);EOL',
      'while (...)EOL...{',
      'for (...)EOL...{',
      'if (...)EOL...{',
      'foreach (...)EOL...{',
      '}EOLelse if (...)EOL...{',
      '}EOLelseEOL...{',
    );

  }//end getPatterns()


}//end class

?>