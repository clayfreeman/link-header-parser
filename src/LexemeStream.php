<?php

declare(strict_types = 1);

namespace ClayFreeman\LinkHeaderParser;

/**
 * Provides a wrapper used to treat a lexeme sequence as a stream.
 */
class LexemeStream {

  /**
   * The lexeme sequence to represent as a stream.
   *
   * @var \ClayFreeman\LinkHeaderParser\Lexeme[]
   */
  protected array $lexemes = [];

  /**
   * Constructs a LexemeStream object.
   *
   * @param \ClayFreeman\LinkHeaderParser\Lexeme[] $lexemes
   *   The lexeme sequence to represent as a stream.
   */
  public function __construct(array $lexemes) {
    $this->lexemes = $lexemes;
  }

  /**
   * Consumes a lexeme from the front of the stream.
   *
   * @return \ClayFreeman\LinkHeaderParser\Lexeme
   *   A lexeme from the front of the stream.
   *
   * @throws \UnderflowException
   *   If the stream is empty.
   */
  public function consume(): Lexeme {
    if ($lexeme = array_shift($this->lexemes)) {
      return $lexeme;
    }

    throw new \UnderflowException();
  }

  /**
   * Peek at the first lexeme in the stream.
   *
   * @return \ClayFreeman\LinkHeaderParser\Lexeme|null
   *   The first lexeme in the stream, or NULL if the stream is empty.
   */
  public function peek(): ?Lexeme {
    if ($lexeme = reset($this->lexemes)) {
      return $lexeme;
    }

    return NULL;
  }

}
