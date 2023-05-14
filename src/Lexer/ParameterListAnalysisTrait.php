<?php

declare(strict_types = 1);

namespace ClayFreeman\LinkHeaderParser\Lexer;

use ClayFreeman\LinkHeaderParser\Lexeme;
use ClayFreeman\LinkHeaderParser\Token;

use Psr\Http\Message\StreamInterface;

/**
 * A trait used to perform lexical analysis on a 'Link' header parameter list.
 */
trait ParameterListAnalysisTrait {

  use BaseLexerTrait;

  /**
   * Generate a sequence of lexemes for a parameter.
   *
   * Each parameter consists of a name, optionally followed by a value.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a sequence of lexemes.
   *
   * @return \Generator|\ClayFreeman\LinkHeaderParser\Lexeme[]
   *   A sequence of lexemes representing a parameter.
   */
  private function analyzeParameter(StreamInterface $input): \Generator {
    foreach ($this->analyzeParameterName($input) as $lexeme) {
      yield $lexeme;
    }

    // Determine whether to generate a lexeme for a parameter value.
    if ($this->peek($input) === '=') {
      foreach ($this->analyzeParameterValue($input) as $lexeme) {
        yield $lexeme;
      }
    }
  }

  /**
   * Generate a lexeme for a parameter name.
   *
   * Lexical analysis for parameter names is intentionally very loose; this
   * method only expects to find a parameter name delimiter and makes no
   * guarantee that the 'token' character class requirement is satisfied.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a lexeme.
   *
   * @return \Generator|\ClayFreeman\LinkHeaderParser\Lexeme[]
   *   A lexeme representing a parameter name.
   */
  private function analyzeParameterName(StreamInterface $input): \Generator {
    $this->read($input, "\t ");
    $name = $this->readUntil($input, "\t ;=", 'expecting a parameter name');
    $this->read($input, "\t ");

    yield new Lexeme(Token::ParameterName, $name);
  }

  /**
   * Generate a lexeme for a parameter quoted string value.
   *
   * A quoted string is surrounded by double quotes on either side and may
   * contain two byte escape sequences within it which must be processed.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a lexeme.
   *
   * @return \Generator|\ClayFreeman\LinkHeaderParser\Lexeme[]
   *   A lexeme representing a parameter value.
   */
  private function analyzeParameterQuotedStringValue(StreamInterface $input): \Generator {
    $this->discard($input);

    $value = $this->readUntil($input, '"\\');
    while ($this->peek($input) === '\\') {
      $this->discard($input);

      $value .= $input->read(1);
      $value .= $this->readUntil($input, '"\\');
    }

    $this->read($input, '"', 'expecting a double quote to delimit the end of the quoted string');
    yield new Lexeme(Token::ParameterValue, $value);
  }

  /**
   * Generate a lexeme for a parameter token value.
   *
   * Lexical analysis for parameter token values is intentionally very loose;
   * this method only expects to find a parameter value delimiter and makes no
   * guarantee that the 'token' character class requirement is satisfied.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a lexeme.
   *
   * @return \Generator|\ClayFreeman\LinkHeaderParser\Lexeme[]
   *   A lexeme representing a parameter value.
   */
  private function analyzeParameterTokenValue(StreamInterface $input): \Generator {
    $value = $this->readUntil($input, "\t ;", 'expecting a parameter value');
    yield new Lexeme(Token::ParameterValue, $value);
  }

  /**
   * Generate a lexeme for a parameter value.
   *
   * Parameter values can either be quoted strings or conform to the 'token'
   * class which is unquoted. This method detects which one is used to determine
   * which subsequent lexical analysis step is required.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a lexeme.
   *
   * @return \Generator|\ClayFreeman\LinkHeaderParser\Lexeme[]
   *   A lexeme representing a parameter value.
   */
  private function analyzeParameterValue(StreamInterface $input): \Generator {
    $this->discard($input);

    // Consume all leading whitespace (if present).
    $this->read($input, "\t ");

    // Determine whether the parameter value is a quoted string or token.
    if ($this->peek($input) === '"') {
      $result = $this->analyzeParameterQuotedStringValue($input);
    }
    else {
      $result = $this->analyzeParameterTokenValue($input);
    }

    foreach ($result as $lexeme) {
      yield $lexeme;
    }

    // Consume all trailing whitespace (if present).
    $this->read($input, "\t ");
  }

  /**
   * Generate a sequence of lexemes for an optional parameter list.
   *
   * If no parameter list is present, this method won't generate any lexemes.
   *
   * Parameters are read continuously until the end of the parameter list is
   * encountered (delimited by a 0x20 byte or EOF). Successive parameters are
   * separated by a semicolon (optionally surrounded by whitespace).
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a sequence of lexemes.
   *
   * @return \Generator|\ClayFreeman\LinkHeaderParser\Lexeme[]
   *   A sequence of lexemes representing an optional parameter list.
   */
  protected function analyzeParameters(StreamInterface $input): \Generator {
    if ($this->peek($input) === ';') {
      yield from $this->analyzeParametersPresent($input);
    }

    yield from [];
  }

  /**
   * Generate a sequence of lexemes for a parameter list.
   *
   * This method expects that the presence of a parameter list has been
   * established prior to being invoked.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a sequence of lexemes.
   *
   * @return \Generator|\ClayFreeman\LinkHeaderParser\Lexeme[]
   *   A sequence of lexemes representing a parameter list.
   */
  private function analyzeParametersPresent(StreamInterface $input): \Generator {
    do {
      // Discard the leading parameter delimiter.
      $this->discard($input);

      // Attempt to process a parameter, yielding its lexemes.
      foreach ($this->analyzeParameter($input) as $lexeme) {
        yield $lexeme;
      }
    } while ($this->peek($input) === ';');

    // Consume all trailing whitespace (if present).
    $this->read($input, "\t ");
  }

}
