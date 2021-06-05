<?php
/**
 * Website: http://sourceforge.net/projects/simplehtmldom/
 * Acknowledge: Jose Solorzano (https://sourceforge.net/projects/php-html/)
 *
 * Licensed under The MIT License
 * See the LICENSE file in the project root for more information.
 *
 * Authors:
 *   S.C. Chen
 *   John Schlick
 *   Rus Carroll
 *   logmanoriginal
 *
 * Contributors:
 *   Yousuke Kumakura
 *   Vadim Voituk
 *   Antcs
 *   Igor (Dicr) Tarasov
 *
 * Version $Rev$
 */

namespace simplehtmldom;

use Exception;

use function array_key_exists;
use function call_user_func_array;
use function count;
use function file_get_contents;
use function file_put_contents;
use function func_get_args;
use function function_exists;
use function html_entity_decode;
use function iconv;
use function in_array;
use function is_array;
use function is_file;
use function ltrim;
use function mb_detect_encoding;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function sprintf;
use function strcasecmp;
use function strcspn;
use function strlen;
use function strncmp;
use function strpos;
use function strspn;
use function strtolower;
use function substr;
use function substr_replace;
use function trigger_error;
use function trim;

use const E_USER_ERROR;
use const ENT_HTML5;
use const ENT_QUOTES;
use const LOCK_EX;
use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/HtmlNode.php';
require_once __DIR__ . '/Debug.php';

/**
 * HTML Document.
 *
 * @method bool|$this load_file(...$args)
 * @method void clear()
 *
 * @property-read string $outertext
 * @property-read string $innertext
 * @property-read string $plaintext
 * @property-read string $charset
 * @property-read string $target_charset
 */
class HtmlDocument
{
    /** @var HtmlNode */
    public $root;

    /** @var HtmlNode[] */
    public $nodes = [];

    /** @var callable|null */
    public $callback;

    /** @var bool */
    public $lowercase = false;

    /** @var int */
    public $original_size;

    /** @var int */
    public $size;

    /** @var int */
    protected $pos;

    /** @var string */
    protected $doc;

    /** @var string|null */
    protected $char;

    /** @var int */
    protected $cursor;

    /** @var HtmlNode */
    protected $parent;

    /** @var string[] */
    protected $noise = [];

    /** @var string */
    protected $token_blank = " \t\r\n";

    /** @var string */
    protected $token_equal = ' =/>';

    /** @var string */
    protected $token_slash = " />\r\n\t";

    /** @var string */
    protected $token_attr = ' >';

    /** @var string */
    public $_charset = '';

    /** @var string */
    public $_target_charset = '';

    /** @var string */
    public $default_br_text = '';

    /** @var string */
    public $default_span_text = '';

