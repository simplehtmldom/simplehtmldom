<?php
/*******************************************************************************
Version: 0.99 ($Rev$)
Website: http://sourceforge.net/projects/simplehtmldom/
Author: S.C. Chen (me578022@gmail.com)
Acknowledge: Jose Solorzano (https://sourceforge.net/projects/php-html/)
Contributions by: Yousuke Kumakura (Attribute filters)
Licensed under The MIT License
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

define('HDOM_INFO_TEXT',    4);
define('HDOM_INFO_INNER',   5);
define('HDOM_INFO_OUTER',   6);


// helper functions
// -----------------------------------------------------------------------------
// get dom form file
function file_get_dom() {
    $dom = new simple_html_dom;
    $args = func_get_args();
    $dom->load(call_user_func_array('file_get_contents', $args));
    return $dom;
}

// get dom form string
function str_get_dom($str) {
    $dom = new simple_html_dom;
    $dom->load($str);
    return $dom;
}

// simple html dom node
// -----------------------------------------------------------------------------
class simple_html_dom_node {
    public $dnode = null;
    public $dom = null;
    public $begin = -1;
    public $end = -1;
    public $_info = array();

    function __construct($dom, $dnode) {
        $this->dom = $dom;
        $this->dnode = $dnode;
        $dnode->__bind = &$this;
    }

    function __toString() {
        return $this->outertext();
    }

    function __get($name) {
        if ($this->dnode->nodeName[0]!=='#' && $this->dnode->hasAttribute($name))
               return $this->dnode->getAttribute($name);

        switch($name) {
            case 'tag': return $this->dnode->nodeName;
            case 'outertext': return $this->outertext();
            case 'innertext': return $this->innertext();
            case 'plaintext': return $this->plaintext();
        }
    }

    function __set($name, $value) {
        switch($name) {
            case 'outertext': return $this->_info[HDOM_INFO_OUTER] = $value;
            case 'innertext': return $this->_info[HDOM_INFO_INNER] = $value;
            case 'plaintext': return $this->_info->restore_noise($this->info[HDOM_INFO_TEXT]);
        }
        $this->dnode->setAttribute($name, $value);
    }
    
    function __isset($name) {
        switch($name) {
            case 'outertext': return true;
            case 'innertext': return true;
            case 'plaintext': return true;
        }
        return ($this->dnode->nodeName[0]!=='#' && $this->dnode->hasAttribute($name));
    }

    // get dom node's inner html
    function innertext() {
        if (isset($this->_info[HDOM_INFO_INNER])) return $this->_info[HDOM_INFO_INNER];
        
        $ret = '';
        if ($this->dnode->hasChildNodes()) {
            foreach($this->dnode->childNodes as $c)
                $ret .= $c->__bind->outertext();
        }
        else {
            $str = $this->dnode->nodeValue;
            if ($this->dnode->nodeName!=='#cdata-section')
                $str = htmlentities($str, ENT_COMPAT,'UTF-8');
            $str = mb_convert_encoding($str, $this->dom->encoding, 'UTF-8');
            $ret .= $str;
        }
        return $ret;
    }

    // get dom node's outer text (with tag)
    function outertext() {
        $ret = '';

        // begin tag
        switch($this->dnode->nodeName) {
            case '#text':
                break;
            case '#document':
                break;
            case '#cdata-section':
                break;
            case '#comment':
                $ret .= '<!--';
                break;
            default:
                $str = '<'.$this->dnode->nodeName;
                if ($this->dnode->hasAttributes()) {
                    $str .= ' ';
                    foreach($this->dnode->attributes as $a) {
                        $str .= $a->name;
                        $str .= '="' . urldecode(mb_convert_encoding($a->value, $this->dom->encoding, 'UTF-8')) . '" ';
                    }
                    // trim last blank
                    $str = substr($str, 0, -1);
                }
                $ret .= $str . '>';
        }
        
        $ret .= $this->innertext();

        // end tag
        switch($this->dnode->nodeName) {
            case '#text':
                break;
            case '#document':
                break;
            case '#cdata-section':
                break;
            case '#comment':
                $ret .= '-->';
                break;
            case 'br':
                break;
            case 'img':
                break;
            case 'input':
                break;
            default:
                $ret .= '</'.$this->dnode->nodeName.'>';
        }
        return $ret;
    }

    // get dom node's plain text
    function plaintext() {
        switch($this->dnode->nodeName) {
            case '#cdata-section':
                return '';
            case '#comment':
                return '';
        }

        $ret = '';

        if ($this->dnode->hasChildNodes()) {
            foreach($this->dnode->childNodes as $c)
                $ret .= $c->__bind->plaintext();
        }
        else {
            $str = $this->dnode->nodeValue;
            if ($this->dnode->nodeName!=='#cdata-section')
                $str = htmlentities($str, ENT_COMPAT, 'UTF-8');
            $str = mb_convert_encoding($str, $this->dom->encoding, 'UTF-8');
            $ret .= $str;
        }
        return $ret;
    }

    // find elements by css selector
    function find($selector, $idx=-1) {
        $selector = trim($selector);
        //if ($selector==='*') return $this->children;

        $selectors = $this->parse_selector($selector);
        if (($count=count($selectors))===0) return array();
        $found_keys = array();

        // find each selector
        for ($c=0; $c<$count; ++$c) {
            if (($levle=count($selectors[0]))===0)
                return array();

            $head = array($this->begin=>1);

            // handle descendant selectors, no recursive!
            for ($l=0; $l<$levle; ++$l) {
                $ret = array();
                foreach($head as $k=>$v) {
                    $n = ($l==0) ? $this : $this->dom->nodes[$k];
                    $n->seek($selectors[$c][$l], $ret);
                }
                $head = $ret;
            }

            foreach($head as $k=>$v) {
                if (!isset($found_keys[$k]))
                    $found_keys[$k] = 1;
            }
        }

        // sort keys
        ksort($found_keys);

        $found = array();
        foreach($found_keys as $k=>$v)
            $found[] = $this->dom->nodes[$k];

        // return nth-element or array
        if ($idx<0) return $found;
        return (isset($found[$idx])) ? $found[$idx] : null;
    }

    protected function parse_selector($selector_string) {
        // pattern of CSS selectors, modified from mootools
        $pattern = "/([A-Za-z0-9_\\-:]*)(?:\#([\w-]+)|\.([\w-]+))?(?:\[(\w+)(?:([!*^$]?=)[\"']?([^\"']*)[\"']?)?])?/";

        // handle multiple selectors
        $selector_list = split(',', $selector_string);
        $selectors = array();

        foreach($selector_list as $selector) {
            $result = array();
            preg_match_all($pattern, trim($selector), $matches, PREG_SET_ORDER);

            foreach ($matches as $m) {
                list($tag, $key, $val, $exp) = array($m[1], null, null, '=');

                if ($m[0]==='') continue;
                if(!empty($m[2])) {$key='id'; $val=$m[2];}
                if(!empty($m[3])) {$key='class'; $val=$m[3];}
                if(!empty($m[4])) {$key=$m[4];}
                if(!empty($m[5])) {$exp=$m[5];}
                if(!empty($m[6])) {$val=$m[6];}

                // convert to lowercase
                $tag=strtolower($tag);
                $key=strtolower($key);
                
                switch($tag) {
                    case 'text':
                        $tag = '#'.$tag;
                        break;
                    case 'comment':
                        $tag = '#'.$tag;
                        break;
                }

                $result[] = array($tag, $key, $val, $exp);
            }
            $selectors[] = $result;
        }
        return $selectors;
    }

    // seek for given conditions
    protected function seek($selector, &$ret) {
        list($tag, $key, $val, $exp) = $selector;
        
        //echo $this->dnode->nodeName;
        //echo $this->begin . '-' . $this->end;
        //echo "<br>\n";

        for($i=$this->begin; $i<$this->end; ++$i) {
            $node = $this->dom->nodes[$i];
            $pass = true;
            
            //echo $tag . '->' . $node->dnode->nodeName."\n";

            // compare tag
            if (isset($tag[0]) && $tag[0]==='#' && $tag===$node->dnode->nodeName) {
                if(!isset($ret[$i]))
                    $ret[$i] = 1;
                continue;
            }
            
            if ($node->dnode->nodeName[0]==='#')
                continue;
            
            if ($tag && $tag!=$node->dnode->nodeName) {$pass=false;}
            
            // compare key
            if ($pass && $key && !$node->dnode->hasAttribute($key)) {$pass=false;}
            // compare value
            if ($pass && $key && $val) {
                $check = $this->match($exp, $val, $node->dnode->getAttribute($key));

                // handle multiple class
                if (!$check && strcasecmp($key, 'class')===0) {
                    foreach(explode(' ',$node->dnode->getAttribute($key)) as $k) {
                        $check = $this->match($exp, $val, $k);
                        if ($check) break;
                    }
                }

                if (!$check)
                    $pass = false;
            }

            if ($pass) {
                if(!isset($ret[$i]))
                    $ret[$i] = 1;
            }
        }
        unset($node);
    }

    protected function match($exp, $pattern, $value) {
        $check = true;
        switch ($exp) {
            case '=':
                $check = ($value===$pattern) ? true : false; break;
            case '!=':
                $check = ($value!==$pattern) ? true : false; break;
            case '^=':
                $check = (preg_match("/^".preg_quote($pattern,'/')."/", $value)) ? true : false; break;
            case '$=':
                $check = (preg_match("/".preg_quote($pattern,'/')."$/", $value)) ? true : false; break;
            case '*=':
                $check = (preg_match("/".preg_quote($pattern,'/')."/", $value)) ? true : false; break;
        }
        return $check;
    }
/*
    

    function __isset($name) {
    }
*/
}

