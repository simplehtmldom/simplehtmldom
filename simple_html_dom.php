<?php
/*******************************************************************************
Version: 0.97
Author: S. C. Chen (me578022@gmail.com)
Acknowledge: Jose Solorzano (https://sourceforge.net/projects/php-html/)
Contributions by: Yousuke Kumakura (Attribute Filters)
Licensed under The MIT License
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

define('HDOM_TYPE_ELEMENT', 1);
define('HDOM_TYPE_COMMENT', 2);
define('HDOM_TYPE_TEXT',    3);
define('HDOM_TYPE_ENDTAG',  4);
define('HDOM_QUOTE_DOUBLE', 0);
define('HDOM_QUOTE_SINGLE', 1);
define('HDOM_QUOTE_NO',     3);
define('HDOM_INFO_BEGIN',   0);
define('HDOM_INFO_END',     1);
define('HDOM_INFO_QUOTE',   2);
define('HDOM_INFO_SPACE',   3);
define('HDOM_INFO_TEXT',    4);
define('HDOM_INFO_INNER',   5);
define('HDOM_INFO_OUTER',   6);
define('HDOM_INFO_ENDSPACE',7);

// helper functions
// -----------------------------------------------------------------------------
// get dom form file
function file_get_dom() {
    $args = func_get_args();
    $dom = new simple_html_dom;
    $dom->load(call_user_func_array('file_get_contents', $args), true);
    return $dom;
}

// get dom form string
function str_get_dom($str, $lowercase=true) {
    $dom = new simple_html_dom;
    $dom->load($str, $lowercase);
    return $dom;
}

// simple html dom node
// -----------------------------------------------------------------------------
class simple_html_dom_node {
    public $tag = '';
    public $attr = array();
    public $parent = null;
    public $children = array();
    public $nodetype = HDOM_TYPE_TEXT;
    public $dom = null;
    public $nodes = array();
    public $info = array(
        HDOM_INFO_BEGIN=>0,
        HDOM_INFO_END=>0,
        HDOM_INFO_TEXT=>'',
        HDOM_INFO_ENDSPACE=>'',
        HDOM_INFO_QUOTE=>array(),
        HDOM_INFO_SPACE=>array());

    function __construct($dom) {
        $this->dom = $dom;
    }

    function __toString() {
        return $this->outertext();
    }

    function __get($name) {
        if (isset($this->attr[$name])) return $this->attr[$name];
        switch($name) {
            case 'outertext': return $this->outertext();
            case 'innertext': return $this->innertext();
            case 'plaintext': return $this->plaintext();
            default: return array_key_exists($name, $this->attr);
        }
    }

    function __set($name, $value) {
        switch($name) {
            case 'outertext': return $this->info[HDOM_INFO_OUTER] = $value;
            case 'innertext': return $this->info[HDOM_INFO_INNER] = $value;
            case 'plaintext': return $this->info[HDOM_INFO_TEXT] = $value;
        }
        if (!isset($this->attr[$name])) {
            $this->info[HDOM_INFO_SPACE][] = array(' ', '', ''); 
            $this->info[HDOM_INFO_QUOTE][] = HDOM_QUOTE_DOUBLE;
        }
        $this->attr[$name] = $value;
    }

    function __isset($name) {
        switch($name) {
            case 'outertext': return true;
            case 'innertext': return true;
            case 'plaintext': return true;
        }
        //no value attr: nowrap, checked selected...
        return (array_key_exists($name, $this->attr)) ? true : isset($this->attr[$name]);
    }

    // clean up memory due to php5 circular references memory leak...
    function clear() {
        unset($this->tag);
        unset($this->nodetype);
        unset($this->info);
        unset($this->attr);
        unset($this->parent);
        unset($this->children);
        unset($this->nodes);
        unset($this->dom);
    }

    // returns the parent of node
    function parent() {
        return $this->parent;
    }

    // returns children of node
    function children($idx=-1) {
        if ($idx==-1) return $this->children;
        if (isset($this->children[$idx])) return $this->children[$idx];
        return null;
    }

    // returns the first child of node
    function first_child() {
        if (count($this->children)>0) return $this->children[0];
        return null;
    }

    // returns the last child of node
    function last_child() {
        if (($count=count($this->children))>0) return $this->children[$count-1];
        return null;
    }

    // returns the next sibling of node    
    function next_sibling() {
        if ($this->parent===null) return null;
        $idx = 0;
        $count = count($this->parent->children);
        while ($idx<$count && $this!==$this->parent->children[$idx]) ++$idx;
        if (++$idx>=$count) return null;
        return $this->parent->children[$idx];
    }

    // returns the previous sibling of node
    function prev_sibling() {
        if ($this->parent===null) return null;
        $idx = 0;
        $count = count($this->parent->children);
        while ($idx<$count && $this!==$this->parent->children[$idx]) ++$idx;
        if (--$idx<0) return null;
        return $this->parent->children[$idx];
    }

    // get dom node's inner html
    function innertext() {
        if (isset($this->info[HDOM_INFO_INNER])) return $this->info[HDOM_INFO_INNER];
        $ret = '';
        foreach($this->nodes as $n) $ret .= $n->outertext();
        return $ret;
    }

    // get dom node's outer text (with tag)
    function outertext() {
        if ($this->tag=='root') return $this->dom->save();
        if (isset($this->info[HDOM_INFO_OUTER])) return $this->info[HDOM_INFO_OUTER];
        // begin tag
        $ret = $this->dom->nodes[$this->info[HDOM_INFO_BEGIN]]->text();
        // inner
        if (isset($this->info[HDOM_INFO_INNER])) $ret .= $this->info[HDOM_INFO_INNER];
        else {foreach($this->nodes as $n) $ret .= $n->outertext();}
        // end tag
        if($this->info[HDOM_INFO_END]) $ret .= $this->dom->nodes[$this->info[HDOM_INFO_END]]->text($this->tag);
        return $ret;
    }

    // get dom node's plain text
    function plaintext() {
        if ($this->nodetype==HDOM_TYPE_TEXT) return $this->info[HDOM_INFO_TEXT];
        $ret = '';
        foreach($this->nodes as $n) $ret .= $n->plaintext();
        return $ret;
    }

    // get node's text
    function text($tag=null) {
        if ($tag===null) $tag = $this->tag;

        switch($this->nodetype) {
            case HDOM_TYPE_TEXT:    return $this->info[HDOM_INFO_TEXT];
            case HDOM_TYPE_COMMENT: return $this->info[HDOM_INFO_TEXT];
            case HDOM_TYPE_ENDTAG:  return '</'.$tag.'>';
        }

        $ret = '<'.$tag;
        $i = 0;

        foreach($this->attr as $key=>$val) {
            // skip removed attribute
            if ($val===null || $val===false) {
                ++$i; 
                continue;
            }
            $ret .= $this->info[HDOM_INFO_SPACE][$i][0];
            //no value attr: nowrap, checked selected...
            if ($val===true)
                $ret .= $key;
            else {
                $quote = '';
                switch($this->info[HDOM_INFO_QUOTE][$i]) {
                    case HDOM_QUOTE_DOUBLE: $quote = '"'; break;
                    case HDOM_QUOTE_SINGLE: $quote = "'"; break;
                }
                $ret .= $key.$this->info[HDOM_INFO_SPACE][$i][1].'='.$this->info[HDOM_INFO_SPACE][$i][2].$quote.$val.$quote;
            }
            ++$i;
        }
        return $ret . $this->info[HDOM_INFO_ENDSPACE] . '>';
    }

    // find elements by css selector
    function find($selector, $idx=-1) {
        if ($selector=='*') return $this->children;

        $selectors = $this->parse_selector($selector);
        if (($levle=count($selectors))==0) return array();

        $ret = array();
        $head = array($this->info[HDOM_INFO_BEGIN]=>1);

        // no recursive!
        for ($l=0; $l<$levle; ++$l) {
            $op = $selectors[$l]['op'];
            $key = $selectors[$l]['key'];
            $val = $selectors[$l]['val'];
            $tag = $selectors[$l]['tag'];
            $exp = $selectors[$l]['exp'];
            if ($this->dom->lowercase) {
                if ($tag) $tag = strtolower($tag);
                if ($key) $key = strtolower($key);
            }

            $ret = array();
            foreach($head as $k=>$v) {
                $n = ($k==-1) ? $this->dom->root : $this->dom->nodes[$k];
                $n->seek($op, $tag, $key, $val, $exp, $ret);
            }
            $head = $ret;
        }

        $final = array();
        foreach($head as $k=>$v) $final[] = $this->dom->nodes[$k];

        if ($idx<0) return $final;
        return (isset($final[$idx])) ? $final[$idx] : null;
    }

    private function parse_selector($selector) {
        $selectors = array();

        // parse CSS selectors, pattern is modified from mootools
        $pattern = "/([!]?)([A-Za-z0-9_\\-:]*)(?:\#([\w-]+)|\.([\w-]+))?(?:\[(\w+)(?:([!*^$]?=)[\"']?([^\"']*)[\"']?)?])?/";
        preg_match_all($pattern, trim($selector), $matches, PREG_SET_ORDER);

        foreach ($matches as $v) {
            $op = true;
            $key = null;
            $val = null;
            $tag = $v[2];
            $exp = '=';
            if ($v[0]=='') continue;
            if ($v[1]=='!') $op = false;
            if(!empty($v[3])) {$key = 'id'; $val = $v[3];}
            if(!empty($v[4])) {$key = 'class'; $val = $v[4];}
            if(!empty($v[5])) {
                $key = $v[5];
                if(!empty($v[6])) $exp = $v[6];
                if(!empty($v[7])) $val = $v[7];
            }
            $selectors[] = array('op'=>$op, 'tag'=>$tag, 'key'=>$key, 'val'=>$val, 'exp'=>$exp);
        }
        return $selectors;
    }

    // seek for given conditions
    private function seek($op, $tag, $key, $val, $exp, &$ret) {
        for($i=$this->info[HDOM_INFO_BEGIN]+1; $i<$this->info[HDOM_INFO_END]; ++$i) {
            $n = $this->dom->nodes[$i];
            if ($n->nodetype==HDOM_TYPE_ENDTAG) continue;
            $pass = true;
            if ($tag && $tag!=$n->tag) $pass = false;
            if ($pass && $key && !(isset($n->attr[$key]))) $pass = false;
            if ($pass && $key && $val) {
                $check = true;
                switch ($exp) {
                    case '=':
                        $check = ($n->attr[$key]===$val) ? true : false; break;
                    case '!=':
                        $check = ($n->attr[$key]!==$val) ? true : false; break;
                    case '^=':
                        $check = (preg_match("/^".preg_quote($val,'/')."/", $n->attr[$key])) ? true : false; break;
                    case '$=':
                        $check = (preg_match("/".preg_quote($val,'/')."$/", $n->attr[$key])) ? true : false; break;
                    case '*=':
                        $check = (preg_match("/".preg_quote($val,'/')."/", $n->attr[$key])) ? true : false; break;
                }
                if (!isset($n->attr[$key]) || !$check) $pass = false;
            }
            if ($op) {if ($pass) $ret[$i] = 1;}
            else {if (!$pass) $ret[$i] = 1;}
        }
        unset($n);
    }

    // camel naming conventions
    function getAttribute($name) {return $this->__get($name);}
    function setAttribute($name, $value) {$this->__set($name, $value);}
    function hasAttribute($name) {return $this->__isset($name);}
    function removeAttribute($name) {$this->__set($name, null);}
    function getElementById($id) {return $this->find("#$id", 0);}
    function getElementsById($id, $idx=-1) {return $this->find("#$id", $idx);}
    function getElementByTagName($name) {return $this->find($name, 0);}
    function getElementsByTagName($name, $idx=-1) {return $this->find($name, $idx);}
    function parentNode() {return $this->parent();}
    function childNodes($idx=-1) {return $this->children($idx);}
    function firstChild() {return $this->first_child();}
    function lastChild() {return $this->last_child();}
    function nextSibling() {return $this->next_sibling();}
    function previousSibling() {return $this->prev_sibling();}
}

// simple html dom parser
// -----------------------------------------------------------------------------
class simple_html_dom {
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
    private $token_slash = array(' '=>1, '/'=>1, '>'=>1, "\n"=>1, "\t"=>1);
    private $token_attr  = array(' '=>1, '>'=>1);
    private $self_closing_tags = array('img'=>1, 'br'=>1, 'input'=>1, 'meta'=>1, 'link'=>1, 'hr'=>1, 'base'=>1, 'embed'=>1, 'spacer'=>1);
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
    function load($str, $lowercase=true) {
        // prepare
        $this->prepare($str, $lowercase);
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
    function load_file() {
        $args = func_get_args();
        $this->load(call_user_func_array('file_get_contents', $args), true);
    }

    // save dom as string
    function save($filepath='') {
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
        if ($filepath!=='') file_put_contents($filepath, $ret);
        return $ret;
    }

    // find dom node by css selector
    function find($selector, $idx=-1) {
        return $this->root->find($selector, $idx);
    }

    // prepare HTML data and init everything
    function prepare($str, $lowercase=true) {
        $this->clear();
        $this->noise = array();
        $this->nodes = array();
        $this->html = $str;
        $this->lowercase = $lowercase;
        $this->index = 0;
        $this->pos = 0;
        $this->root = new simple_html_dom_node($this);
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
        $node = new simple_html_dom_node($this);
        $this->nodes[] = $node;
        $node->tag = 'text';
        $node->info[HDOM_INFO_BEGIN] = $this->index;
        $node->info[HDOM_INFO_TEXT] = $this->restore_noise($s);
        $node->parent = $this->parent;
        $this->parent->nodes[] = $node;

        ++$this->index;
        return $node;
    }

    // read tag info
    private function read_tag() {
        if ($this->char!='<') {
            $this->root->info[HDOM_INFO_END] = $this->index;
            return null;
        }
        $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next

        $node = new simple_html_dom_node($this);
        $this->nodes[] = $node;
        $node->info[HDOM_INFO_BEGIN] = $this->index;
        ++$this->index;

        // end tag
        if ($this->char=='/') {
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
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
                    $this->parent->nodes[] = $node;
                }
                $this->parent->info[HDOM_INFO_END] = $this->index-1;
            }
            else {
                $this->parent->info[HDOM_INFO_END] = $this->index-1;
                $this->parent = $this->parent->parent;
            }

            $node->parent = $this->parent;
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
            return $node;
        }

        $node->tag = $this->copy_until($this->token_slash);
        $node->parent = $this->parent;

        // comment
        if ($node->tag=='!--___noise___') {
            $node->nodetype = HDOM_TYPE_COMMENT;
            $node->info[HDOM_INFO_END] = 0;
            $node->info[HDOM_INFO_TEXT] = '<' . $node->tag . $this->copy_until_char_escape('>');
            $node->tag = 'comment';
            if ($this->char=='>') $node->info[HDOM_INFO_TEXT].='>';
            $node->info[HDOM_INFO_TEXT] = $this->restore_noise($node->info[HDOM_INFO_TEXT]);
            $this->parent->nodes[] = $node;
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
            return $node;
        }

        // text
        if (!preg_match("/^[A-Za-z0-9_\\-:]+$/", $node->tag)) {
            $node->nodetype = HDOM_TYPE_TEXT;
            $node->info[HDOM_INFO_END] = 0;
            $node->info[HDOM_INFO_TEXT] = '<' . $node->tag . $this->copy_until_char_escape('>');
            $node->tag = 'text';
            if ($this->char=='>') $node->info[HDOM_INFO_TEXT].='>';
            $node->info[HDOM_INFO_TEXT] = $this->restore_noise($node->info[HDOM_INFO_TEXT]);
            $this->parent->nodes[] = $node;
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
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
        $this->parent->nodes[] = $node;

        $guard = 0; // prevent infinity loop
        $space = array($this->copy_skip($this->token_blank), '', '');

        // handle attributes
        do {
            if ($this->char!==null && $space[0]=='') break;
            $name = $this->copy_until($this->token_equal);

            if($guard==$this->pos) {
                $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
                continue;
            }
            $guard = $this->pos;

            // handle endless '<'
            if($this->pos>=$this->size-1 && $this->char!='>') {
                $node->nodetype = HDOM_TYPE_TEXT;
                $node->info[HDOM_INFO_END] = 0;
                $node->info[HDOM_INFO_TEXT] = '<'.$node->tag;
                $node->info[HDOM_INFO_TEXT] .= $space[0];
                $node->info[HDOM_INFO_TEXT] .= $name;
                $node->info[HDOM_INFO_TEXT] = $this->restore_noise($node->info[HDOM_INFO_TEXT]);
                $node->tag = 'text';
                return $node;
            }

            if ($name!='/' && $name!='') {
                $space[1] = $this->copy_skip($this->token_blank);
                if ($this->lowercase) $name = strtolower($name);
                if ($this->char=='=') {
                    $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
                    $this->parse_attr($node, $name, $space);
                }
                else {
                    //no value attr: nowrap, checked selected...
                    $node->info[HDOM_INFO_QUOTE][] = HDOM_QUOTE_NO;
                    $node->attr[$name] = true;
                    if ($this->char!='>') $this->char = $this->html[--$this->pos]; // prev
                }
                $node->info[HDOM_INFO_SPACE][] = $space;
                $space = array($this->copy_skip($this->token_blank), '', '');
            }
            else
                break;
        } while($this->char!='>' && $this->char!='/');

        $node->info[HDOM_INFO_ENDSPACE] = $space[0];

        // check self closing
        if ($this->copy_until_char_escape('>')=='/') {    
            $node->info[HDOM_INFO_ENDSPACE] .= '/';
            $node->info[HDOM_INFO_END] = 0;
        }
        else {
            // reset parent
            if (!isset($this->self_closing_tags[strtolower($node->tag)])) $this->parent = $node;
        }
        $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
        return $node;
    }

    // parse attributes
    private function parse_attr($node, $name, &$space) {
        $space[2] = $this->copy_skip($this->token_blank);
        switch($this->char) {
            case '"':
                $node->info[HDOM_INFO_QUOTE][] = HDOM_QUOTE_DOUBLE;
                $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
                $value = $this->copy_until_char_escape('"');
                $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
                break;
            case '\'':
                $node->info[HDOM_INFO_QUOTE][] = HDOM_QUOTE_SINGLE;
                $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
                $value = $this->copy_until_char_escape("'");
                $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
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
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
        }
    }

    private function copy_skip($chars) {
        $ret = '';
        while ($this->char!==null) {
            if (!isset($chars[$this->char])) return $ret;
            $ret .= $this->char;
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
        }
        return $ret;
    }

    private function copy_until($chars) {
        $ret = '';
        while ($this->char!==null) {
            if (isset($chars[$this->char])) return $ret;
            $ret .= $this->char;
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
        }
        return $ret;
    }

    private function copy_until_char($char) {
        $ret = '';
        while ($this->char!=$char && $this->char!==null) {
            $ret .= $this->char;
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
        }
        return $ret;
    }

    private function copy_until_char_escape($char) {
        $ret = '';
        while ($this->char!=$char && $this->char!==null) {
            // ignore string escape
            if ($this->char=='\\') {
                $ret .= $this->char;
                $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
            }
            $ret .= $this->char;
            $this->char = (++$this->pos<$this->size) ? $this->html[$this->pos] : null; // next
        }
        return $ret;
    }

    // camel naming conventions
    function childNodes($idx=-1) {return $this->root->childNodes($idx);}
    function firstChild() {return $this->root->first_child();}
    function lastChild() {return $this->root->last_child();}
    function getElementById($id) {return $this->find("#$id", 0);}
    function getElementsById($id, $idx=-1) {return $this->find("#$id", $idx);}
    function getElementByTagName($name) {return $this->find($name, 0);}
    function getElementsByTagName($name, $idx=-1) {return $this->find($name, $idx);}
    function loadFile() {$args = func_get_args();$this->load(call_user_func_array('file_get_contents', $args), true);}

    // deprecated names
    function save_file($filepath) {return $this->save($filepath);}
}
?>