<?php

declare(strict_types = 1);

namespace ClayFreeman\LinkHeaderParser;

/**
 * Enumerates the types of HTTP 'Link' header value tokens.
 */
enum Token: string {

  /**
   * Used to identify a parameter name within a HTTP 'Link' header.
   */
  case ParameterName = 'parameter_name';

  /**
   * Used to identify a parameter value within a HTTP 'Link' header.
   */
  case ParameterValue = 'parameter_value';

  /**
   * Used to identify the URI Reference within a HTTP 'Link' header.
   */
  case URIReference = 'uri_reference';

}