    /** @var string[] */
    const SELF_CLOSING_TAGS = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'
    ];

    /** @var string[] */
    const BLOCK_TAGS = [
        'body', 'div', 'form', 'root', 'span', 'table'
    ];

    /** @var string[][] */
    const OPTIONAL_CLOSING_TAGS = [
        // Not optional, see
        // https://www.w3.org/TR/html/textlevel-semantics.html#the-b-element
        'b' => ['b'],
        'dd' => ['dd', 'dt'],
        // Not optional, see
        // https://www.w3.org/TR/html/grouping-content.html#the-dl-element
        'dl' => ['dd', 'dt'],
        'dt' => ['dd', 'dt'],
        'li' => ['li'],
        'optgroup' => ['optgroup', 'option'],
        'option' => ['optgroup', 'option'],
        'p' => ['p'],
        'rp' => ['rp', 'rt'],
        'rt' => ['rp', 'rt'],
        'td' => ['td', 'th'],
        'th' => ['td', 'th'],
        'tr' => ['td', 'th', 'tr']
    ];

    /** @var ?array */
    protected $optional_closing_array;

    /**
     * @param string $func
     * @param array $args
     * @return mixed|void
     */
    public function __call($func, $args)
    {
        // Allow users to call methods with lower_case syntax
        switch ($func) {
            case 'load_file':
                $actual_function = 'loadFile';
                break;

            case 'clear':
                return; /* no-op */

            default:
                trigger_error(
                    'Call to undefined method ' . __CLASS__ . '::' . $func . '()',
                    E_USER_ERROR
                );
        }

        // phpcs:ignore Generic.Files.LineLength
        Debug::log(__CLASS__ . '->' . $func .
            '() has been deprecated and will be removed in the next major version of simplehtmldom. Use ' . __CLASS__ .
            '->' . $actual_function . '() instead.');

        return call_user_func_array([$this, $actual_function], $args);
    }

    /**
     * HtmlDocument constructor.
     *
     * @param string|null $str
     * @param bool $lowercase
     * @param bool $forceTagsClosed
     * @param string $target_charset
     * @param bool $stripRN
     * @param string $defaultBRText
     * @param string $defaultSpanText
     * @param int $options
     */
    public function __construct(
        $str = null,
        $lowercase = true,
        $forceTagsClosed = true,
        $target_charset = DEFAULT_TARGET_CHARSET,
        $stripRN = true,
        $defaultBRText = DEFAULT_BR_TEXT,
        $defaultSpanText = DEFAULT_SPAN_TEXT,
        $options = 0
    ) {
        if ($str) {
            if (preg_match('/^http:\/\//i', $str) || is_file($str)) {
                $this->load_file($str);
            } else {
                $this->load(
                    $str,
                    $lowercase,
                    $stripRN,
                    $defaultBRText,
                    $defaultSpanText,
                    $options
                );
            }
        } else {
            $this->prepare($str, $lowercase, $defaultBRText, $defaultSpanText);
        }

        // Forcing tags to be closed implies that we don't trust the html, but
        // it can lead to parsing errors if we SHOULD trust the html.
        if (! $forceTagsClosed) {
            $this->optional_closing_array = [];
        }

        $this->_target_charset = $target_charset;
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'root' => $this->root,
            'noise' => empty($this->noise) ? 'none' : $this->noise,
            'charset' => $this->_charset,
            'target charset' => $this->_target_charset,
            'original size' => $this->original_size
        ];
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (isset($this->nodes)) {
            foreach ($this->nodes as $n) {
                $n->clear();
            }
        }
    }

    /**
     * @param string $str
     * @param bool $lowercase
     * @param bool $stripRN
     * @param string $defaultBRText
     * @param string $defaultSpanText
     * @param int $options
     * @return $this
     */
    public function load(
        $str,
        $lowercase = true,
        $stripRN = true,
        $defaultBRText = DEFAULT_BR_TEXT,
        $defaultSpanText = DEFAULT_SPAN_TEXT,
        $options = 0
    ) {
        // prepare
        $this->prepare($str, $lowercase, $defaultBRText, $defaultSpanText);

        if ($stripRN) {
            // Temporarily remove any element that shouldn't loose whitespace
            $this->remove_noise("'<\s*script[^>]*>(.*?)<\s*/\s*script\s*>'is");
            $this->remove_noise("'<!\[CDATA\[(.*?)\]\]>'is");
            $this->remove_noise("'<!--(?!>|\->)(.*?)-->'is");
            $this->remove_noise("'<\s*style[^>]*>(.*?)<\s*/\s*style\s*>'is");
            $this->remove_noise("'<\s*(?:code)[^>]*>(.*?)<\s*/\s*(?:code)\s*>'is");

            // Remove whitespace and newlines between tags
            $this->doc = preg_replace('/\>([\t\s]*[\r\n]^[\t\s]*)\</m', '><', $this->doc);

            // Remove whitespace and newlines in text
            $this->doc = preg_replace('/([\t\s]*[\r\n]^[\t\s]*)/m', ' ', $this->doc);

            // Restore temporarily removed elements and calculate new size
            $this->doc = $this->restore_noise($this->doc);
            $this->size = strlen($this->doc);
        }

        $this->remove_noise("'(<\?)(.*?)(\?>)'s", true); // server-side script
        if (! empty($this->noise)) {
            // phpcs:ignore Generic.Files.LineLength
            Debug::log('Support for server-side scripts has been deprecated and will be removed in the next major version of simplehtmldom.');
        }

        if ($options & HDOM_SMARTY_AS_TEXT) { // Strip Smarty scripts
            $this->remove_noise("'(\{\w)(.*?)(\})'s", true);
            // phpcs:ignore Generic.Files.LineLength
            Debug::log('Support for Smarty scripts has been deprecated and will be removed in the next major version of simplehtmldom.');
        }

        // parsing
        $this->parse($stripRN);

        // end
        $this->root->_[HtmlNode::HDOM_INFO_END] = $this->cursor;
        $this->parse_charset();
        $this->decode();
        unset($this->doc);

        // make load function chainable
        return $this;
    }

    /**
     * @param callable|null $function_name
     */
    public function set_callback($function_name)
    {
        $this->callback = $function_name;
    }

    /**
     * Remove callback
     */
    public function remove_callback()
    {
        $this->callback = null;
    }

    /**
     * @param string $filepath
     * @return string
     */
    public function save($filepath = '')
    {
        $ret = $this->root->innertext();
        if ($filepath !== '') {
            file_put_contents($filepath, $ret, LOCK_EX);
        }

        return $ret;
    }

    /**
     * @param string $selector
     * @param int|null $idx
     * @param bool $lowercase
     * @return HtmlNode|HtmlNode[]|null
     */
    public function find($selector, $idx = null, $lowercase = false)
    {
        return $this->root->find($selector, $idx, $lowercase);
    }

    /**
     * @param string $selector
     * @param int|null $idx
     * @param bool $lowercase
     * @return HtmlNode|HtmlNode[]|null
     */
    public function expect($selector, $idx = null, $lowercase = false)
    {
        return $this->root->expect($selector, $idx, $lowercase);
    }

    /**
     * @param bool $show_attr
     * @codeCoverageIgnore
     */
    public function dump($show_attr = true)
    {
        $this->root->dump($show_attr);
    }

    /**
     * @param string $str
     * @param bool $lowercase
     * @param string $defaultBRText
     * @param string $defaultSpanText
     */
    protected function prepare(
        $str,
        $lowercase = true,
        $defaultBRText = DEFAULT_BR_TEXT,
        $defaultSpanText = DEFAULT_SPAN_TEXT
    ) {
        $this->clear();

        $this->doc = trim($str);
        $this->size = strlen($this->doc);
        $this->original_size = $this->size; // original size of the html
        $this->pos = 0;
        $this->cursor = 1;
        $this->noise = [];
        $this->nodes = [];
        $this->lowercase = $lowercase;
        $this->default_br_text = $defaultBRText;
        $this->default_span_text = $defaultSpanText;
        $this->root = new HtmlNode($this);
        $this->root->tag = 'root';
        $this->root->_[HtmlNode::HDOM_INFO_BEGIN] = -1;
        $this->root->nodetype = HtmlNode::HDOM_TYPE_ROOT;
        $this->parent = $this->root;

        if ($this->size > 0) {
            $this->char = $this->doc[0];
        }
    }

    /**
     * Decode
     */
    protected function decode()
    {
        foreach ($this->nodes as $node) {
            if (isset($node->_[HtmlNode::HDOM_INFO_TEXT])) {
                $node->_[HtmlNode::HDOM_INFO_TEXT] = html_entity_decode(
                    $this->restore_noise($node->_[HtmlNode::HDOM_INFO_TEXT]),
                    ENT_QUOTES | ENT_HTML5,
                    $this->_target_charset
                );
            }

            if (isset($node->_[HtmlNode::HDOM_INFO_INNER])) {
                $node->_[HtmlNode::HDOM_INFO_INNER] = html_entity_decode(
                    $this->restore_noise($node->_[HtmlNode::HDOM_INFO_INNER]),
                    ENT_QUOTES | ENT_HTML5,
                    $this->_target_charset
                );
            }

            if (isset($node->attr) && is_array($node->attr)) {
                foreach ($node->attr as $a => $v) {
                    if ($v === true) {
                        continue;
                    }

                    $node->attr[$a] = html_entity_decode(
                        $v,
                        ENT_QUOTES | ENT_HTML5,
                        $this->_target_charset
                    );
                }
            }
        }
    }

    /**
     * @param bool $trim
     */
    protected function parse($trim = false)
    {
        while (true) {
            if ($this->char !== '<') {
                $content = $this->copy_until_char('<');

                if ($content !== '') {
                    // Skip whitespace between tags? (</a> <b>)
                    if ($trim && trim($content) === '') {
                        continue;
                    }

                    $node = new HtmlNode($this);
                    ++$this->cursor;
                    $node->_[HtmlNode::HDOM_INFO_TEXT] = $content;
                    $this->link_nodes($node, false);
                }
            }

            if ($this->read_tag($trim) === false) {
                break;
            }
        }
    }

    /**
     * @return string
     */
    protected function parse_charset()
    {
        $charset = null;

        if (function_exists('get_last_retrieve_url_contents_content_type')) {
            $contentTypeHeader = get_last_retrieve_url_contents_content_type();
            $success = preg_match('/charset=(.+)/', $contentTypeHeader, $matches);
            if ($success) {
                $charset = $matches[1];
            }

            // phpcs:ignore Generic.Files.LineLength
            Debug::log('Determining charset using get_last_retrieve_url_contents_content_type() ' .
                ($success ? 'successful' : 'failed'));
        }

        if (empty($charset)) {
            // https://www.w3.org/TR/html/document-metadata.html#statedef-http-equiv-content-type
            $el = $this->root->find('meta[http-equiv=Content-Type]', 0, true);

            if (! empty($el)) {
                $fullValue = $el->{'content'};

                if (! empty($fullValue)) {
                    $success = preg_match(
                        '/charset=(.+)/i',
                        $fullValue,
                        $matches
                    );

                    if ($success) {
                        $charset = $matches[1];
                    }
                }
            }
        }

        if (empty($charset)) {
            // https://www.w3.org/TR/html/document-metadata.html#character-encoding-declaration
            $meta = $this->root->find('meta[charset]', 0);
            if ($meta !== null) {
                $charset = $meta->{'charset'};
            }
        }

        // Try to guess the charset based on the content
        // Requires Multibyte String (mbstring) support (optional)
        if (empty($charset) && function_exists('mb_detect_encoding')) {
            /**
             * mb_detect_encoding() is not intended to distinguish between
             * charsets, especially single-byte charsets. Its primary
             * purpose is to detect which multibyte encoding is in use,
             * i.e. UTF-8, UTF-16, shift-JIS, etc.
             *
             * -- https://bugs.php.net/bug.php?id=38138
             *
             * Adding both CP1251/ISO-8859-5 and CP1252/ISO-8859-1 will
             * always result in CP1251/ISO-8859-5 and vice versa.
             *
             * Thus, only detect if it's either UTF-8 or CP1252/ISO-8859-1
             * to stay compatible.
             */
            $encoding = mb_detect_encoding(
                $this->doc,
                ['UTF-8', 'CP1252', 'ISO-8859-1']
            );

            if ($encoding === 'CP1252' || $encoding === 'ISO-8859-1') {
                // Due to a limitation of mb_detect_encoding
                // 'CP1251'/'ISO-8859-5' will be detected as
                // 'CP1252'/'ISO-8859-1'. This will cause iconv to fail, in
                // which case we can simply assume it is the other charset.
                if (! @iconv('CP1252', 'UTF-8', $this->doc)) {
                    $encoding = 'CP1251';
                }
            }

            if ($encoding !== false) {
                $charset = $encoding;
            }
        }

        if (empty($charset)) {
            Debug::log('Unable to determine charset from source document. Assuming UTF-8');
            $charset = 'UTF-8';
        }

        // Since CP1252 is a superset, if we get one of it's subsets, we want
        // it instead.
        if (in_array(strtolower($charset), ['iso-8859-1', 'latin1', 'latin-1'])) {
            $charset = 'CP1252';
        }

        return $this->_charset = $charset;
    }

    /**
     * @param bool $trim
     * @return bool
     */
    protected function read_tag($trim)
    {
        if ($this->char !== '<') { // End Of File
            $this->root->_[HtmlNode::HDOM_INFO_END] = $this->cursor;

            // We might be in a nest of unclosed elements for which the end tags
            // can be omitted. Close them for faster seek operations.
            do {
                if (array_key_exists(strtolower($this->parent->tag), self::OPTIONAL_CLOSING_TAGS)) {
                    $this->parent->_[HtmlNode::HDOM_INFO_END] = $this->cursor;
                }

                $this->parent = $this->parent->parent;
            } while (! empty($this->parent));

            return false;
        }

        $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

        if ($trim) { // "<   /html>"
            $this->skip($this->token_blank);
        }

        // End tag: https://dev.w3.org/html5/pf-summary/syntax.html#end-tags
        if ($this->char === '/') {
            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

            $tag = $this->copy_until_char('>');
            $tag = $trim ? ltrim($tag, $this->token_blank) : $tag;

            // Skip attributes and whitespace in end tags
            if ($trim) {
                $pos = strpos($tag, ' ');
                if ($pos !== false) {
                    // phpcs:ignore Generic.Files.LineLength
                    Debug::log_once('Source document contains superfluous whitespace in end tags (</html   >).');
                    $tag = substr($tag, 0, $pos);
                }
            }

            if (strcasecmp($this->parent->tag, $tag)) { // Parent is not start tag
                $parent_lower = strtolower($this->parent->tag);
                $tag_lower = strtolower($tag);

                if (array_key_exists($parent_lower, self::OPTIONAL_CLOSING_TAGS) &&
                    in_array($tag_lower, self::BLOCK_TAGS, true)) {
                    $org_parent = $this->parent;

                    // Look for the start tag
                    while (($this->parent->parent) && strtolower($this->parent->tag) !== $tag_lower) {
                        // Close any unclosed element with optional end tags
                        if (array_key_exists(strtolower($this->parent->tag), self::OPTIONAL_CLOSING_TAGS)) {
                            $this->parent->_[HtmlNode::HDOM_INFO_END] = $this->cursor;
                        }

                        $this->parent = $this->parent->parent;
                    }

                    // No start tag, close grandparent
                    if (strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $org_parent;

                        if ($this->parent->parent) {
                            $this->parent = $this->parent->parent;
                        }

                        $this->parent->_[HtmlNode::HDOM_INFO_END] = $this->cursor;

                        return $this->as_text_node($tag);
                    }
                } elseif (! empty($this->parent->parent) && in_array($tag_lower, self::BLOCK_TAGS, true)) {
                    // grandparent exists + current is block tag
                    // Parent has no end tag
                    $this->parent->_[HtmlNode::HDOM_INFO_END] = 0;
                    $org_parent = $this->parent;

                    // Find start tag
                    while ($this->parent->parent && strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $this->parent->parent;
                    }

                    // No start tag, close parent
                    if (strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $org_parent; // restore original parent
                        $this->parent->_[HtmlNode::HDOM_INFO_END] = $this->cursor;

                        return $this->as_text_node($tag);
                    }
                } elseif (($this->parent->parent) && strtolower($this->parent->parent->tag) === $tag_lower) {
                    // Grandparent exists and current tag closes it
                    $this->parent->_[HtmlNode::HDOM_INFO_END] = 0;
                    $this->parent = $this->parent->parent;
                } else { // Random tag, add as text node
                    return $this->as_text_node($tag);
                }
            }

            // Link with start tag
            $this->parent->_[HtmlNode::HDOM_INFO_END] = $this->cursor;

            if ($this->parent->parent) {
                $this->parent = $this->parent->parent;
            }

            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

            return true;
        }

        // Start tag: https://dev.w3.org/html5/pf-summary/syntax.html#start-tags
        $node = new HtmlNode($this);
        $node->_[HtmlNode::HDOM_INFO_BEGIN] = $this->cursor++;

        // Tag name
        $tag = $this->copy_until($this->token_slash);

        if (isset($tag[0]) && $tag[0] === '!') { // Doctype, CData, Comment
            if (isset($tag[2]) && $tag[1] === '-' && $tag[2] === '-') { // Comment ("<!--")
                /**
                 * Comments must have the following format:
                 *
                 * 1. The string "<!--"
                 *
                 * 2. Optionally, text, with the additional restriction that the
                 * text must not start with the string ">", nor start with the
                 * string "->", nor contain the strings "<!--", "-->", or "--!>",
                 * nor end with the string "<!-".
                 *
                 * 3. The string "-->"
                 *
                 * -- https://www.w3.org/TR/html53/syntax.html#comments
                 */

                // Go back until $tag only contains start of comment "!--".
                while (strlen($tag) > 3) {
                    $this->char = $this->doc[--$this->pos]; // previous
                    $tag = substr($tag, 0, -1);
                }

                $node->nodetype = HtmlNode::HDOM_TYPE_COMMENT;
                $node->tag = 'comment';

                $data = '';

                while (true) {
                    // Copy until first char of end tag
                    $data .= $this->copy_until_char('-');

                    // Look ahead in the document, maybe we are at the end
                    if (($this->pos + 3) > $this->size) { // End of document
                        Debug::log('Source document ended unexpectedly!');
                        break;
                    }

                    if (substr($this->doc, $this->pos, 3) === '-->') { // end
                        $data .= $this->copy_until_char('>');
                        break;
                    }

                    $data .= $this->char;
                    $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                }

                if ($data[0] === '>') { // "<!-->"
                    Debug::log('Comment must not start with the string ">"!');
                    $this->pos -= strlen($data);
                    $this->char = $this->doc[$this->pos];
                    $data = '';
                }

                if (strncmp($data, '->', 2) === 0) { // "<!--->"
                    Debug::log('Comment must not start with the string "->"!');
                    $this->pos -= strlen($data);
                    $this->char = $this->doc[$this->pos];
                    $data = '';
                }

                if (strpos($data, '<!--') !== false) { // "<!--<!---->"
                    Debug::log('Comment must not contain the string "<!--"!');
                    // simplehtmldom can work with it anyway
                }

                if (strpos($data, '--!>') !== false) { // "<!----!>-->"
                    Debug::log('Comment must not contain the string "--!>"!');
                    // simplehtmldom can work with it anyway
                }

                if (substr($data, -3, 3) === '<!-') { // "<!--<!--->"
                    Debug::log('Comment must not end with "<!-"!');
                    // simplehtmldom can work with it anyway
                }

                $tag .= $data;
                $tag = $this->restore_noise($tag);

                // Comment starts after "!--" and ends before "--" (5 chars total)
                $node->_[HtmlNode::HDOM_INFO_INNER] = substr($tag, 3, -2);
            } elseif (substr($tag, 1, 7) === '[CDATA[') {
                // Go back until $tag only contains start of cdata "![CDATA[".
                while (strlen($tag) > 8) {
                    $this->char = $this->doc[--$this->pos]; // previous
                    $tag = substr($tag, 0, -1);
                }

                // CDATA can contain HTML stuff, need to find closing tags first
                $node->nodetype = HtmlNode::HDOM_TYPE_CDATA;
                $node->tag = 'cdata';

                $data = '';

                // There is a rare chance of empty CDATA: "<[CDATA[]]>"
                // In which case the current char is the first "[" of the end tag
                // But the CDATA could also just be a bracket: "<[CDATA[]]]>"
                while (true) {
                    // Copy until first char of end tag
                    $data .= $this->copy_until_char(']');

                    // Look ahead in the document, maybe we are at the end
                    if (($this->pos + 3) > $this->size) { // End of document
                        Debug::log('Source document ended unexpectedly!');
                        break;
                    }

                    if (substr($this->doc, $this->pos, 3) === ']]>') { // end
                        $data .= $this->copy_until_char('>');
                        break;
                    }

                    $data .= $this->char;
                    $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                }

                $tag .= $data;
                $tag = $this->restore_noise($tag);

                // CDATA starts after "![CDATA[" and ends before "]]" (10 chars total)
                $node->_[HtmlNode::HDOM_INFO_INNER] = substr($tag, 8, -2);
            } else { // Unknown
                Debug::log('Source document contains unknown declaration: <' . $tag);
                $node->nodetype = HtmlNode::HDOM_TYPE_UNKNOWN;
                $node->tag = 'unknown';
            }

            $node->_[HtmlNode::HDOM_INFO_TEXT] = '<' . $tag . $this->copy_until_char('>');

            if ($this->char === '>') {
                $node->_[HtmlNode::HDOM_INFO_TEXT] .= '>';
            }

            $this->link_nodes($node, true);
            $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

            return true;
        }

        if (! preg_match('/^\w[\w:-]*$/', $tag)) { // Invalid tag name
            $node->_[HtmlNode::HDOM_INFO_TEXT] = '<' . $tag . $this->copy_until('<>');

            if ($this->char === '>') { // End tag
                $node->_[HtmlNode::HDOM_INFO_TEXT] .= '>';
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
            }

            $this->link_nodes($node, false);
            Debug::log('Source document contains invalid tag name: ' . $node->_[HtmlNode::HDOM_INFO_TEXT]);

            return true;
        }

        // Valid tag name
        $node->nodetype = HtmlNode::HDOM_TYPE_ELEMENT;
        $tag_lower = strtolower($tag);
        $node->tag = ($this->lowercase) ? $tag_lower : $tag;

        if (array_key_exists($tag_lower, self::OPTIONAL_CLOSING_TAGS)) { // Optional closing tag
            while (in_array(strtolower($this->parent->tag), self::OPTIONAL_CLOSING_TAGS[$tag_lower])) {
                // Previous element was the last element of ancestor
                $this->parent->_[HtmlNode::HDOM_INFO_END] = $node->_[HtmlNode::HDOM_INFO_BEGIN] - 1;
                $this->parent = $this->parent->parent;
            }

            $node->parent = $this->parent;
        }

        $guard = 0; // prevent infinity loop

        // [0] Space between tag and first attribute
        $space = [$this->copy_skip($this->token_blank), '', ''];

        do { // Parse attributes
            $name = $this->copy_until($this->token_equal);

            if ($name === '' && $this->char !== null && $space[0] === '') {
                break;
            }

            if ($guard === $this->pos) { // Escape infinite loop
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                continue;
            }

            $guard = $this->pos;

            if ($this->pos >= $this->size - 1 && $this->char !== '>') { // End Of File
                Debug::log('Source document ended unexpectedly!');
                $node->nodetype = HtmlNode::HDOM_TYPE_TEXT;
                $node->_[HtmlNode::HDOM_INFO_END] = 0;
                $node->_[HtmlNode::HDOM_INFO_TEXT] = '<' . $tag . $space[0] . $name;
                $node->tag = 'text';
                $this->link_nodes($node, false);

                return true;
            }

            if ($name === '/' || $name === '') { // No more attributes
                break;
            }

            // [1] Whitespace after attribute name
            $space[1] = (strpos($this->token_blank, $this->char) === false) ? '' : $this->copy_skip($this->token_blank);

            $name = $this->restore_noise($name); // might be a noisy name

            if ($this->lowercase) {
                $name = strtolower($name);
            }

            if ($this->char === '=') { // Attribute with value
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                $this->parse_attr($node, $name, $space, $trim); // get attribute value
            } else { // Attribute without value
                $node->_[HtmlNode::HDOM_INFO_QUOTE][$name] = HtmlNode::HDOM_QUOTE_NO;
                $node->attr[$name] = true;
                if ($this->char !== '>') {
                    $this->char = $this->doc[--$this->pos];
                } // prev
            }

            // Space before attribute and around equal sign
            if (! $trim && $space !== [' ', '', '']) {
                // phpcs:ignore Generic.Files.LineLength
                Debug::log_once('Source document contains superfluous whitespace in attributes (<e    attribute  =  "value">). Enable trimming or fix attribute spacing for best performance.');
                $node->_[HtmlNode::HDOM_INFO_SPACE][$name] = $space;
            }

            // prepare for next attribute
            $space = [
                ((strpos($this->token_blank, $this->char) === false) ? '' : $this->copy_skip($this->token_blank)),
                '',
                ''
            ];
        } while ($this->char !== '>' && $this->char !== '/');

        $this->link_nodes($node, true);

        // Space after last attribute before closing the tag
        if (! $trim && $space[0] !== '') {
            // phpcs:ignore Generic.Files.LineLength
            Debug::log_once('Source document contains superfluous whitespace before the closing bracket (<e attribute="value"     >). Enable trimming or remove spaces before closing brackets for best performance.');
            $node->_[HtmlNode::HDOM_INFO_ENDSPACE] = $space[0];
        }

        $rest = ($this->char === '>') ? '' : $this->copy_until_char('>');
        $rest = ($trim) ? trim($rest) : $rest; // <html   /   >

        $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

        if (trim($rest) === '/') { // Void element
            if ($rest !== '') {
                if (isset($node->_[HtmlNode::HDOM_INFO_ENDSPACE])) {
                    $node->_[HtmlNode::HDOM_INFO_ENDSPACE] .= $rest;
                } else {
                    $node->_[HtmlNode::HDOM_INFO_ENDSPACE] = $rest;
                }
            }
            $node->_[HtmlNode::HDOM_INFO_END] = 0;
        } elseif (! in_array(strtolower($node->tag), self::SELF_CLOSING_TAGS, true)) {
            $innertext = $this->copy_until_char('<');
            if ($innertext !== '') {
                $node->_[HtmlNode::HDOM_INFO_INNER] = $innertext;
            }

            $this->parent = $node;
        }

        if ($node->tag === 'br') {
            $node->_[HtmlNode::HDOM_INFO_INNER] = $this->default_br_text;
        } elseif ($node->tag === 'script') {
            $data = '';

            // There is a rare chance of empty script: "<script></script>"
            // In which case the current char is the start of the end tag
            // But the script could also just contain tags: "<script><div></script>"
            while (true) {
                // Copy until first char of end tag
                $data .= $this->copy_until_char('<');

                // Look ahead in the document, maybe we are at the end
                if (($this->pos + 9) > $this->size) { // End of document
                    Debug::log('Source document ended unexpectedly!');
                    break;
                }

                if (substr($this->doc, $this->pos, 8) === '</script') { // end
                    $this->skip('>'); // don't include the end tag
                    break;
                }

                // Note: A script tag may contain any other tag except </script>
                // which needs to be escaped as <\/script>

                $data .= $this->char;
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
            }

            $node = new HtmlNode($this);
            ++$this->cursor;
            $node->_[HtmlNode::HDOM_INFO_TEXT] = $data;
            $this->link_nodes($node, false);
        }

        return true;
    }

    /**
     * @param HtmlNode $node
     * @param string $name
     * @param array $space
     * @param bool $trim
     */
    protected function parse_attr(HtmlNode $node, $name, array &$space, $trim)
    {
        $is_duplicate = isset($node->attr[$name]);

        if (! $is_duplicate) // Copy whitespace between "=" and value
        {
            $space[2] = (strpos($this->token_blank, $this->char) === false) ? '' : $this->copy_skip($this->token_blank);
        }

        switch ($this->char) {
            case '"':
                $quote_type = HtmlNode::HDOM_QUOTE_DOUBLE;
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                $value = $this->copy_until_char('"');
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                break;

            case '\'':
                // phpcs:ignore Generic.Files.LineLength
                Debug::log_once('Source document contains attribute values with single quotes (<e attribute=\'value\'>). Use double quotes for best performance.');
                $quote_type = HtmlNode::HDOM_QUOTE_SINGLE;
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                $value = $this->copy_until_char('\'');
                $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
                break;

            default:
                // phpcs:ignore Generic.Files.LineLength
                Debug::log_once('Source document contains attribute values without quotes (<e attribute=value>). Use double quotes for best performance');
                $quote_type = HtmlNode::HDOM_QUOTE_NO;
                $value = $this->copy_until($this->token_attr);
        }

        $value = $this->restore_noise($value);

        if ($trim) {
            // Attribute values must not contain control characters other than space
            // https://www.w3.org/TR/html/dom.html#text-content
            // https://www.w3.org/TR/html/syntax.html#attribute-values
            // https://www.w3.org/TR/xml/#AVNormalize
            $value = preg_replace("/[\r\n\t\s]+/u", ' ', $value);
            $value = trim($value);
        }

        if (! $is_duplicate) {
            if ($quote_type !== HtmlNode::HDOM_QUOTE_DOUBLE) {
                $node->_[HtmlNode::HDOM_INFO_QUOTE][$name] = $quote_type;
            }

            $node->attr[$name] = $value;
        }
    }

    /**
     * @param HtmlNode $node
     * @param bool $is_child
     */
    protected function link_nodes(HtmlNode $node, $is_child)
    {
        $node->parent = $this->parent;
        $this->parent->nodes[] = $node;
        if ($is_child) {
            $this->parent->children[] = $node;
        }
    }

    /**
     * @param string $tag
     * @return true
     */
    protected function as_text_node($tag)
    {
        $node = new HtmlNode($this);
        ++$this->cursor;
        $node->_[HtmlNode::HDOM_INFO_TEXT] = '</' . $tag . '>';
        $this->link_nodes($node, false);
        $this->char = (++$this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

        return true;
    }

    /**
     * @param string $chars
     */
    protected function skip($chars)
    {
        $this->pos += strspn($this->doc, $chars, $this->pos);
        $this->char = ($this->pos < $this->size) ? $this->doc[$this->pos] : null; // next
    }

    /**
     * @param string $chars
     * @return string
     */
    protected function copy_skip($chars)
    {
        $pos = $this->pos;
        $len = strspn($this->doc, $chars, $pos);
        if ($len === 0) {
            return '';
        }

        $this->pos += $len;
        $this->char = ($this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

        return substr($this->doc, $pos, $len);
    }

    /**
     * @param string $chars
     * @return string
     */
    protected function copy_until($chars)
    {
        $pos = $this->pos;
        $len = strcspn($this->doc, $chars, $pos);
        $this->pos += $len;
        $this->char = ($this->pos < $this->size) ? $this->doc[$this->pos] : null; // next

        return substr($this->doc, $pos, $len);
    }

    /**
     * @param string $char
     * @return string
     */
    protected function copy_until_char($char)
    {
        if ($this->char === null) {
            return '';
        }

        $pos = strpos($this->doc, $char, $this->pos);
        if ($pos === false) {
            $ret = substr($this->doc, $this->pos, $this->size - $this->pos);
            $this->char = null;
            $this->pos = $this->size;

            return $ret;
        }

        if ($pos === $this->pos) {
            return '';
        }

        $pos_old = $this->pos;
        $this->char = $this->doc[$pos];
        $this->pos = $pos;

        return substr($this->doc, $pos_old, $pos - $pos_old);
    }

    /**
     * @param string $pattern
     * @param bool $remove_tag
     */
    protected function remove_noise($pattern, $remove_tag = false)
    {
        $count = preg_match_all(
            $pattern,
            $this->doc,
            $matches,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE
        );

        for ($i = $count - 1; $i > -1; --$i) {
            $key = '___noise___' . sprintf('% 5d', count($this->noise) + 1000);

            $idx = ($remove_tag) ? 0 : 1; // 0 = entire match, 1 = submatch
            $this->noise[$key] = $matches[$i][$idx][0];
            $this->doc = substr_replace($this->doc, $key, $matches[$i][$idx][1], strlen($matches[$i][$idx][0]));
        }

        // reset the length of content
        $this->size = strlen($this->doc);

        if ($this->size > 0) {
            $this->char = $this->doc[0];
        }
    }

    /**
     * @param string $text
     * @return string
     */
    public function restore_noise($text)
    {
        if (empty($this->noise)) {
            return $text;
        } // nothing to restore

        $pos = 0;
        while (true) {
            $pos = strpos($text, '___noise___', $pos);
            if ($pos === false) {
                break;
            }

            // Sometimes there is a broken piece of markup, and we don't GET the
            // pos+11 etc... token which indicates a problem outside of us...

            // todo: "___noise___1000" (or any number with four or more digits)
            // in the DOM causes an infinite loop which could be utilized by
            // malicious software
            if (strlen($text) > $pos + 15) {
                $key = '___noise___'
                    . $text[$pos + 11]
                    . $text[$pos + 12]
                    . $text[$pos + 13]
                    . $text[$pos + 14]
                    . $text[$pos + 15];

                if (isset($this->noise[$key])) {
                    $text = substr($text, 0, $pos)
                        . $this->noise[$key]
                        . substr($text, $pos + 16);

                    unset($this->noise[$key]);
                } else {
                    Debug::log_once('Noise restoration failed. DOM has been corrupted!');
                    // do this to prevent an infinite loop.
                    // FIXME: THis causes an infinite loop because the keyword ___NOISE___ is included in the key!
                    $text = substr($text, 0, $pos)
                        . 'UNDEFINED NOISE FOR KEY: '
                        . $key
                        . substr($text, $pos + 16);
                }
            } else {
                // There is no valid key being given back to us... We must get
                // rid of the ___noise___ or we will have a problem.
                Debug::log_once('Noise restoration failed. The provided key is incomplete: ' . $text);
                $text = substr($text, 0, $pos)
                    . 'NO NUMERIC NOISE KEY'
                    . substr($text, $pos + 11);
            }
        }

        return $text;
    }

    /**
     * @param string $text
     * @return string
     */
    public function search_noise($text)
    {
        foreach ($this->noise as $noiseElement) {
            if (strpos($noiseElement, $text) !== false) {
                return $noiseElement;
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->root->innertext();
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function __get($name)
    {
        switch ($name) {
            case 'charset':
                return $this->_charset;

            case 'target_charset':
                return $this->_target_charset;

            case 'innertext':
            case 'outertext':
                return $this->root->innertext();

            case 'plaintext':
                return $this->root->text();

            default:
                return null;
        }
    }

    /**
     * @param string $name
     * @param ?string $value
     * @throws Exception
     */
    /** WITH THIS NOT PASS TESTS !!!
    public function __set($name, $value)
    {
    switch ($name) {
    case 'charset':
    $this->_charset = $value;
    break;

    case 'target_charset':
    $this->_target_charset = $value;
    break;

            default:
                $this->root->{$name} = $value;
        }
    }
    */

    /**
     * @param string $name
     * @return bool
     * @throws Exception
     */
    public function __isset($name)
    {
        switch ($name) {
            case 'innertext':
            case 'outertext':
            case 'plaintext':
                return true;

            case 'charset':
                return $this->_charset !== null;

            case 'target_charset':
                return $this->_target_charset !== null;

            default:
                return isset($this->root->{$name});
        }
    }

    /**
     * @param int $idx
     * @return HtmlNode|HtmlNode[]
     */
    public function childNodes($idx = -1)
    {
        return $this->root->childNodes($idx);
    }

    /**
     * @return HtmlNode|null
     */
    public function firstChild()
    {
        return $this->root->firstChild();
    }

    /**
     * @return HtmlNode|null
     */
    public function lastChild()
    {
        return $this->root->lastChild();
    }

    /**
     * @param string $name
     * @param string|null $value
     * @return HtmlNode
     * @noinspection PhpMethodMayBeStaticInspection
     */
    public function createElement($name, $value = null)
    {
        $node = new HtmlNode(null);
        $node->nodetype = HtmlNode::HDOM_TYPE_ELEMENT;
        $node->_[HtmlNode::HDOM_INFO_BEGIN] = 1;
        $node->_[HtmlNode::HDOM_INFO_END] = 1;

        if ($value !== null) {
            $node->_[HtmlNode::HDOM_INFO_INNER] = $value;
        }

        $node->tag = $name;

        return $node;
    }

    /**
     * @param string|null $value
     * @return HtmlNode
     */
    public function createTextNode($value)
    {
        $node = new HtmlNode($this);
        $node->nodetype = HtmlNode::HDOM_TYPE_TEXT;

        if ($value !== null) {
            $node->_[HtmlNode::HDOM_INFO_TEXT] = $value;
        }

        return $node;
    }

    /**
     * @param string $id
     * @return HtmlNode|null
     */
    public function getElementById($id)
    {
        return $this->find("#$id", 0);
    }

    /**
     * @param string $id
     * @param int|null $idx
     * @return HtmlNode|HtmlNode[]|null
     */
    public function getElementsById($id, $idx = null)
    {
        return $this->find("#$id", $idx);
    }

    /**
     * @param string $name
     * @return HtmlNode|null
     */
    public function getElementByTagName($name)
    {
        return $this->find($name, 0);
    }

    /**
     * @param string $name
     * @param int|null $idx
     * @return HtmlNode|HtmlNode[]|null
     */
    public function getElementsByTagName($name, $idx = null)
    {
        return $this->find($name, $idx);
    }

    /**
     * @param string $file
     * @return $this|false
     */
    public function loadFile($file)
    {
        $args = func_get_args();

        $doc = file_get_contents(...$args);

        return $doc !== false ? $this->load($doc) : false;
    }
}