// simple html dom parser
// -----------------------------------------------------------------------------
class simple_html_dom {
    public $dom = null;
    public $root = null;
    public $nodes = array();
    public $total = 0;

    function __get($name) {
        if (isset($this->dom->{$name}))
            return $this->dom->{$name};
    }

    function __set($name, $value) {
        if (isset($this->dom->{$name}))
            $this->dom->{$name} = $value;
    }

    function __toString() {
        return $this->root->outertext();
    }

    // load html from file
    function load_file() {
        $args = func_get_args();
        $this->load(call_user_func_array('file_get_contents', $args), true);
    }

    // load html from string
    function load($str) {
        $this->dom = new DOMDocument();
        $this->dom->recover = true;
        $this->dom->preserveWhiteSpace = true;
        @$this->dom->loadHTML($str);
        $this->root = new simple_html_dom_node($this, $this->dom->childNodes->item(1));
        $this->parse($this->root);
    }

    // parse html content
    function parse($node) {
        if ($node->dnode->hasChildNodes()) {
            $node->begin = $this->total; 
            foreach($node->dnode->childNodes as $c) {
                $n = new simple_html_dom_node($this, $c);
                $this->nodes[] = $n;
                ++$this->total;
                $this->parse($n);
            }
            $node->end = $this->total;
        }
    }

    // find dom node by css selector
    function find($selector, $idx=-1) {
        return $this->root->find($selector, $idx);
    }

    // clean up memory
    function clear() {
        foreach($this->nodes as $n) {
            $n->dom = null;
            $n->dnode->__bind = null;
            $n->dnode = null;
            $n = null;
        }
        $this->root->dom = null;
        $this->root->dnode->__bind = null;
        $this->root->dnode = null;
        $this->root = null;
        $this->dom = null;
    }
}
?>