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
 * Class Evaluate
 *
 * @package PowerHTML\Parsers\Syntax
 */
class Evaluate extends SyntaxController {

    /**
     * Pattern for matching evaluations.
     *
     * @var string
     */
    protected $pattern = '/\\{\\{([\\$a-zA-Z0-9\\_\\(\\)\\-\\\'\\"\\s\\,]+)\\}\\}/';

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

                if( substr( $matches[1], 0, 1 ) == '$' ) {

                    $matches[1] = substr( $matches[1], 1 );

                    global ${$matches[1]};

                    return ${$matches[1]};

                } else {

                    return eval( "return {$matches[1]};" );

                }

            },
            $this->html
        );

        return $this->html;

    }

}