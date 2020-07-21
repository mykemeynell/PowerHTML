<?php

/**
 * PowerHTML template engine, PowerParser parsing agent.
 *
 * @author: Myke Howells
 * @date: Wed 30 Mar 2016
 * @package: PowerHTML
 */

namespace PowerHTML\Parsers;

use PowerHTML\Exceptions\InvalidEvalMarkerString;
use PowerHTML\Exceptions\TemplateNotFoundException;
use PowerHTML\PowerHTML;


/**
 * PowerParser, used for parsing the PowerHTML files and reading executable functions within the HTML code.
 */
class PowerParser extends PowerHTML {

    /**
     * Options array.
     *
     * @var array
     */
    protected $options = [
        'allow_php_eval' => true,
        'template' => '',
        'eval_markers' => '{{|}}',
    ];

    /**
     * File extension for PowerHTML templates.
     *
     * @var string
     */
    private $extension = '.pwr.html';

    /**
     * Regex for matching variables within expected tags.
     *
     * @var string
     */
    private $eval_regex = '';

    /**
     * Regex for matching include statements.
     *
     * @var string
     */
    private $include_regex = '/\\@include\\((?<file>.+)\\)/';

    private static $parser_map = [
        'eval' => '\\PowerHTML\\Parsers\\Syntax\\Evaluate',
        'include' => '\\PowerHTML\\Parsers\\Syntax\\Includer',
    ];

    /**
     * Template output html.
     *
     * @var string
     */
    protected $html = '';

    /**
     * PowerParser constructor.
     *
     * @param array|null $options
     */
    function __construct( array $options = null ) {

        if( is_array( $options ) && $this->is_assoc( $options ) )
            $this->set_options( $options );

        $this->write_eval_expression();

    }

    /**
     * Get the eval regex.
     *
     * @return string
     */
    private function get_eval_regex() {

        return $this->eval_regex;

    }

    /**
     * Set the eval regex.
     *
     * @param string $eval_regex
     * @return PowerParser
     */
    private function set_eval_regex( $eval_regex ) {

        $this->eval_regex = $eval_regex;
        return $this;

    }

    /**
     * Get the include regex.
     *
     * @return string
     */
    private function get_include_regex() {

        return $this->include_regex;

    }

    /**
     * Parse the eval markers and make suitable for regex pattern.
     *
     * @throws InvalidEvalMarkerString
     * @throws \PowerHTML\Exceptions\InvalidOptionNameException
     */
    private function parse_eval_markers() {

        $eval_markers = str_split(
            $this->option( 'eval_markers' )
        );

        foreach( $eval_markers as $key => $char )
            $eval_markers[ $key ] = "\\{$char}";

        $eval_markers = implode( '', $eval_markers );

        $eval_markers = str_replace(
            '\\|',
            '|',
            $eval_markers
        );

        if( strpos( $this->option( 'eval_markers' ), '|' ) === 0 )
            throw new InvalidEvalMarkerString( 'Eval markers must have a pipe characters separating opening and closing markers' );

        if( substr_count( $eval_markers, '|' ) > 1 )
            throw new InvalidEvalMarkerString( 'The eval marker string contains too many pipes' );

        $this->set_option( 'eval_markers', $eval_markers );

    }

    /**
     * Write the regex that would be used for matching a string that is to be evaluated.
     *
     * @throws InvalidEvalMarkerString
     * @throws \PowerHTML\Exceptions\InvalidOptionNameException
     */
    private function write_eval_expression() {

        $eval_expression = '(?<expr>\\$[a-zA-Z0-9\\_]+)';

        $this->parse_eval_markers();

        $this->set_eval_regex(
            '/' . str_replace( '|', $eval_expression, $this->option( 'eval_markers' ) ) . '/'
        );

    }

    /**
     * Get the full template path.
     *
     * @return string
     */
    private function template_path() {

        return $this->option( 'template' ) . '.' . ltrim( $this->extension, '.' );

    }

    /**
     * Get the HTML from a template.
     *
     * @return $this
     * @throws TemplateNotFoundException
     */
    private function get_template_html() {

        if( ! file_exists( $this->template_path() ) )
            throw new TemplateNotFoundException( 'Template could not be found at \'' . $this->template_path() . '\'' );

        $this->html = file_get_contents(
            $this->template_path()
        );

        return $this;

    }

    /**
     * Run a set of regular expression handlers.
     *
     * @param array $regex
     * @return string
     */
    private function run_regex( array $regex ) {

        if( empty( $regex ) )
            return $this->html;

        $map = [];

        foreach( $regex as $parser ) {

            $agent = new static::$parser_map[ $parser ];

            $this->html = $agent->set_html( $this->html )
                ->set_dir( substr( $this->template_path(), 0, strrpos( $this->template_path(), '/') ) )
                ->set_extension( $this->extension )
                ->run();

        }

        return $this->html;

    }

    /**
     * Start the parsing process.
     *
     * @param array|null $data
     * @return string
     * @throws TemplateNotFoundException
     * @throws \PowerHTML\Exceptions\InvalidOptionNameException
     */
    public function process( array $data = null ) {

        $regex = [
            'include',
        ];

        if( $this->option( 'allow_php_eval' ) == true )
            array_push( $regex, 'eval' );

        $this->get_template_html();

        if( ! empty( ( array ) $data ) )
            foreach( $data as $key => $value ) {

                global $$key;
                $$key = $value;

            }

        return $this->run_regex( $regex );

    }

}
