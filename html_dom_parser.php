<?php
/*******************************************************************************
Version: 0.95
Author: S. C. Chen (me578022@gmail.com)
Acknowledge: Jose Solorzano (https://sourceforge.net/projects/php-html/)
Contributions by: Yousuke Kumakura (selector improvements)
Licensed under The MIT License
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

define('HDOM_TYPE_ELEMENT', 1);
define('HDOM_TYPE_TEXT',    3);
define('HDOM_TYPE_ENDTAG',  4);
define('HDOM_QUOTE_DOUBLE', 0);
define('HDOM_QUOTE_SINGLE', 1);
define('HDOM_QUOTE_NO',     3);
define('HDOM_INFO_BEGIN',   0);
define('HDOM_INFO_END',     1);
define('HDOM_INFO_SLASH',   2);
define('HDOM_INFO_QUOTE',   3);
define('HDOM_INFO_SPACE',   4);
define('HDOM_INFO_TEXT',    5);
define('HDOM_INFO_INNER',   6);
define('HDOM_INFO_OUTER',   7);

// quick functions
// -----------------------------------------------------------------------------
// get dom form file
function file_get_dom($filepath, $lowercase=true) {
    $dom = new html_dom_parser;
    $dom->load_file($filepath, $lowercase);
    return $dom;
}

// get dom form string
function str_get_dom($str, $lowercase=true) {
    $dom = new html_dom_parser;
    $dom->load($str, $lowercase);
    return $dom;
}

// html dom node
// -----------------------------------------------------------------------------
class html_dom_node {
    public $tag = '';
    public $attr = array();
    public $parent = null;
    public $children = array();
    public $parser = null;
    public $nodetype = HDOM_TYPE_TEXT;
    public $info = array(
        HDOM_INFO_BEGIN=>0,
        HDOM_INFO_END=>0,
        HDOM_INFO_TEXT=>'',
        HDOM_INFO_SLASH=>false, 
        HDOM_INFO_QUOTE=>array(), 
        HDOM_INFO_SPACE=>array(),
    );

    function __construct($parser) {
        $this->parser = $parser;
    }

    function __get($var) {
        if (isset($this->attr[$var])) return $this->attr[$var];
        if ($var=='innertext') return $this->innertext();
        if ($var=='outertext') return $this->outertext();
        if ($var=='plaintext') return $this->plaintext();
        return array_key_exists($var, $this->attr);
    }

    function __set($var, $val) {
        if ($var=='innertext') return $this->info[HDOM_INFO_INNER] = $val;
        if ($var=='outertext') return $this->info[HDOM_INFO_OUTER] = $val;
        if ($var=='plaintext') return $this->info[HDOM_INFO_TEXT] = $val;
        if (!isset($this->attr[$var])) {
            array_unshift($this->info[HDOM_INFO_SPACE], '');
            array_unshift($this->info[HDOM_INFO_SPACE], '');
            array_unshift($this->info[HDOM_INFO_SPACE], ' ');
            $this->info[HDOM_INFO_QUOTE][] = HDOM_QUOTE_DOUBLE;
        }
        $this->attr[$var] = $val;
    }

    function __isset($var) {
        if ($var=='innertext') return true;
        if ($var=='outertext') return true;
        //no value attr: nowrap, checked selected...
        if(array_key_exists($var, $this->attr)) return true;
        return isset($this->attr[$var]);
    }

    function __toString() {
        return $this->outertext();
    }

    // clean up memory due to php5 circular references memory leak...
    function clear() {
        unset($this->tag);
        unset($this->nodetype);
        unset($this->info);
        unset($this->attr);
        unset($this->parser);
        unset($this->parent);
        unset($this->children);
    }

    function remove_attr($var) {
        if (isset($this->attr[$var])) unset($this->attr[$var]);
    }

    // get dom node's inner html
    function innertext() {
        if (isset($this->info[HDOM_INFO_INNER])) return $this->info[HDOM_INFO_INNER];
        $ret = '';
        foreach($this->children as $n) $ret .= $n->outertext();
        return $ret;
    }

    // get dom node's outer text (with tag)
    function outertext() {
        if (isset($this->info[HDOM_INFO_OUTER])) return $this->info[HDOM_INFO_OUTER];
        // begin tag
        $ret = $this->parser->nodes[$this->info[HDOM_INFO_BEGIN]]->text();
        // inner
        if (isset($this->info[HDOM_INFO_INNER])) $ret .= $this->info[HDOM_INFO_INNER];
        else {foreach($this->children as $n) $ret .= $n->outertext(); }
        // end tag
        if($this->info[HDOM_INFO_END])
            $ret .= $this->parser->nodes[$this->info[HDOM_INFO_END]]->text($this->tag);
        return $ret;
    }

    // get dom node's plain text
    function plaintext() {
        if ($this->nodetype==HDOM_TYPE_TEXT) return $this->info[HDOM_INFO_TEXT];
        $ret = '';
        foreach($this->children as $n) $ret .= $n->plaintext();
        return $ret;
    }

    // get node text
    function text($tag=null) {
        if ($tag===null) $tag = $this->tag;
        if ($this->nodetype==HDOM_TYPE_TEXT) return $this->info[HDOM_INFO_TEXT];
        if ($this->nodetype==HDOM_TYPE_ENDTAG) return '</'.$tag.'>';

        $ret = '<'.$tag;
        $i = 0;
        $j = 0;

        $count_space = count($this->info[HDOM_INFO_SPACE]);
        foreach($this->attr as $key=>$val) {
            $ret .= ($j<$count_space) ? $this->info[HDOM_INFO_SPACE][$j++] : ' ';

            //no value attr: nowrap, checked selected...
            if ($val===true || $val===false) {
                if($val===true) $ret .= $key;
                if ($j<$count_space) ++$j;
            }
            else {
                $quote = '"';
                if ($this->info[HDOM_INFO_QUOTE][$i]==HDOM_QUOTE_DOUBLE) $quote = '"';
                else if ($this->info[HDOM_INFO_QUOTE][$i]==HDOM_QUOTE_SINGLE) $quote = "'";
                else $quote = '';

                $ret .= $key;
                if ($j<$count_space) $ret .= $this->info[HDOM_INFO_SPACE][$j++];

                $ret .= '=';
                if ($j<$count_space) $ret .= $this->info[HDOM_INFO_SPACE][$j++];

                $ret .= $quote.$val.$quote;
            }
            ++$i;
        }

        if ($j<$count_space) $ret .= $this->info[HDOM_INFO_SPACE][$j];
        if($this->info[HDOM_INFO_SLASH]) $ret .= '/';
        return $ret.'>';
    }

    // find nodes by css selector
    function find($selector, $idx=-1) {
        $selector = trim($selector);
        if ($selector=='*') return $this->children;

        // parse CSS selectors, pattern is modified from mootools
        $pattern = "/(\w*|\*)(?:\#([\w-]+)|\.([\w-]+))?(?:\[(\w+)(?:([!*^$]?=)[\"']?([^\"']*)[\"']?)?])?/";
        preg_match_all($pattern, $selector, $matches, PREG_SET_ORDER);
        $selectors = array();

        foreach ($matches as $v) {
            $key = null;
            $val = null;
            $tag = null;
            $exp = '=';
            if ($v[0]=='') continue;
            $tag = $v[1];
            if(!empty($v[2])) {$key='id'; $val=$v[2];}
            if(!empty($v[3])) {$key='class'; $val=$v[3];}
            if(!empty($v[4])) {
                $key=$v[4];
                if(!empty($v[5])) $exp=$v[5];
                if(!empty($v[6])) $val=$v[6];
            }
            $selectors[] = array('tag'=>$tag, 'key'=>$key, 'val'=>$val, 'exp'=>$exp);
        }

        if (($levle=count($selectors))==0) return array();
        $ret = array();
        $head = array($this->info[HDOM_INFO_BEGIN]=>1);

        // no recursive!
        for ($l=0; $l<$levle; ++$l) {
            $key = $selectors[$l]['key'];
            $val = $selectors[$l]['val'];
            $tag = $selectors[$l]['tag'];
            $exp = $selectors[$l]['exp'];
            if ($this->parser->lowercase) {
                if ($tag) $tag = strtolower($tag);
                if ($key) $key = strtolower($key);
            }

            $ret = array();
            foreach($head as $k=>$v) {
                $n = ($k==-1) ? $this->parser->root : $this->parser->nodes[$k];
                $n->seek($tag, $key, $val, $exp, $ret);
            }
            $head = $ret;
        }

        $final = array();
        foreach($head as $k=>$v) $final[] = $this->parser->nodes[$k];

        if ($idx<0) return $final;
        if (!isset($final[$idx])) return null;
        return $final[$idx];
    }

    // seek for given condition
    private function seek($tag, $key, $val, $exp, &$ret) {
        for($i=$this->info[HDOM_INFO_BEGIN]+1; $i<$this->info[HDOM_INFO_END]; ++$i) {
            $n = $this->parser->nodes[$i];
            if ($n->nodetype==HDOM_TYPE_ENDTAG) continue;

            $pass = true;
            if ($tag && $tag!=$n->tag) $pass = false;
            if ($pass && $key && !(isset($n->attr[$key]))) $pass = false;
            if ($pass && $key && $val) {
                switch ($exp) {
                    case '=':
                        $valCheck = ($n->attr[$key] === $val) ? true : false;
                        break;
                    case '!=':
                        $valCheck = ($n->attr[$key] !== $val) ? true : false;
                        break;
                    case '^=':
                        $valCheck = (preg_match("/^{$val}/", $n->attr[$key])) ? true : false;
                        break;
                    case '$=':
                        $valCheck = (preg_match("/{$val}$/", $n->attr[$key])) ? true : false;
                        break;
                    case '*=':
                        $valCheck = (preg_match("/{$val}/", $n->attr[$key])) ? true : false;
                        break;
                    default:
                        $valCheck = true;
                        break;
                }
                if (!isset($n->attr[$key]) || !$valCheck) $pass = false;
            }
            if ($pass) $ret[$i] = 1;
        }
        unset($n);
    }
}

// html dom parser
// -----------------------------------------------------------------------------
class html_dom_parser {
    public  $nodes = array();
    public  $root = null;
    public  $lowercase = false;
    private $parent = null;
    private $pos;
    private $char;
    private $size;
    private $html;
    private $index;
    private $max_node = 0;
    private $noise = array();
    // use isset instead of in_array, performance increase about 30%...
    private $token_blank = array(' '=>1, "\t"=>1, "\r"=>1, "\n"=>1);
    private $token_equal = array(' '=>1, '='=>1, '/'=>1, '>'=>1, '<'=>1);
    private $token_slash = array(' '=>1, '/'=>1, '>'=>1);
    private $token_attr  = array(' '=>1, '>'=>1);
    private $self_closing_tags = array('img'=>1, 'br'=>1, 'input'=>1, 'meta'=>1, 'link'=>1, 'hr'=>1, 'embed'=>1);
    private $block_tags = array('div'=>1, 'span'=>1, 'table'=>1, 'form'=>1, 'dl'=>1, 'ol'=>1);
    private $optional_closing_tags = array(
        'th'=>array('th'=>1, 'tr'=>1, 'td'=>1), 
        'tr'=>array('tr'=>1, 'td'=>1), 
        'td'=>array('td'=>1),
        'dt'=>array('dt'=>1, 'dd'=>1),
        'dd'=>array('dd'=>1, 'dt'=>1),
        'li'=>array('li'=>1),
        'p'=>array('p'=>1),
    );

    function __toString() {
        return $this->save();
    }

    // load html from string
    function load($str, $attr_name_lowercase=true) {
        // prepare
        $this->prepare($str, $attr_name_lowercase);
        // strip out comments
        $this->remove_noise("'<!--(.*?)-->'is", false, false);
        // strip out <style> tags
        $this->remove_noise("'<\s*style[^>]*[^/]>(.*?)<\s*/\s*style\s*>'is", false, false);
        $this->remove_noise("'<\s*style\s*>(.*?)<\s*/\s*style\s*>'is", false, false);
        // strip out <script> tags
        $this->remove_noise("'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'is", false, false);
        $this->remove_noise("'<\s*script\s*>(.*?)<\s*/\s*script\s*>'is", false, false);
        // strip out <pre> tags
        $this->remove_noise("'<\s*pre[^>]*>(.*?)<\s*/\s*pre\s*>'is", false, false);
        // strip out <code> tags
        $this->remove_noise("'<\s*code[^>]*>(.*?)<\s*/\s*code\s*>'is", false, false);
        // strip out server side scripts
        $this->remove_noise("'(<\?)(.*?)(\?>)'is", false, false);
        // parsing
        while ($this->parse());
    }

    // load html from file
    function load_file($filepath, $attr_name_lowercase=true) {
        $this->load(file_get_contents($filepath), $attr_name_lowercase);
    }

    // save dom as string
    function save() {
        $ret = '';
        $count = count($this->nodes);
        for ($i=0; $i<$count; ++$i) {
            // outertext defined
            if (isset($this->nodes[$i]->info[HDOM_INFO_OUTER])) {
                $ret .= $this->nodes[$i]->info[HDOM_INFO_OUTER];
                if ($this->nodes[$i]->info[HDOM_INFO_END]>0) $i = $this->nodes[$i]->info[HDOM_INFO_END];
                continue;
            }

            $ret .= $this->nodes[$i]->text();

            // innertext defined
            if (isset($this->nodes[$i]->info[HDOM_INFO_INNER]) && $this->nodes[$i]->info[HDOM_INFO_END]>0) {
                $ret .= $this->nodes[$i]->info[HDOM_INFO_INNER];
                if ($this->nodes[$i]->info[HDOM_INFO_END]-1>$i)
                    $i = $this->nodes[$i]->info[HDOM_INFO_END]-1;
            }
        }
        return $ret;
    }

    // save dom string to file
    function save_file($filepath) {
        return file_put_contents($filepath, $this->save());
    }

    // find dom node by css selector
    function find($selector, $idx=-1) {
        return $this->root->find($selector, $idx);
    }

    // prepare HTML data and init everything
    function prepare($str, $attr_name_lowercase=true) {
        $this->clear();
        $this->noise = array();
        $this->nodes = array();
        $this->html = $str;
        $this->lowercase = $attr_name_lowercase;
        $this->index = 0;
        $this->pos = 0;
        $this->root = new html_dom_node($this);
        $this->root->tag = 'root';
        $this->root->info[HDOM_INFO_BEGIN] = -1;
        $this->parent = $this->root;
        // set the length of content
        $this->size = strlen($str);
        if ($this->size>0) $this->char = $this->html[0];
    }

    // clean up memory due to php5 circular references memory leak...
    function clear() {
        if (isset($this->html)) unset($this->html);
        if (isset($this->noise)) unset($this->noise);

        foreach($this->nodes as $n) {
            $n->clear();
            unset($n);
        }

        if (isset($this->parent)) {$this->parent->clear(); unset($this->parent);}
        if (isset($this->root)) {$this->root->clear(); unset($this->root);}
    }

    // remove noise from html content
    function remove_noise($pattern, $remove_tag=true, $remove_contents=true) {
        $count = preg_match_all($pattern, $this->html, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);

        for ($i=$count-1; $i>-1; --$i) {
            $key = '___noise___'.sprintf("% 3d", count($this->noise));
            $idx = ($remove_tag) ? 0 : 1;
            $this->noise[$key] = ($remove_contents) ? '' : $matches[$i][$idx][0];
            $this->html = substr_replace($this->html, $key, $matches[$i][$idx][1], strlen($matches[$i][$idx][0]));
        }

        // reset the length of content
        $this->size = strlen($this->html);
        if ($this->size>0) $this->char = $this->html[0];
    }

    // restore noise to html content
    private function restore_noise($text) {
        while(($pos=strpos($text, '___noise___'))!==false) {
            $key = '___noise___'.$text[$pos+11].$text[$pos+12].$text[$pos+13];
            if (isset($this->noise[$key])) 
                $text = substr($text, 0, $pos).$this->noise[$key].substr($text, $pos+14);
        }
        return $text;
    }

    // parse html content
    function parse() {
        $s = $this->copy_until_char('<');
        if ($s=='') return $this->read_tag();

        // text
        $node = new html_dom_node($this);
        $this->nodes[] = $node;
        $node->tag = 'text';
        $node->info[HDOM_INFO_BEGIN] = $this->index;
        $node->info[HDOM_INFO_TEXT] = $this->restore_noise($s);
        $node->parent = $this->parent;
        $this->parent->children[] = $node;
        // next
        ++$this->index;
        return $node;
    }

    // read tag info
    private function read_tag() {
        if ($this->char!='<') {
            $this->root->info[HDOM_INFO_END] = $this->index;
            return null;
        }

        // next
        $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;

        $node = new html_dom_node($this);
        $this->nodes[] = $node;
        $node->info[HDOM_INFO_BEGIN] = $this->index;
        ++$this->index;

        // end tag
        if ($this->char=='/') {
            // next
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
            $this->skip($this->token_blank);
            $node->nodetype = HDOM_TYPE_ENDTAG;
            $node->tag = $this->copy_until_char_escape('>');
            $tag_lower = strtolower($node->tag);
            if ($this->lowercase) $node->tag = $tag_lower;

            // mapping parent node
            if (strtolower($this->parent->tag)!==$tag_lower) {                
                if (isset($this->block_tags[$tag_lower]))  {
                    $this->parent->info[HDOM_INFO_END] = null;
                    while (($this->parent->parent) && strtolower($this->parent->tag)!==$tag_lower)
                        $this->parent = $this->parent->parent;
                }
                else {
                    $node->nodetype = HDOM_TYPE_TEXT;
                    $node->info[HDOM_INFO_END] = $this->index-1;
                    $node->info[HDOM_INFO_TEXT] = '</' . $node->tag . '>';
                    $node->tag = 'text';
                    $this->parent->children[] = $node;
                }
                $this->parent->info[HDOM_INFO_END] = $this->index-1;
            }
            else {
                $this->parent->info[HDOM_INFO_END] = $this->index-1;
                $this->parent = $this->parent->parent;
            }

            $node->parent = $this->parent;
            // next
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
            return $node;
        }

        $node->tag = $this->copy_until($this->token_slash);
        $node->parent = $this->parent;

        // text
        if (!preg_match("/^[A-Za-z0-9_\\-]+$/", $node->tag)) {
            $node->nodetype = HDOM_TYPE_TEXT;
            $node->info[HDOM_INFO_END] = 0;
            $node->info[HDOM_INFO_TEXT] = '<' . $node->tag . $this->copy_until_char_escape('>');
            if ($this->char=='>') $node->info[HDOM_INFO_TEXT].='>';
            $node->info[HDOM_INFO_TEXT] = $this->restore_noise($node->info[HDOM_INFO_TEXT]);
            
            $node->tag = 'text';
            $this->parent->children[] = $node;
            // next
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
            return $node;
        }

        // begin tag
        $node->nodetype = HDOM_TYPE_ELEMENT;
        $tag_lower = strtolower($node->tag);
        if ($this->lowercase) $node->tag = $tag_lower;

        // handle optional closing tags
        if (isset($this->optional_closing_tags[$tag_lower]) ) {
            while (isset($this->optional_closing_tags[$tag_lower][strtolower($this->parent->tag)])) {
                $this->parent->info[HDOM_INFO_END] = 0;
                $this->parent = $this->parent->parent;
            }
            $node->parent = $this->parent;
        }
        $this->parent->children[] = $node;

        // prevent infinity loop
        $guard = 0;

        // attributes
        while($this->char!='>' && $this->char!='/') {
            if ($this->char!=null) {
                if (($node->info[HDOM_INFO_SPACE][]=$this->copy_skip($this->token_blank))=='')
                    break;
            }

            $name = $this->copy_until($this->token_equal);

            if($guard==$this->pos) {
                // next
                $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
                continue;
            }
            $guard = $this->pos;

            // handle endless '<'
            if($this->pos>=$this->size-1) {
                $node->nodetype = HDOM_TYPE_TEXT;
                $node->info[HDOM_INFO_END] = 0;
                $node->info[HDOM_INFO_TEXT] = '<'.$node->tag;
                if(isset($node->info[HDOM_INFO_SPACE][0])) $node->info[HDOM_INFO_TEXT] .= $node->info[HDOM_INFO_SPACE][0];
                $node->info[HDOM_INFO_TEXT] .= $name;
                $node->info[HDOM_INFO_TEXT] = $this->restore_noise($node->info[HDOM_INFO_TEXT]);
                $node->tag = 'text';
                return $node;
            }

            if ($name!='/' && $name!='') {
                $node->info[HDOM_INFO_SPACE][] = $this->copy_skip($this->token_blank);
                if ($this->lowercase) $name = strtolower($name);
                if ($this->char=='=') {
                    // next
                    $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
                    $this->parse_attr($node, $name);
                }
                else {
                    //no value attr: nowrap, checked selected...
                    $node->attr[$name] = true;
                    $node->info[HDOM_INFO_QUOTE][] = HDOM_QUOTE_NO;
                    // prev
                    if ($this->char!='>') $this->char = $this->html[--$this->pos];
                }
            }
        }

        // check self closing
        $end_space = $this->copy_until_char_escape('>');
        if ($end_space=='/') {
            $node->info[HDOM_INFO_SLASH] = true;
            $node->info[HDOM_INFO_END] = 0;
        }
        else {
            $node->info[HDOM_INFO_SPACE][] = $end_space;
            // reset parent
            if (!isset($this->self_closing_tags[strtolower($node->tag)])) $this->parent = $node;
        }
        // next
        $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
        return $node;
    }

    // parse attributes
    private function parse_attr($node, $name) {
        $node->info[HDOM_INFO_SPACE][] = $this->copy_skip($this->token_blank);

        switch($this->char) {
            case '"':
                $node->info[HDOM_INFO_QUOTE][] = HDOM_QUOTE_DOUBLE;
                // next
                $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
                $value = $this->copy_until_char_escape('"');
                // next
                $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
                break;
            case '\'':
                $node->info[HDOM_INFO_QUOTE][] = HDOM_QUOTE_SINGLE;
                // next
                $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
                $value = $this->copy_until_char_escape("'");
                // next
                $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
                break;
            default:
                $node->info[HDOM_INFO_QUOTE][] = HDOM_QUOTE_NO;
                $value = $this->copy_until($this->token_attr);
        }
        $node->attr[$this->restore_noise($name)] = $this->restore_noise($value);
    }

    private function skip($chars) {
        while ($this->char!==null) {
            if (!isset($chars[$this->char])) return;
            // next
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
        }
    }

    private function copy_skip($chars) {
        $ret = '';
        while ($this->char!==null) {
            if (!isset($chars[$this->char])) return $ret;
            $ret .= $this->char;
            // next
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
        }
        return $ret;
    }

    private function copy_until($chars) {
        $ret = '';
        while ($this->char!==null) {
            if (isset($chars[$this->char])) return $ret;
            $ret .= $this->char;
            // next
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
        }
        return $ret;
    }

    private function copy_until_char($char) {
        $ret = '';
        while ($this->char!=$char && $this->char!==null) {
            $ret .= $this->char;
            // next
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
        }
        return $ret;
    }

    private function copy_until_char_escape($char) {
        $ret = '';
        while ($this->char!=$char && $this->char!==null) {
            // ignore string escape
            if ($this->char=='\\') {
                $ret .= $this->char;
                // next
                $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
            }
            $ret .= $this->char;
            // next
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null;
        }
        return $ret;
    }
}
?>