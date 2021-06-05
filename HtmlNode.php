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

use function array_diff;
use function array_flip;
use function array_intersect_key;
use function array_keys;
use function array_map;
use function array_merge;
use function array_pop;
use function array_search;
use function array_shift;
use function array_slice;
use function array_values;
use function call_user_func;
use function call_user_func_array;
use function count;
use function end;
use function explode;
use function file_put_contents;
use function filter_var;
use function htmlentities;
use function iconv;
use function implode;
use function in_array;
use function is_array;
use function is_numeric;
use function is_string;
use function ksort;
use function ltrim;
use function ord;
use function preg_match;
use function preg_match_all;
use function preg_quote;
use function preg_replace;
use function range;
use function rtrim;
use function str_ireplace;
use function str_repeat;
use function str_replace;
use function strlen;
use function strncmp;
use function strpos;
use function strtolower;
use function strtoupper;
use function substr;
use function trigger_error;
use function trim;

use const E_USER_ERROR;
use const ENT_COMPAT;
use const ENT_QUOTES;
use const FILTER_VALIDATE_INT;
use const LOCK_EX;
use const PREG_SET_ORDER;

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/Debug.php';

/**
 * HTML Node.
 *
 * @method bool has_child()
 * @method self|self[]|null children(int $idx = -1)
 * @method HtmlNode|null first_child()
 * @method HtmlNode|null last_child()
 * @method HtmlNode|null next_sibling()
 * @method HtmlNode|null prev_sibling()
 *
 * @property string $outertext
 * @property string $innertext
 * @property-read string $plaintext
 * @property-read string $xmltext
 */
class HtmlNode
{
    /** @var int */
    const HDOM_TYPE_ELEMENT = 1;

    /** @var int */
    const HDOM_TYPE_COMMENT = 2;

    /** @var int */
    const HDOM_TYPE_TEXT = 3;

    /** @var int */
    const HDOM_TYPE_ROOT = 5;

    /** @var int */
    const HDOM_TYPE_UNKNOWN = 6;

    /** @var int */
    const HDOM_TYPE_CDATA = 7;

    /** @var int */
    const HDOM_QUOTE_DOUBLE = 0;

    /** @var int */
    const HDOM_QUOTE_SINGLE = 1;

    /** @var int */
    const HDOM_QUOTE_NO = 3;

    /** @var int */
    const HDOM_INFO_BEGIN = 0;

    /** @var int */
    const HDOM_INFO_END = 1;

    /** @var int */
    const HDOM_INFO_QUOTE = 2;

    /** @var int */
    const HDOM_INFO_SPACE = 3;

    /** @var int */
    const HDOM_INFO_TEXT = 4;

    /** @var int */
    const HDOM_INFO_INNER = 5;

    /** @var int */
    const HDOM_INFO_OUTER = 6;

    /** @var int */
    const HDOM_INFO_ENDSPACE = 7;

    /** @var string[] */
    const BLOCK_ELEMENTS = [
        'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ol', 'ul', 'pre', 'address', 'blockquote', 'dl', 'div', 'fieldset',
        'form', 'hr', 'noscript', 'table'
    ];

    /** @var string[] */
    const INLINE_ELEMENTS = [
        'b', 'big', 'i', 'small', 'tt', 'abbr', 'acronym', 'cite', 'code', 'dfn', 'em', 'kbd', 'strong', 'samp', 'var',
        'a', 'bdo', 'br', 'img', 'map', 'object', 'q', 'script', 'span', 'sub', 'sup', 'button', 'input', 'label',
        'select', 'textarea'
    ];

    /** @var int */
    public $nodetype = self::HDOM_TYPE_TEXT;

    /** @var string */
    public $tag = 'text';

    /** @var array */
    public $attr = [];

    /** @var self[] */
    public $children = [];

    /** @var self[] */
    public $nodes = [];

    /** @var ?self */
    public $parent;

    /** @var array */
    public $_ = [];

    /** @var ?HtmlDocument */
    private $dom;

