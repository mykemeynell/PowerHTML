<?php

/**
 * PowerHTML template engine.
 *
 * @author: Myke Howells
 * @date: Wed 30 Mar 2016
 * @package: PowerHTML
 */

namespace PowerHTML;

use PowerHTML\Exceptions\InvalidOptionNameException;
use PowerHTML\Exceptions\TemplateDirectoryParseException;
use PowerHTML\Parsers\PowerParser;

class PowerHTML {

    /**
     * Options for PowerHTMl.
     *
     * @var array
     */
    protected $options = [
        'allow_php_eval' => true,
    ];

    function __construct( array $options = null ) {

        $this->set_option( 'template_dir', $_SERVER[ 'DOCUMENT_ROOT' ] . '/resources/templates' );

        if( is_array( $options ) && $this->is_assoc( $options ) )
            $this->set_options( $options );

        if( ! $this->parse_template_dir() )
            throw new TemplateDirectoryParseException( 'Failed to parse template directory' );

    }

    /**
     * Set an option value.
     *
     * @param $option
     * @param $value
     */
    protected function set_option( $option, $value ) {

        $this->options[ $option ] = $value;

    }

    /**
     * Read an option.
     *
     * @param $option
     * @return null
     * @throws InvalidOptionNameException
     */
    protected function option( $option ) {

        if( ! is_string( $option ) )
            throw new InvalidOptionNameException( 'Option name is expected to be a string' );

        if( ! array_key_exists( $option, $this->options ) )
            return null;

        return $this->options[ $option ];

    }

    /**
     * Get options that can be passed to the Parser.
     *
     * @return array
     * @throws InvalidOptionNameException
     */
    protected function parser_options() {

        return [
            'allow_php_eval' => $this->option( 'allow_php_eval' ),
            'template' => rtrim( $this->template_dir(), '/' ) . '/' . ltrim( $this->template, '/' ),
        ];

    }

    /**
     * Parse the template directory string to make sure everything is okay.
     *
     * @return bool
     */
    protected function parse_template_dir() {

        if( ! is_string( $this->options[ 'template_dir' ] ) )
            return false;

        $this->options[ 'template_dir' ] = rtrim(
            $this->options[ 'template_dir' ], '/'
        );

        return true;

    }

    /**
     * Get the set template directory.
     *
     * @return string
     */
    protected function template_dir() {

        return $this->option( 'template_dir' );

    }

    /**
     * @param string $template
     * @return boolean
     */
    public function set_template( $template ) {

        $this->template = $template;

        return ( $this->template == $template );

    }

    /**
     * Merge passed options into the PowerHTML options array.
     *
     * @param array $options
     * @return array
     */
    protected function set_options( array $options ) {

        $this->options = array_merge(
            $this->options,
            $options
        );

    }

    /**
     * Check if array is associative or indexed.
     *
     * @param array $array
     * @return bool
     */
    protected function is_assoc( array $array ) {

        return array_keys( $array ) !== range( 0, count( $array ) - 1 );

    }

    /**
     * Echo the parsed html out to the screen.
     */
    public function render() {

        echo $this->html;

    }

    /**
     * Return the html string rather than echo (like render()).
     *
     * @return string
     */
    public function store() {

        return $this->html;

    }

    /**
     * Pass data through to a PowerParser object.
     *
     * @param $var
     * @param $value
     * @return $this
     */
    public function with( $var, $value ) {

        global $$var;

        $$var = $value;

        return $this;

    }

    /**
     * Parse a template and set it into the html property ready for render.
     *
     * @param null $template
     * @param array $data
     * @return $this
     */
    public function parse( $template = null, array $data = null ) {

        if( is_null( $template ) )
            $template = '/index';

        $this->template = preg_replace(
            '/\\.[^.\\s]{3,4}$/i',
            '',
            $template
        );

        $parser = new PowerParser(
            $this->parser_options()
        );

        $this->html = $parser->process( $data );

        return $this;


    }

}
