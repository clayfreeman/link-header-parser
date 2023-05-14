<?php

declare(strict_types = 1);

namespace ClayFreeman\LinkHeaderParser;

use ClayFreeman\LinkHeaderParser\LexemeStream;
use ClayFreeman\LinkHeaderParser\Lexer\ParameterListAnalysisTrait;
use ClayFreeman\LinkHeaderParser\Lexer\URIReferenceAnalysisTrait;

use Psr\Http\Message\StreamInterface;

/**
 * Used to perform lexical analysis on a HTTP 'Link' header value.
 */
class Lexer {

  use ParameterListAnalysisTrait;
  use URIReferenceAnalysisTrait;

  /**
   * Run lexical analysis on the supplied stream and produce a lexeme stream.
   *
   * HTTP 'Link' header values have this format:
   *   link-value = "<" URI-Reference ">" *( OWS ";" OWS link-param )
   *   link-param = token BWS [ "=" BWS ( token / quoted-string ) ]
   *
   * This method is responsible for delegating the lexical analysis for each
   * part of the value to a method which produces a generator, and collecting
   * each generator result.
   *
   * @param \Psr\Http\Message\StreamInterface $input
   *   The input stream from which to produce a lexeme stream.
   *
   * @return \ClayFreeman\LinkHeaderParser\LexemeStream
   *   A lexeme stream resulting from the lexical analysis.
   */
  public function analyze(StreamInterface $input): LexemeStream {
    // Define the grammatical structure of the HTTP 'Link' header value.
    $lexeme_generators = [
      $this->analyzeURIReference($input),
      $this->analyzeParameters($input),
    ];

    // Generate a sequence of lexemes after running the analysis.
    foreach ($lexeme_generators as $lexeme_generator) {
      foreach ($lexeme_generator as $lexeme) {
        $lexemes[] = $lexeme;
      }
    }

    // Create a lexeme stream from the resulting list of lexemes.
    return new LexemeStream($lexemes ?? []);
  }

}