    /**
     * @param string $func
     * @param array $args
     * @return mixed
     */
    public function __call($func, array $args)
    {
        // Allow users to call methods with lower_case syntax
        switch ($func) {
            case 'has_child':
                $actual_function = 'hasChildNodes';
                break;

            case 'children':
                $actual_function = 'childNodes';
                break;

            case 'first_child':
                $actual_function = 'firstChild';
                break;

            case 'last_child':
                $actual_function = 'lastChild';
                break;

            case 'next_sibling':
                $actual_function = 'nextSibling';
                break;

            case 'prev_sibling':
                $actual_function = 'previousSibling';
                break;

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
     * HtmlNode constructor.
     *
     * @param HtmlDocument|null $dom
     */
    public function __construct(HtmlDocument $dom = null)
    {
        if ($dom !== null) {
            $this->dom = $dom;
            $dom->nodes[] = $this;
        }
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        // Translate node type to human-readable form
        switch ($this->nodetype) {
            case self::HDOM_TYPE_ELEMENT:
                $nodetype = "HDOM_TYPE_ELEMENT ($this->nodetype)";
                break;

            case self::HDOM_TYPE_COMMENT:
                $nodetype = "HDOM_TYPE_COMMENT ($this->nodetype)";
                break;

            case self::HDOM_TYPE_TEXT:
                $nodetype = "HDOM_TYPE_TEXT ($this->nodetype)";
                break;

            case self::HDOM_TYPE_ROOT:
                $nodetype = "HDOM_TYPE_ROOT ($this->nodetype)";
                break;

            case self::HDOM_TYPE_CDATA:
                $nodetype = "HDOM_TYPE_CDATA ($this->nodetype)";
                break;

            default:
                $nodetype = "HDOM_TYPE_UNKNOWN ($this->nodetype)";
        }

        return [
            'nodetype' => $nodetype,
            'tag' => $this->tag,
            'attributes' => empty($this->attr) ? 'none' : $this->attr,
            'nodes' => empty($this->nodes) ? 'none' : $this->nodes
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->outertext();
    }

    /**
     * Clear
     */
    public function clear()
    {
        // Break link to origin & branch
        unset($this->dom, $this->parent);
    }

    /**
     * @param bool $show_attr
     * @param int $depth
     * @codeCoverageIgnore
     */
    public function dump($show_attr = true, $depth = 0)
    {
        echo str_repeat("\t", $depth) . $this->tag;

        if ($show_attr && ! empty($this->attr)) {
            echo '(';
            foreach ($this->attr as $k => $v) {
                echo "[$k]=>\"$v\", ";
            }

            echo ')';
        }

        echo "\n";

        if ($this->nodes) {
            foreach ($this->nodes as $node) {
                $node->dump($show_attr, $depth + 1);
            }
        }
    }

    /**
     * @param bool $echo
     * @return string
     * @codeCoverageIgnore
     */
    public function dump_node($echo = true)
    {
        $string = $this->tag;

        if (! empty($this->attr)) {
            $string .= '(';
            foreach ($this->attr as $k => $v) {
                $string .= "[$k]=>\"$v\", ";
            }

            $string .= ')';
        }

        if (! empty($this->_)) {
            $string .= ' $_ (';
            foreach ($this->_ as $k => $v) {
                if (is_array($v)) {
                    $string .= "[$k]=>(";
                    foreach ($v as $k2 => $v2) {
                        $string .= "[$k2]=>\"$v2\", ";
                    }

                    $string .= ')';
                } else {
                    $string .= "[$k]=>\"$v\", ";
                }
            }

            $string .= ')';
        }

        if (isset($this->text)) {
            $string .= ' text: (' . $this->text . ')';
        }

        $string .= ' HDOM_INNER_INFO: ';
        $string .= isset($node->_[self::HDOM_INFO_INNER]) ? "'" . $node->_[self::HDOM_INFO_INNER] . "'" : ' NULL ';
        $string .= ' children: ' . count($this->children);
        $string .= ' nodes: ' . count($this->nodes);
        $string .= "\n";

        if ($echo) {
            echo $string;
        }

        return $string;
    }

    /**
     * @param ?self $parent
     * @return ?self
     */
    public function parent(self $parent = null)
    {
        // I am SURE that this doesn't work properly.
        // It fails to unset the current node from it's current parents nodes or
        // children list first.
        if ($parent !== null) {
            $this->parent = $parent;
            $this->parent->nodes[] = $this;
            $this->parent->children[] = $this;
        }

        return $this->parent;
    }

    /**
     * @param string $tag
     * @return ?self
     */
    public function find_ancestor_tag($tag)
    {
        if ($this->parent === null) {
            return null;
        }

        $ancestor = $this->parent;

        while ($ancestor !== null) {
            if ($ancestor->tag === $tag) {
                break;
            }

            $ancestor = $ancestor->parent;
        }

        return $ancestor;
    }

    /**
     * @return string
     */
    public function innertext()
    {
        if (isset($this->_[self::HDOM_INFO_INNER])) {
            $ret = $this->_[self::HDOM_INFO_INNER];
        } elseif (isset($this->_[self::HDOM_INFO_TEXT])) {
            $ret = $this->_[self::HDOM_INFO_TEXT];
        } else {
            $ret = '';
        }

        foreach ($this->nodes as $n) {
            $ret .= $n->outertext();
        }

        return $this->convert_text($ret);
    }

    /**
     * @return string
     */
    public function outertext()
    {
        if ($this->tag === 'root') {
            return $this->innertext();
        }

        // todo: What is the use of this callback? Remove?
        if ($this->dom && $this->dom->callback !== null) {
            call_user_func($this->dom->callback, $this);
        }

        if (isset($this->_[self::HDOM_INFO_OUTER])) {
            return $this->convert_text($this->_[self::HDOM_INFO_OUTER]);
        }

        if (isset($this->_[self::HDOM_INFO_TEXT])) {
            return $this->convert_text($this->_[self::HDOM_INFO_TEXT]);
        }

        $ret = '';

        if (isset($this->_[self::HDOM_INFO_BEGIN])) {
            $ret = $this->makeup();
        }

        // todo: <br> should either never have self::HDOM_INFO_INNER or always
        if (isset($this->_[self::HDOM_INFO_INNER]) && $this->tag !== 'br') {
            $ret .= $this->_[self::HDOM_INFO_INNER];
        }

        if ($this->nodes) {
            foreach ($this->nodes as $n) {
                $ret .= $n->outertext();
            }
        }

        if (isset($this->_[self::HDOM_INFO_END]) && $this->_[self::HDOM_INFO_END] !== 0) {
            $ret .= '</' . $this->tag . '>';
        }

        return $this->convert_text($ret);
    }

    /**
     * Returns true if the provided element is a block level element
     *
     * @link https://www.w3resource.com/html/HTML-block-level-and-inline-elements.php
     * @param self $node
     * @return bool
     */
    protected function is_block_element(self $node)
    {
        // todo: When we have the utility class this should be moved there
        return in_array(strtolower($node->tag), self::BLOCK_ELEMENTS);
    }

    /**
     * Returns true if the provided element is an inline level element
     *
     * @link https://www.w3resource.com/html/HTML-block-level-and-inline-elements.php
     * @param self $node
     * @return bool
     */
    protected function is_inline_element(self $node)
    {
        // todo: When we have the utility class this should be moved there
        return in_array(strtolower($node->tag), self::INLINE_ELEMENTS);
    }

    /**
     * @param bool $trim
     * @return string
     */
    public function text($trim = true)
    {
        $ret = '';

        if (in_array($this->nodetype, [self::HDOM_TYPE_COMMENT, self::HDOM_TYPE_UNKNOWN]) ||
            in_array(strtolower($this->tag), ['script', 'style'])) {
            $ret = '';
        } elseif ($this->nodetype === self::HDOM_TYPE_CDATA || isset($this->_[self::HDOM_INFO_INNER])) {
            $ret = $this->_[self::HDOM_INFO_INNER];
        } elseif ($this->nodetype === self::HDOM_TYPE_TEXT) {
            $ret = $this->_[self::HDOM_INFO_TEXT];
        }

        if ($this->nodes === null) {
            return '';
        }

        foreach ($this->nodes as $n) {
            if ($this->is_block_element($n)) {
                $block = ltrim($this->convert_text($n->text(false)));

                if (empty($block)) {
                    continue;
                }

                $ret = rtrim($ret) . "\n\n" . $block;
            } elseif ($this->is_inline_element($n)) {
                // todo: <br> introduces code smell because no space but \n
                if (strtolower($n->tag) === 'br') {
                    $ret .= $this->dom->default_br_text ?: DEFAULT_BR_TEXT;
                } else {
                    $inline = ltrim($this->convert_text($n->text(false)));

                    if (empty($inline)) {
                        continue;
                    }

                    $ret .= $this->convert_text($n->text(false));
                }
            } else {
                $ret .= $this->convert_text($n->text(false));
            }
        }

        // Reduce whitespace at start/end to a single (or none) space
        $ret = preg_replace('/[ \t\n\r\0\x0B\xC2\xA0]+$/u', $trim ? '' : ' ', $ret);

        return preg_replace('/^[ \t\n\r\0\x0B\xC2\xA0]+/u', $trim ? '' : ' ', $ret);
    }

    /**
     * @return string
     */
    public function xmltext()
    {
        $ret = $this->innertext();
        $ret = str_ireplace('<![CDATA[', '', $ret);

        return str_replace(']]>', '', $ret);
    }

    /**
     * @return string
     */
    public function makeup()
    {
        // text, comment, unknown
        if (isset($this->_[self::HDOM_INFO_TEXT])) {
            return $this->_[self::HDOM_INFO_TEXT];
        }

        $ret = '<' . $this->tag;

        foreach ($this->attr as $key => $val) {
            // skip removed attribute
            if ($val === null || $val === false) {
                continue;
            }

            $ret .= isset($this->_[self::HDOM_INFO_SPACE][$key]) ? $this->_[self::HDOM_INFO_SPACE][$key][0] : ' ';

            //no value attr: nowrap, checked selected...
            if ($val === true) {
                $ret .= $key;
            } else {
                $quote_type = isset($this->_[self::HDOM_INFO_QUOTE][$key]) ?
                    $this->_[self::HDOM_INFO_QUOTE][$key] : self::HDOM_QUOTE_DOUBLE;

                switch ($quote_type) {
                    case self::HDOM_QUOTE_SINGLE:
                        $quote = '\'';
                        $val = htmlentities($val, ENT_QUOTES, $this->dom->target_charset);
                        break;

                    case self::HDOM_QUOTE_NO:
                        $quote = '';
                        break;

                    default:
                        $quote = '"';
                        $val = htmlentities($val, ENT_COMPAT, $this->dom->target_charset);
                }

                $ret .= $key
                    . (isset($this->_[self::HDOM_INFO_SPACE][$key]) ? $this->_[self::HDOM_INFO_SPACE][$key][1] : '')
                    . '='
                    . (isset($this->_[self::HDOM_INFO_SPACE][$key]) ? $this->_[self::HDOM_INFO_SPACE][$key][2] : '')
                    . $quote
                    . $val
                    . $quote;
            }
        }

        if (isset($this->_[self::HDOM_INFO_ENDSPACE])) {
            $ret .= $this->_[self::HDOM_INFO_ENDSPACE];
        }

        return $ret . '>';
    }

    /**
     * @param string $selector
     * @param ?int $idx
     * @param bool $lowercase
     * @return self|self[]|null
     */
    public function find($selector, $idx = null, $lowercase = false)
    {
        $selectors = $this->parse_selector($selector);
        if (empty($selectors)) {
            return [];
        }

        $found_keys = [];

        // find each selector
        foreach ($selectors as $selectorPart) {
            // The change on the below line was documented on the sourceforge
            // code tracker id 2788009
            // used to be: if (($level=count($selectors[0]))===0) return array();
            if (empty($selectorPart)) {
                Debug::log_once('Empty selector (' . $selector . ') matches nothing.');

                return [];
            }

            if (! isset($this->_[self::HDOM_INFO_BEGIN])) {
                Debug::log_once('Invalid operation. The current node has no start tag.');

                return [];
            }

            $head = [$this->_[self::HDOM_INFO_BEGIN] => 1];
            $cmd = ' '; // Combinator

            // handle descendant selectors, no recursive!
            foreach (array_keys($selectorPart) as $l) {
                $ret = [];

                foreach ($head as $k => $v) {
                    $n = ($k === -1) ? $this->dom->root : $this->dom->nodes[$k];
                    //PaperG - Pass this optional parameter on to the seek function.
                    $n->seek($selectorPart[$l], $ret, $cmd, $lowercase);
                }

                $head = $ret;
                $cmd = $selectorPart[$l][6]; // Next Combinator
            }

            foreach ($head as $k => $v) {
                if (! isset($found_keys[$k])) {
                    $found_keys[$k] = 1;
                }
            }
        }

        // sort keys
        ksort($found_keys);

        $found = [];
        foreach ($found_keys as $k => $v) {
            $found[] = $this->dom->nodes[$k];
        }

        // return nth-element or array
        if ($idx === null) {
            return $found;
        }

        if ($idx < 0) {
            $idx += count($found);
        }

        return (isset($found[$idx])) ? $found[$idx] : null;
    }

    /**
     * @param string $selector
     * @param ?int $idx
     * @param bool $lowercase
     * @return self|self[]|null
     */
    public function expect($selector, $idx = null, $lowercase = false)
    {
        return $this->find($selector, $idx, $lowercase) ?: null;
    }

    /**
     * @param string $selector
     * @param array $ret
     * @param string $parent_cmd
     * @param bool $lowercase
     */
    protected function seek($selector, array &$ret, $parent_cmd, $lowercase = false)
    {
        list($ps_selector, $tag, $ps_element, $id, $class, $attributes) = $selector;
        $nodes = [];

        if ($parent_cmd === ' ') { // Descendant Combinator
            // Find parent closing tag if the current element doesn't have a closing
            // tag (i.e. void element)
            $end = ! empty($this->_[self::HDOM_INFO_END]) ? (int)$this->_[self::HDOM_INFO_END] : 0;
            if ($end === 0 && $this->parent) {
                $parent = $this->parent;
                while ($parent !== null && ! isset($parent->_[self::HDOM_INFO_END])) {
                    --$end;
                    $parent = $parent->parent;
                }

                $end += $parent->_[self::HDOM_INFO_END];
            }

            if ($end === 0) {
                $end = count($this->dom->nodes);
            }

            // Get list of target nodes
            $nodes_start = $this->_[self::HDOM_INFO_BEGIN] + 1;

            // remove() makes $this->dom->nodes non-contiguous; use what is left.
            $nodes = array_intersect_key(
                $this->dom->nodes,
                array_flip(range($nodes_start, $end))
            );
        } elseif ($parent_cmd === '>') { // Child Combinator
            $nodes = $this->children;
        } elseif ($this->parent && in_array($this, $this->parent->children, false)) {
            if ($parent_cmd === '+') { // Next-Sibling Combinator
                $index = array_search($this, $this->parent->children, true) + 1;
                if ($index !== false && $index < count($this->parent->children)) {
                    $nodes[] = $this->parent->children[$index];
                }
            } elseif ($parent_cmd === '~') { // Subsequent Sibling Combinator
                $index = array_search($this, $this->parent->children, true);
                $nodes = array_slice($this->parent->children, $index);
            }
        }

        // Go through each element starting at this element until the end tag
        // Note: If this element is a void tag, any previous void element is
        // skipped.
        foreach ($nodes as $node) {
            $pass = true;

            // Skip root nodes
            if (! $node->parent) {
                unset($node);
                continue;
            }

            // Handle 'text' selector
            if ($pass && $tag === 'text') {
                if ($node->tag === 'text') {
                    $pos = array_search($node, $this->dom->nodes, true);
                    if ($pos !== false) {
                        $ret[$pos] = 1;
                    }
                }

                if (isset($node->_[self::HDOM_INFO_INNER])) {
                    $ret[$node->_[self::HDOM_INFO_BEGIN]] = 1;
                }

                unset($node);
                continue;
            }

            // Handle 'cdata' selector
            if ($pass && $tag === 'cdata') {
                if ($node->tag === 'cdata') {
                    $ret[$node->_[self::HDOM_INFO_BEGIN]] = 1;
                }

                unset($node);
                continue;
            }

            // Handle 'comment'
            if ($pass && $tag === 'comment' && $node->tag === 'comment') {
                $ret[$node->_[self::HDOM_INFO_BEGIN]] = 1;
                unset($node);
                continue;
            }

            // Skip if node isn't a child node (i.e. text nodes)
            if ($pass && ! in_array($node, $node->parent->children, true)) {
                unset($node);
                continue;
            }

            // Skip if tag doesn't match
            if ($pass && $tag !== '' && $tag !== $node->tag && $tag !== '*') {
                $pass = false;
            }

            // Skip if ID doesn't exist
            if ($pass && $id !== '' && ! isset($node->attr['id'])) {
                $pass = false;
            }

            // Check if ID matches
            if ($pass && $id !== '' && isset($node->attr['id'])) {
                // Note: Only consider the first ID (as browsers do)
                $node_id = explode(' ', trim($node->attr['id']))[0];

                if ($id !== $node_id) {
                    $pass = false;
                }
            }

            // Check if all class(es) exist
            if ($pass && $class !== '' && is_array($class) && ! empty($class)) {
                if (isset($node->attr['class'])) {
                    // Apply the same rules for the pattern and attribute value
                    // Attribute values must not contain control characters other than space
                    // https://www.w3.org/TR/html/dom.html#text-content
                    // https://www.w3.org/TR/html/syntax.html#attribute-values
                    // https://www.w3.org/TR/xml/#AVNormalize
                    $node_classes = preg_replace("/[\r\n\t\s]+/u", ' ', $node->attr['class']);
                    $node_classes = trim($node_classes);
                    $node_classes = explode(' ', $node_classes);

                    if ($lowercase) {
                        $node_classes = array_map('strtolower', $node_classes);
                    }

                    foreach ($class as $c) {
                        if (! in_array($c, $node_classes, false)) {
                            $pass = false;
                            break;
                        }
                    }
                } else {
                    $pass = false;
                }
            }

            // Check attributes
            if ($pass && $attributes !== '' && is_array($attributes) && ! empty($attributes)) {
                foreach ($attributes as list ($att_name, $att_expr, $att_val, $att_inv, $att_case_sensitivity)) {
                    // Handle indexing attributes (i.e. "[2]")
                    /**
                     * Note: This is not supported by the CSS Standard but adds
                     * the ability to select items compatible to XPath (i.e.
                     * the 3rd element within it's parent).
                     *
                     * Note: This doesn't conflict with the CSS Standard which
                     * doesn't work on numeric attributes anyway.
                     */
                    if (is_numeric($att_name) && $att_expr === '' && $att_val === '') {
                        $count = 0;

                        // Find index of current element in parent
                        foreach ($node->parent->children as $c) {
                            if ($c->tag === $node->tag) {
                                ++$count;
                            }

                            if ($c === $node) {
                                break;
                            }
                        }

                        // If this is the correct node, continue with next
                        // attribute
                        if ($count === (int)$att_name) {
                            continue;
                        }
                    }

                    // Check attribute availability Attribute should NOT be set
                    if (($att_inv && isset($node->attr[$att_name])) ||
                        ($att_name !== 'plaintext' && ! isset($node->attr[$att_name]))) {
                        $pass = false;
                        break;
                    }

                    // Continue with next attribute if expression isn't defined
                    if ($att_expr === '') {
                        continue;
                    }

                    // If they have told us that this is a "plaintext"
                    // search then we want the plaintext of the node - right?
                    // todo "plaintext" is not a valid CSS selector!
                    $nodeKeyValue = $att_name === 'plaintext' ? $node->text() : $node->attr[$att_name];

                    // If lowercase is set, do a case insensitive test of
                    // the value of the selector.
                    $check = $lowercase ? $this->match(
                        $att_expr,
                        strtolower($att_val),
                        strtolower($nodeKeyValue),
                        $att_case_sensitivity
                    ) : $this->match(
                        $att_expr,
                        $att_val,
                        $nodeKeyValue,
                        $att_case_sensitivity
                    );

                    $check = $ps_element === 'not' ? ! $check : $check;
                    if (! $check) {
                        $pass = false;
                        break;
                    }
                }
            }

            // Found a match. Add to list and clear node
            $pass = $ps_selector === 'not' ? ! $pass : $pass;
            if ($pass) {
                $ret[$node->_[self::HDOM_INFO_BEGIN]] = 1;
            }

            unset($node);
        }
    }

    /**
     * @param string $exp
     * @param string $pattern
     * @param string $value
     * @param bool $case_sensitivity
     * @return bool
     */
    protected function match($exp, $pattern, $value, $case_sensitivity)
    {
        if ($case_sensitivity === 'i') {
            $pattern = strtolower($pattern);
            $value = strtolower($value);
        }

        // Apply the same rules for the pattern and attribute value
        // Attribute values must not contain control characters other than space
        // https://www.w3.org/TR/html/dom.html#text-content
        // https://www.w3.org/TR/html/syntax.html#attribute-values
        // https://www.w3.org/TR/xml/#AVNormalize
        $pattern = preg_replace("/[\r\n\t\s]+/u", ' ', $pattern);
        $pattern = trim($pattern);

        $value = preg_replace("/[\r\n\t\s]+/u", ' ', $value);
        $value = trim($value);

        switch ($exp) {
            case '=':
                return ($value === $pattern);

            case '!=':
                return ($value !== $pattern);

            case '^=':
                return (bool)preg_match('/^' . preg_quote($pattern, '/') . '/', $value);

            case '$=':
                return (bool)preg_match('/' . preg_quote($pattern, '/') . '$/', $value);

            case '*=':
                return (bool)preg_match('/' . preg_quote($pattern, '/') . '/', $value);

            case '|=':
                /**
                 * [att|=val]
                 *
                 * Represents an element with the att attribute, its value
                 * either being exactly "val" or beginning with "val"
                 * immediately followed by "-" (U+002D).
                 */
                return strpos($value, $pattern) === 0;

            case '~=':
                /**
                 * [att~=val]
                 *
                 * Represents an element with the att attribute whose value is a
                 * whitespace-separated list of words, one of which is exactly
                 * "val". If "val" contains whitespace, it will never represent
                 * anything (since the words are separated by spaces). Also if
                 * "val" is the empty string, it will never represent anything.
                 */
                return in_array($pattern, explode(' ', trim($value)), true);

            default:
                Debug::log('Unhandled attribute selector: ' . $exp . '!');

                return false;
        }
    }

    /**
     * @param string $selector_string
     * @return array
     */
    protected function parse_selector($selector_string)
    {
        /**
         * Pattern of CSS selectors, modified from mootools (https://mootools.net/)
         *
         * Paperg: Add the colon to the attribute, so that it properly finds
         * <tag attribute="something" > like google does.
         *
         * Note: if you try to look at this attribute, you MUST use getAttribute
         * since $dom->x:y will fail the php syntax check.
         *
         * Notice the \[ starting the attribute? and the @? following? This
         * implies that an attribute can begin with an @ sign that is not
         * captured. This implies that an html attribute specifier may start
         * with an @ sign that is NOT captured by the expression. Farther study
         * is required to determine of this should be documented or removed.
         *
         * Matches selectors in this order:
         *
         * [0] - full match
         *
         * [1] - pseudo selector
         *     (?:\:(\w+)\()?
         *     Matches the pseudo selector (optional)
         *
         * [2] - tag name
         *     ([\w:\*-]*)
         *     Matches the tag name consisting of zero or more words, colons,
         *     asterisks and hyphens.
         *
         * [3] - pseudo selector
         *     (?:\:(\w+)\()?
         *     Matches the pseudo selector (optional)
         *
         * [4] - id name
         *     (?:\#([\w-]+))
         *     Optionally matches a id name, consisting of an "#" followed by
         *     the id name (one or more words and hyphens).
         *
         * [5] - class names (including dots)
         *     (?:\.([\w\.-]+))?
         *     Optionally matches a list of class, consisting of an "."
         *     followed by the class name (one or more words and hyphens)
         *     where multiple classes can be chained (i.e. ".foo.bar.baz")
         *
         * [6] - attributes
         *     ((?:\[@?(?:!?[\w:-]+)(?:(?:[!*^$|~]?=)[\"']?(?:.*?)[\"']?)?(?:\s*?(?:[iIsS])?)?\])+)?
         *     Optionally matches the attributes list
         *
         * [7] - separator
         *     ([\/, >+~]+)
         *     Matches the selector list separator
         */
        // phpcs:ignore Generic.Files.LineLength
        $pattern =
            "/(?:\:(\w+)\()?([\w:\*-]*)(?:\:(\w+)\()?(?:\#([\w-]+))?(?:|\.([\w\.-]+))?((?:\[@?(?:!?[\w:-]+)(?:(?:[!*^$|~]?=)[\"']?(?:.*?)[\"']?)?(?:\s*?(?:[iIsS])?)?\])+)?(?:\))?(?:\))?([\/, >+~]+)/is";

        preg_match_all(
            $pattern,
            trim($selector_string) . ' ', // Add final ' ' as pseudo separator
            $matches,
            PREG_SET_ORDER
        );

        $selectors = [];
        $result = [];

        foreach ($matches as $m) {
            $m[0] = trim($m[0]);

            // Skip NoOps
            if ($m[0] === '' || $m[0] === '/' || $m[0] === '//') {
                continue;
            }

            array_shift($m);

            // Convert to lowercase
            if ($this->dom->lowercase) {
                $m[1] = strtolower($m[1]);
            }

            // Extract classes
            if ($m[4] !== '') {
                $m[4] = explode('.', $m[4]);
            }

            /* Extract attributes (pattern based on the pattern above!)

             * [0] - full match
             * [1] - attribute name
             * [2] - attribute expression
             * [3] - attribute value
             * [4] - case sensitivity
             *
             * Note: Attributes can be negated with a "!" prefix to their name
             */
            if ($m[5] !== '') {
                preg_match_all(
                    "/\[@?(!?[\w:-]+)(?:([!*^$|~]?=)[\"']?(.*?)[\"']?)?(?:\s+?([iIsS])?)?\]/is",
                    trim($m[5]),
                    $attributes,
                    PREG_SET_ORDER
                );

                // Replace element by array
                $m[5] = [];

                foreach ($attributes as $att) {
                    // Skip empty matches
                    if (trim($att[0]) === '') {
                        continue;
                    }

                    $inverted = (isset($att[1][0]) && $att[1][0] === '!');
                    $m[5][] = [
                        $inverted ? substr($att[1], 1) : $att[1], // Name
                        (isset($att[2])) ? $att[2] : '', // Expression
                        (isset($att[3])) ? $att[3] : '', // Value
                        $inverted, // Inverted Flag
                        (isset($att[4])) ? strtolower($att[4]) : '', // Case-Sensitivity
                    ];
                }
            }

            // Sanitize Separator
            $m[6] = $m[6] !== '' && trim($m[6]) === '' ? ' ' : trim($m[6]);

            // Clear Separator if it's a Selector List
            $is_list = ($m[6] === ',');
            if ($is_list) {
                $m[6] = '';
            }

            $result[] = $m;

            if ($is_list) { // Selector List
                $selectors[] = $result;
                $result = [];
            }
        }

        if (! empty($result)) {
            $selectors[] = $result;
        }

        return $selectors;
    }

    /**
     * @param string $name
     * @return false|string
     */
    public function __get($name)
    {
        if (isset($this->attr[$name])) {
            return $this->convert_text($this->attr[$name]);
        }

        switch ($name) {
            case 'outertext':
                return $this->outertext();
            case 'innertext':
                return $this->innertext();
            case 'plaintext':
                return $this->text();
            case 'xmltext':
                return $this->xmltext();
            default:
                return false;
        }
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'outertext':
                $this->_[self::HDOM_INFO_OUTER] = $value;
                break;

            case 'innertext':
                if (isset($this->_[self::HDOM_INFO_TEXT])) {
                    $this->_[self::HDOM_INFO_TEXT] = '';
                }

                $this->_[self::HDOM_INFO_INNER] = $value;
                break;

            default:
                $this->attr[$name] = $value;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        switch ($name) {
            case 'innertext':
            case 'plaintext':
            case 'outertext':
            case 'xmltext':
                return true;

            default:
                return isset($this->attr[$name]);
        }
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->attr[$name]);
    }

    /**
     * @param string $text
     * @return string
     */
    public function convert_text($text)
    {
        $converted_text = $text;

        $sourceCharset = '';
        $targetCharset = '';

        if ($this->dom) {
            $sourceCharset = strtoupper($this->dom->_charset);
            $targetCharset = strtoupper($this->dom->_target_charset);
        }

        if (! empty($sourceCharset) && ! empty($targetCharset)) {
            if (strtoupper($sourceCharset) === strtoupper($targetCharset)) {
                $converted_text = $text;
            } elseif ((strtoupper($targetCharset) === 'UTF-8') && (self::is_utf8($text))) {
                Debug::log_once('The source charset was incorrectly detected as ' . $sourceCharset .
                    ' but should have been UTF-8');
                $converted_text = $text;
            } else {
                $converted_text = iconv($sourceCharset, $targetCharset, $text);
            }
        }

        // Lets make sure that we don't have that silly BOM issue with any of the utf-8 text we output.
        if ($targetCharset === 'UTF-8') {
            if (strncmp($converted_text, "\xef\xbb\xbf", 3) === 0) {
                $converted_text = substr($converted_text, 3);
            }

            if (substr($converted_text, -3) === "\xef\xbb\xbf") {
                $converted_text = substr($converted_text, 0, -3);
            }
        }

        return $converted_text;
    }

    /**
     * Check if string is in UTF-8
     *
     * @param $str
     * @return bool
     */
    public static function is_utf8($str)
    {
        $len = strlen($str);

        for ($i = 0; $i < $len; $i++) {
            $c = ord($str[$i]);
            if ($c > 128) {
                if (($c >= 254)) {
                    return false;
                }

                if ($c >= 252) {
                    $bits = 6;
                } elseif ($c >= 248) {
                    $bits = 5;
                } elseif ($c >= 240) {
                    $bits = 4;
                } elseif ($c >= 224) {
                    $bits = 3;
                } elseif ($c >= 192) {
                    $bits = 2;
                } else {
                    return false;
                }

                if (($i + $bits) > $len) {
                    return false;
                }

                while ($bits > 1) {
                    $i++;
                    $b = ord($str[$i]);
                    if ($b < 128 || $b > 191) {
                        return false;
                    }

                    $bits--;
                }
            }
        }

        return true;
    }

    /**
     * @return array|false
     */
    public function get_display_size()
    {
        $width = -1;
        $height = -1;

        if ($this->tag !== 'img') {
            return false;
        }

        // See if there is a height or width attribute in the tag itself.
        if (isset($this->attr['width'])) {
            $width = $this->attr['width'];
        }

        if (isset($this->attr['height'])) {
            $height = $this->attr['height'];
        }

        // Now look for an inline style.
        if (isset($this->attr['style'])) {
            // Thanks to user "gnarf" from stackoverflow for this regular expression.
            $attributes = [];

            preg_match_all(
                '/([\w-]+)\s*:\s*([^;]+)\s*;?/',
                $this->attr['style'],
                $matches,
                PREG_SET_ORDER
            );

            foreach ($matches as $match) {
                $attributes[$match[1]] = $match[2];
            }

            // If there is a width in the style attributes:
            // check that the last two characters are px (pixels)
            if (isset($attributes['width']) && $width === -1 &&
                strtolower(substr($attributes['width'], -2)) === 'px') {
                $proposed_width = substr($attributes['width'], 0, -2);
                // Now make sure that it's an integer and not something stupid.
                if (filter_var($proposed_width, FILTER_VALIDATE_INT)) {
                    $width = $proposed_width;
                }
            }

            // If there is a width in the style attributes:
            // check that the last two characters are px (pixels)
            if (isset($attributes['height']) && $height === -1 &&
                strtolower(substr($attributes['height'], -2)) === 'px') {
                $proposed_height = substr($attributes['height'], 0, -2);
                // Now make sure that it's an integer and not something stupid.
                if (filter_var($proposed_height, FILTER_VALIDATE_INT)) {
                    $height = $proposed_height;
                }
            }
        }

        // Future enhancement:
        // Look in the tag to see if there is a class or id specified that has
        // a height or width attribute to it.

        // Far future enhancement
        // Look at all the parent tags of this image to see if they specify a
        // class or id that has an img selector that specifies a height or width
        // Note that in this case, the class or id will have the img subselector
        // for it to apply to the image.

        // ridiculously far future development
        // If the class or id is specified in a SEPARATE css file that's not on
        // the page, go get it and do what we were just doing for the ones on
        // the page.

        return [
            'height' => $height,
            'width' => $width
        ];
    }

    /**
     * @param string $filepath
     * @return string
     */
    public function save($filepath = '')
    {
        $ret = $this->outertext();

        if ($filepath !== '') {
            file_put_contents($filepath, $ret, LOCK_EX);
        }

        return $ret;
    }

    /**
     * @param string|string[] $class
     */
    public function addClass($class)
    {
        if (is_string($class)) {
            $class = explode(' ', $class);
        }

        if (is_array($class)) {
            foreach ($class as $c) {
                if (isset($this->class)) {
                    if ($this->hasClass($c)) {
                        continue;
                    }

                    $this->class .= ' ' . $c;
                } else {
                    $this->class = $c;
                }
            }
        }
    }

    /**
     * @param string $class
     * @return bool
     */
    public function hasClass($class)
    {
        return is_string($class) && isset($this->class) &&
            in_array($class, explode(' ', $this->class), true);
    }

    /**
     * @param string|array|null $class
     */
    public function removeClass($class = null)
    {
        if (! isset($this->class)) {
            return;
        }

        if ($class === null) {
            $this->removeAttribute('class');

            return;
        }

        if (is_string($class)) {
            $class = explode(' ', $class);
        }

        if (is_array($class)) {
            $class = array_diff(explode(' ', $this->class), $class);
            if (empty($class)) {
                $this->removeAttribute('class');
            } else {
                $this->class = implode(' ', $class);
            }
        }
    }

    /**
     * @return array
     */
    public function getAllAttributes()
    {
        return $this->attr;
    }

    /**
     * @param string $name
     * @return ?string
     */
    public function getAttribute($name)
    {
        return $this->{$name};
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setAttribute($name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute($name)
    {
        return isset($this->{$name});
    }

    /**
     * @param string $name
     */
    public function removeAttribute($name)
    {
        unset($this->{$name});
    }

    /**
     * Remove
     */
    public function remove()
    {
        if ($this->parent) {
            $this->parent->removeChild($this);
        }
    }

    /**
     * @param self $node
     */
    public function removeChild(self $node)
    {
        foreach ($node->children as $child) {
            $node->removeChild($child);
        }

        // No need to re-index node->children because it is about to be removed!

        foreach ($node->nodes as $entity) {
            $enIdx = array_search($entity, $node->nodes, true);
            $edIdx = array_search($entity, $node->dom->nodes, true);

            if ($enIdx !== false) {
                unset($node->nodes[$enIdx]);
            }

            if ($edIdx !== false) {
                unset($node->dom->nodes[$edIdx]);
            }
        }

        // No need to re-index node->nodes because it is about to be removed!

        $nIdx = array_search($node, $this->nodes, true);
        $cIdx = array_search($node, $this->children, true);
        $dIdx = array_search($node, $this->dom->nodes, true);

        if ($nIdx !== false) {
            unset($this->nodes[$nIdx]);
        }

        $this->nodes = array_values($this->nodes);

        if ($cIdx !== false) {
            unset($this->children[$cIdx]);
        }

        $this->children = array_values($this->children);

        if ($dIdx !== false) {
            unset($this->dom->nodes[$dIdx]);
        }

        // Do not re-index dom->nodes because nodes point to other nodes in the
        // array explicitly!

        $node->clear();
    }

    /**
     * @param string $id
     * @return ?self
     */
    public function getElementById($id)
    {
        return $this->find("#$id", 0);
    }

    /**
     * @param string $id
     * @param ?int $idx
     * @return self|self[]|null
     */
    public function getElementsById($id, $idx = null)
    {
        return $this->find("#$id", $idx);
    }

    /**
     * @param string $name
     * @return ?self
     */
    public function getElementByTagName($name)
    {
        return $this->find($name, 0);
    }

    /**
     * @param string $name
     * @param ?int $idx
     * @return self|self[]|null
     */
    public function getElementsByTagName($name, $idx = null)
    {
        return $this->find($name, $idx);
    }

    /**
     * @return ?self
     */
    public function parentNode()
    {
        return $this->parent();
    }

    /**
     * @param int $idx
     * @return self|self[]|null
     */
    public function childNodes($idx = -1)
    {
        if ($idx === -1) {
            return $this->children;
        }

        if (isset($this->children[$idx])) {
            return $this->children[$idx];
        }

        return null;
    }

    /**
     * @return ?self
     */
    public function firstChild()
    {
        return ! empty($this->children) ? $this->children[0] : null;
    }

    /**
     * @return ?self
     */
    public function lastChild()
    {
        return ! empty($this->children) ? end($this->children) : null;
    }

    /**
     * @return ?self
     */
    public function nextSibling()
    {
        if ($this->parent === null) {
            return null;
        }

        $idx = array_search($this, $this->parent->children, true);

        return $idx !== false && isset($this->parent->children[$idx + 1]) ? $this->parent->children[$idx + 1] : null;
    }

    /**
     * @return ?self
     */
    public function previousSibling()
    {
        if ($this->parent === null) {
            return null;
        }

        $idx = array_search($this, $this->parent->children, true);

        return $idx !== false && $idx > 0 ? $this->parent->children[$idx - 1] : null;
    }

    /**
     * @return bool
     */
    public function hasChildNodes()
    {
        return ! empty($this->children);
    }

    /**
     * @return string
     */
    public function nodeName()
    {
        return $this->tag;
    }

    /**
     * @param self $node
     * @return $this
     */
    public function appendChild(self $node)
    {
        $node->parent = $this;
        $this->nodes[] = $node;
        $this->children[] = $node;

        if ($this->dom) { // Attach current node to DOM (recursively)
            $children = [$node];

            while ($children) {
                $child = array_pop($children);
                $children = array_merge($children, $child->children);

                $this->dom->nodes[] = $child;
                $child->dom = $this->dom;
                $child->_[self::HDOM_INFO_BEGIN] = count($this->dom->nodes) - 1;
                $child->_[self::HDOM_INFO_END] = $child->_[self::HDOM_INFO_BEGIN];
            }

            $this->dom->root->_[self::HDOM_INFO_END] = count($this->dom->nodes) - 1;
        }

        return $this;
    }
}
