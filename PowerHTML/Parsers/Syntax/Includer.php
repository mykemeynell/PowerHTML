<?php

/**
 * PowerHTML template engine, PowerParser parsing agent.
 *
 * @author: Myke Howells
 * @date: Wed 30 Mar 2016
 * @package: PowerHTML
 */

namespace PowerHTML\Parsers\Syntax;

/**
 * Class Includer.
 *
 * @package PowerHTML\Parsers\Syntax
 */
class Includer extends SyntaxController {

    /**
     * Pattern for matching evaluations.
     *
     * @var string
     */
    protected $pattern = '/\\@include\\((?<file>.+)\\)/';

    /**
     * The HTML.
     *
     * @var string
     */
    protected $html = '';

    /**
     * Take the HTML and set for Evaluate class.
     *
     * @param $html
     * @return $this
     */
    public function set_html($html) {

        $this->html = $html;

        return $this;

    }

    /**
     * Evaluate code.
     * 
     * @return mixed|string
     */
    public function run() {

        $this->html = preg_replace_callback(
            $this->pattern,
            function ( $matches ) {

                $file_name = preg_replace( "/[^a-zA-Z0-9\\/]+/", "", html_entity_decode( $matches['file'], ENT_QUOTES ) );

                $file_location = $this->dir . '/' . $file_name . '.' . ltrim( $this->extension, '.' );

                $contents = ( file_exists( $file_location ) ) ? file_get_contents( $file_location ) : null;

                return $contents;

            },
            $this->html
        );

        return $this->html;

    }

}