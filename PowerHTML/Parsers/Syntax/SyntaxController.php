<?php

/**
 * The Syntax Controller.
 *
 * @author: Myke Howells
 * @date: Wed 30 Mar 2016
 * @package: PowerHTML
 */

namespace PowerHTML\Parsers\Syntax;

/**
 * Class SyntaxController
 *
 * @package PowerHTML\Parsers\Syntax
 */
class SyntaxController {

    /**
     * The template directory.
     *
     * @var string
     */
    protected $dir;

    /**
     * Generic run method... probably going to be removed soon.
     *
     * @return mixed
     */
    public function run() {

        $this->html = preg_replace_callback(
            $this->pattern,
            function ( $matches ) {

                global ${$matches[1]};

                return ${$matches[1]};

            },
            $this->html
        );

        return $this->html;

    }

    /**
     * Set the template directory.
     *
     * @param $dir
     * @return $this
     */
    public function set_dir( $dir ) {

        $this->dir = '/' . trim( $dir, '/' );

        return $this;

    }

    /**
     * Set the template extension.
     *
     * @param $extension
     * @return $this
     */
    public function set_extension( $extension ) {

        $this->extension = '.' . trim( $extension, '.' );

        return $this;

    }

}