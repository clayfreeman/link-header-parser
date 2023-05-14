<?php

declare(strict_types = 1);

namespace ClayFreeman\LinkHeaderParser\Lexer;

use Psr\Http\Message\StreamInterface;

/**
 * A trait to define the internal base operations of our lexer.
 *
 * This symbol is primarily used in other traits to ensure that certain
 * functionality can safely be assumed to be available.
 */
trait BaseLexerTrait {

  /**
   * Discards the byte at the front of the input stream.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to discard a byte.
   */
  protected function discard(StreamInterface $input): void {
    $input->read(1);
  }

  /**
   * Report a lexical error in the input stream.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream in which the lexical error occurred.
   * @param string|null $extra
   *   A string containing extra contextual information about the error.
   *
   * @throws \UnexpectedValueException
   *   An exception describing a lexical error in the input stream.
   */
  protected function error(StreamInterface $input, ?string $extra = NULL): void {
    $subject = 'end of line';

    // If the stream is not empty, use the next byte in the stream.
    if (strlen($this->peek($input)) === 1) {
      // By default, assume that the byte is unprintable.
      $subject = sprintf('0x%02X', ord($byte = $this->peek($input)));

      // If the byte is printable, run it through var_export() for readability.
      if (ord($byte) >= 33 && ord($byte) <= 126) {
        $subject = var_export($byte, TRUE);
      }
    }

    // Build the exception message, appending the extra info as applicable.
    $error = "Unexpected {$subject} at position {$input->tell()}";
    $error = !empty($extra) ? "{$error}; {$extra}" : $error;

    throw new \UnexpectedValueException($error);
  }

  /**
   * Peek at the next byte in the stream.
   *
   * This method will retrieve the current stream position and store it. Next,
   * an attempt is made to read a single byte from the stream. Finally, the
   * stream is reset to its original position and the character is returned.
   *
   * If an error occurs, or if the resulting read produced an empty string, this
   * method will return an empty string.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The stream from which to peek at the next byte.
   *
   * @return string
   *   The next character in the stream, or an empty string if none.
   */
  protected function peek(StreamInterface $input): string {
    try {
      $pos = $input->tell();
      $chr = $input->read(1);

      if (is_string($chr) && strlen($chr) === 1) {
        $input->seek($pos);
        return $chr;
      }
    }
    catch (\Throwable $e) {
    }

    return '';
  }

  /**
   * Reads from the input stream while bytes match the specified class.
   *
   * The first byte to violate the class will not be read.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to read.
   * @param string $class
   *   A class of bytes that will be accepted as valid from the input stream.
   * @param string|null $empty_error
   *   The error message to use if the result is empty (default: NULL).
   *
   * @return string
   *   The result of reading while bytes match the specified class.
   */
  protected function read(StreamInterface $input, string $class, ?string $empty_error = NULL): string {
    $allowed_bytes = str_split($class);
    $result = '';

    while (strlen($next = $this->peek($input)) === 1 && in_array($next, $allowed_bytes, TRUE)) {
      $result .= $input->read(1);
    }

    if (strlen($result) === 0 && isset($empty_error)) {
      $this->error($input, $empty_error);
    }

    return $result;
  }

  /**
   * Reads from the input stream until a delimiter or EOF is encountered.
   *
   * The specified delimiter class is not consumed when reading from the stream.
   * If an empty delimiter class is supplied, the entire remaining content of
   * the stream will be returned.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to read.
   * @param string $class
   *   A string of zero or more one-byte delimiters at which to stop reading.
   * @param string|null $empty_error
   *   The error message to use if the result is empty (default: NULL).
   *
   * @return string
   *   The result of reading until a delimiter or EOF is encountered.
   */
  protected function readUntil(StreamInterface $input, string $class, ?string $empty_error = NULL): string {
    $delimiters = str_split($class);
    $result = '';

    while (strlen($next = $this->peek($input)) === 1 && !in_array($next, $delimiters, TRUE)) {
      $result .= $input->read(1);
    }

    if (strlen($result) === 0 && isset($empty_error)) {
      $this->error($input, $empty_error);
    }

    return $result;
  }

}
