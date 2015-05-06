<?php

/*
 * Fais afficher le message : Notice: Undefined offset: -1 in /home/chris/documents/crp/src/tao/plugins/CapiXML/models/class.ConditionalTokenizer.php on line 351 Catchable fatal error: Argument 1 passed to ListOfToken::del() must be an instance of Token, null given, called in /home/chris/documents/crp/src/tao/plugins/CapiXML/models/class.ConditionalTokenizer.php on line 360 and defined in /home/chris/documents/crp/src/tao/plugins/CapiXML/models/class.ConditionalTokenizer.php on line 84 ================================== if (or B_Q02bDE2=DK or B_Q02bDE2=RF or B_Q02b3DE1=RF or B_Q02b3DE2=RF) B_D02b3DE1=RF, ==================================
 */
class ListOfToken implements IteratorAggregate
{

    public $list;

    function __construct(array $tokens = array())
    {
        $this->list = $tokens;
    }

    /**
     * Return a DOMDocument with XML
     */
    function getXml()
    {
        return $this->get(0)->getXml();
    }

    function getXmlString()
    {
        $xml = $this->getXml();
        $xml = $xml->saveXML();
        return $xml;
    }

    function add(Token $token)
    {
        $this->list[] = $token;
    }

    function get($rank)
    {
        if ($rank >= count($this->list)){
            return null;
        }
        else {
            return $this->list[$rank];
        }
    }

    function reverse()
    {
        return new ListOfToken(array_reverse($this->list));
    }

    function subSet($begin, $end)
    {
        return new ListOfToken(array_slice($this->list, $begin, $end - $begin + 1));
    }

    function delSet($begin, $end, $replacement = null)
    {
        // TODO: test whether it's usefull to test $replacement for nullity
        if (is_null($replacement)){
            array_splice($this->list, $begin, $end - $begin + 1);
        }
        else {
            array_splice($this->list, $begin, $end - $begin + 1, $replacement->list);
        }
    }

    function pos($token)
    {
        $pos = 0;
        // return array_search($token, $this->list); this does ==, not ===
        foreach ($this->list as $element) {
            if ($element === $token) {
                break;
            }
            $pos ++;
        }
        return $pos;
    }

    function count()
    {
        return count($this->list);
    }

    function isEmpty()
    {
        return count($this->list) == 0;
    }

    function del(Token $token)
    {
        $pos = $this->pos($token);
        array_splice($this->list, $pos, 1);
    }

    function rep(Token $token, Token $replacement)
    {
        // $pos = array_search($token, $this->list);
        $pos = $this->pos($token);
        $this->list[$pos] = $replacement;
    }

    function getIterator()
    {
        $arrayObject = new ArrayObject($this->list);
        return $arrayObject->getIterator();
    }

    function base()
    {
        $buffer = "";
        foreach ($this as $token) {
            $buffer .= "{$token->base()}\n";
        }
        return $buffer;
    }

    function __toString()
    {
        $buffer = "";
        foreach ($this as $token) {
            $buffer .= "$token ";
        }
        return trim($buffer);
    }

    function render()
    {
        $buffer = "";
        foreach ($this->list as $token) {
            $buffer .= $token->render();
        }
        return $buffer;
    }

    /**
     * Sort the data with the stickness in reverse order, higher first
     */
    function sortStickness()
    {
        usort($this->list, array(
            "Token",
            "cmpSticknessRevert"
        ));
        /*
         * print_r($this->stickness);
         */
    }
}

class Token
{ // implements IteratorAggregate {
    var $content;

    var $regexp = "ç{,800}"; // by default, a token that is not findable
    var $listOfToken;

    var $stickness = 0;

    var $reversedOrderParsing = false;

    var $children;

    static $tokenObjects;

    static $tokenStickness;

    function match($string)
    {
        $regexp = "@{$this->regexp}@is";
        return preg_match($regexp, $string);
    }

    function getIterator()
    {
        return $this->children->getIterator();
    }

    function __construct($content = null)
    {
        if (empty($this->content)){
            $this->content = $content;
        }
            // echo "'{$this->content}' ride\n";
        $this->listOfToken = new ListOfToken();
        $this->children = new ListOfToken();
    }

    /**
     * Returns a DOMDocument with only one node
     */
    function getXml($name = null)
    {
        /*
         * echo "Mi estas '".get_class($this)."' kiu enhavas '$this'"; echo ", mi havas ".$this->children->count()." filo(j)n\n";
         */
        if (empty($name)) {
            if (! empty($this->xmlMarkup)){
                $name = $this->xmlMarkup;
            }
            else{
                $name = get_class($this);
            }
        }
        $dom = new DOMDocument();
        $node = $dom->createElement($name);
        $dom->appendChild($node);
        foreach ($this->children as $child) {
            $xml = $child->getXml();
            $xml = $dom->importNode($xml->documentElement, true);
            $node->appendChild($xml);
        }
        return $dom;
    }

    function getXmlOperator($type)
    {
        $dom = new DomDocument();
        $operator = $dom->createElement("operator");
        $dom->appendChild($operator);
        $operator->setAttribute("type", $type);
        foreach ($this->children as $child) {
            $operator->appendChild($dom->importNode($child->getXml()->documentElement, true));
        }
        return $dom;
    }

    /**
     * "renames" the node, simply moving the children
     */
    protected function renameNode($dom, $node, $newName)
    {
        $newNode = $dom->createElement($newName);
        foreach ($node->attributes as $attribute) {
            $newNode->setAttributeNode($attribute);
        }
        
        while ($node->childNodes->length > 0) {
            // /!\ an appendChild also removes the child from the old parent node
            // so the number of child nodes decreases.
            $newNode->appendChild($node->firstChild);
        }
        $node->parentNode->replaceChild($newNode, $node);
    }

    /**
     * provide a basing printing
     */
    function base()
    {
        $type = get_class($this);
        return "($type '{$this->content}')";
    }

    function __toString()
    {
        $content = $this->content;
        $buffer = "$content";
        if ($this->hasChildren()) {
            // print_r($this->children);
            /*
             * $left = "{$this->children->get(0)}"; $right = "{$this->children->get(1)}";
             */
            foreach ($this->children as $child) {
                $buffer .= "($child)";
            }
        }
        return trim("$buffer");
    }

    function render($spaces = "")
    {
        $buffer = "$spaces$this->content\n";
        foreach ($this->children as $child) {
            // echo "fils de $this : $child\n";
            // echo "!!! $child\n";
            
            $buffer .= $child->render("$spaces ");
        }
        return $buffer;
    }

    /**
     * Tells if $this and $token are the same type of token
     */
    function typeOf($token)
    {
        if (is_null($token))
            throw new Exception("null token provided");
        return get_class($this) == get_class($token);
    }

    function hasChildren()
    {
        return ! $this->children->isEmpty();
    }

    /**
     * Reduction function.
     * By defaut, the token is ignored/deleted
     */
    function reduce(&$tokens)
    {
        // echo "<h1>PAFO '$this'</h1>";
        $tokens->del($this);
    }

    static function cmpSticknessRevert($left, $right)
    {
        // The order is reverted!
        if ($left->stickness < $right->stickness){
            return + 1;
        }
        else{ 
            if ($left->stickness > $right->stickness){
                return - 1;
            }
            else{
                return 0;
            }
        }
    }

    /**
     * Reads every derived class and put them as objects in a list of tokens
     * This must be called when this library loads, like a (poor) singleton.
     */
    static function fillInTokens()
    {
        self::$tokenObjects = new ListOfToken();
        foreach (get_declared_classes() as $className) {
            // BUG: when a class inherits from a class that is declared later
            // the php code is okay but get_declared_classes sees nothing
            // BUG: when a class derives from IteratorAggregate, same result.
            // echo "XXX $className\n";
            if (preg_match("/^Token[A-Z]/", $className)) {
                $class = new $className();
                if ($class instanceof Token) {
                    self::$tokenObjects->add($class);
                } else {
                    unset($class);
                }
            }
        }
        
        if (self::$tokenObjects->count() == 0) {
            throw new Exception("No token model found!");
        }
        
        // print_r(self::$tokenObjects);
        self::$tokenStickness = clone self::$tokenObjects;
        // print_r(self::$tokenStickness);
        
        /*
         * echo "__________"; print_r(self::$tokenObjects);
         */
        self::$tokenStickness->sortStickness();
        // print_r(self::$tokenObjects);
        
        // echo "__________";
    }

    function checkTypesOfToken()
    {
        if (! empty($this->allowedChildren)) {
            $index = 0;
            $count = $this->children->count();
            // echo "aerazr $count\n";
            foreach ($this->allowedChildren as $childTypes) {
                $child = $this->children->get($index);
                $childType = get_class($child);
                // echo get_class($this)." enhavas '$childType'!!<br>\n";
                if (! in_array($childType, $childTypes)) {
                    throw new Exception("Type error: $childType in " . get_class($this) . ".");
                }
                if (++ $index >= $count)
                    break; // if there are optional children
            }
        }
    }

    static function staticInit()
    {
        self::fillInTokens();
    }
}
Token::staticInit();

class BinaryToken extends Token
{

    function reduce(&$tokens)
    {
        
        // $pos = array_search($this, $tokens->list);
        $pos = $tokens->pos($this);
        // print_r($pos);
        // print_r($this);
        // print_r($tokens->list);
        
        if (FALSE === $pos){
            throw new Exception("Token to reduce not found");
        }
        $left = $tokens->list[$pos - 1];
        $right = $tokens->list[$pos + 1];
        
        // echo "left";
        // print_r($left);
        // echo "right";
        // print_r($right);
        
        $this->children = new ListOfToken(array(
            $left,
            $right
        ));
        $tokens->del($left);
        $tokens->del($right);
    }
}

class UnaryToken extends Token
{

    function reduce(&$tokens)
    {
        $pos = $tokens->pos($this);
        if (FALSE === $pos)
            throw new Exception("Token to reduce not found");
        $right = $tokens->list[$pos + 1];
        
        // echo "left";
        // print_r($left);
        // echo "right";
        // print_r($right);
        
        $this->children = new ListOfToken(array(
            $right
        ));
        $tokens->del($right);
    }
}

class TokenString extends Token
{
    
    // cf Look Aheads at
    // http://www.phpro.org/tutorials/Introduction-to-PHP-Regex.html#10
    var $regexp = "(?<!\w)['\"](.*?)['\"](?!\w)";

    function __toString()
    {
        return "\"{$this->content}\"";
    }

    function render($spaces = '')
    {
        return $spaces . "\"" . $this->content . "\"\n";
    }

    function getXml($name = null)
    {
        $tokenConstant = new TokenConstant($this->content);
        return $tokenConstant->getXml();
    }
}

class AbsTokenIfElseif extends Token
{

    var $stickness = 10;

    var $reversedOrderParsing = true;

    var $allowedChildren = array(
        array(
            "TokenAnd",
            "TokenOrOrIf",
            "TokenLessEqual",
            "TokenGreaterEqual",
            "TokenNotEqual",
            "TokenEqual",
            "TokenLess",
            "TokenGreater",
            "TokenOpenParenthesis"
        ),
        array(
            "TokenThen"
        ),
        array(
            "TokenElseIf",
            "TokenElse"
        )
    );

    function getXml($name = null)
    {
        $dom = parent::getXml($name);
        
        // put a <condition> above the comparison expression
        $if = $dom->documentElement;
        $condition = $dom->createElement("condition");
        $condition->appendChild($if->firstChild);
        // /!\ this removes the firstChild of <if> to link it to <condition>
        // so, at the following line, $if->firstChild is <then> !
        $if->insertBefore($condition, $if->firstChild);
        return $dom;
    }

    function mutateAssignEqualToEqual(&$listOfToken)
    {
        $tokenAssign = new TokenAssign();
        foreach ($listOfToken as $token) {
            if ($token->typeOf($tokenAssign)) {
                $tokenEqual = new TokenEqual();
                $tokenEqual->tokenAssignImport($token);
                $listOfToken->rep($token, $tokenEqual);
                // print_r($token);
                // print_r($tokenEqual);
                // print_r($listOfToken);
                
                // $listOfToken->del($token);
            }
            $this->mutateAssignEqualToEqual($token->children);
        }
    }

    /**
     * if a child of "if" is a goto, then replace it by a then/else-goto
     */
    function correctMissingThenElse()
    {
        $childThen = $this->children->get(1);
        $childElseElseIf = $this->children->get(2);
        
        $tokenThen = new TokenThen();
        $tokenElse = new TokenElse();
        $tokenElseIf = new TokenElseIf();
        
        if (! is_null($childThen) && ! $childThen->typeOf($tokenThen)) {
            $newChild = new TokenThen();
            $newChild->children->add($childThen);
            $this->children->rep($childThen, $newChild);
        }
        if (! is_null($childElseElseIf) && ! $childElseElseIf->typeOf($tokenElse) && ! $childElseElseIf->typeOf($tokenElseIf)) {
            $newChild = new TokenElse();
            $newChild->children->add($childElseElseIf);
            $this->children->rep($childElseElseIf, $newChild);
        }
    }

    function reduce(&$tokens)
    {
        $pos = $tokens->pos($this);
        if (FALSE === $pos){
            throw new Exception("Token to reduce not found");
        }
        $condition = $tokens->get($pos + 1);
        
        $then = $tokens->get($pos + 2);
        $else = $tokens->get($pos + 3);
        
        $this->children = new ListOfToken(array(
            $condition
        ));
        
        if (! is_null($then)) {
            $this->children->add($then);
            $tokens->del($then);
        }
        
        if (! is_null($else)) {
            $this->children->add($else);
            $tokens->del($else);
        }
        $tokens->del($condition);
        
        // changes every tokenAssign to tokenEqual
        $conditionOrig = $this->children->get(0);
        $listOfToken = new ListOfToken(array(
            $conditionOrig
        ));
        $this->mutateAssignEqualToEqual($listOfToken);
        $conditionNew = $listOfToken->get(0);
        $this->children->rep($conditionOrig, $conditionNew);
        $this->correctMissingThenElse();
    }
}

class TokenElseIf extends AbsTokenIfElseif
{

    var $regexp = "\belse\s*if\b";
    // var $stickness = 21; // to have "elseif" tested before "else" and "if"
    var $content = "elseif";

    var $stickness = 15;

    function getXml($name = null)
    {
        $dom = parent::getXml();
        
        $this->renameNode($dom, $dom->documentElement, "if");
        
        // putting <else> above
        $else = $dom->createElement("else");
        $dom->appendChild($else);
        $else->appendChild($dom->documentElement);
        
        return $dom;
    }
}

class AbsTokenGarbage extends Token
{

    var $stickness = 110;
}

class TokenEndIf extends AbsTokenGarbage
{

    var $regexp = "\bend\s*if\b";

    var $content = "endif";
}

class TokenAnd extends BinaryToken
{

    var $stickness = 40;

    var $content = "and";

    var $regexp = "\band\b";

    var $allowedChildren = array(
        array(
            "TokenAnd",
            "TokenOrOrIf",
            "TokenLessEqual",
            "TokenGreaterEqual",
            "TokenNotEqual",
            "TokenEqual",
            "TokenLess",
            "TokenGreater",
            "TokenOpenParenthesis"
        ),
        array(
            "TokenAnd",
            "TokenOrOrIf",
            "TokenLessEqual",
            "TokenGreaterEqual",
            "TokenNotEqual",
            "TokenEqual",
            "TokenLess",
            "TokenGreater",
            "TokenOpenParenthesis"
        )
    );

    function getXml($name = null)
    {
        return parent::getXmlOperator("and");
    }
}

class TokenOrOrIf extends BinaryToken
{

    var $stickness = 30;
    // var $content = "or";
    // var $regexp = "\b(or)\b";
    // var $regexp = "\bor(?:\s*if)?\b";
    var $regexp = "\b(or if)\b|\bor\b";
    // var $regexp = "\bor\s*if\b";
    var $allowedChildren = array(
        array(
            "TokenAnd",
            "TokenOrOrIf",
            "TokenLessEqual",
            "TokenGreaterEqual",
            "TokenNotEqual",
            "TokenEqual",
            "TokenLess",
            "TokenGreater",
            "TokenOpenParenthesis"
        ),
        array(
            "TokenAnd",
            "TokenOrOrIf",
            "TokenLessEqual",
            "TokenGreaterEqual",
            "TokenNotEqual",
            "TokenEqual",
            "TokenLess",
            "TokenGreater",
            "TokenOpenParenthesis"
        )
    );

    function getXml($name = null)
    {
        return parent::getXmlOperator("or");
    }
}

class TokenIf extends AbsTokenIfElseif
{

    var $regexp = "\bif\b";

    var $content = "if";

    function getXml($name = null)
    {
        return parent::getXml("if");
    }
}

class AbsTokenThenElse extends UnaryToken
{

    var $stickness = 20;

    var $allowedChildren = array(
        array(
            "TokenGoto",
            "TokenAssign"
        )
    );
}

class TokenThen extends AbsTokenThenElse
{

    var $content = "then";

    var $regexp = "\bthen\b";

    function getXml($name = null)
    {
        return parent::getXml("then");
    }
}

class TokenElse extends AbsTokenThenElse
{

    var $content = "else";
    
    // WORKAROUND: the first \b missing due to 999else in the master
    var $regexp = "else\b";

    var $allowedChildren = array(
        array(
            "TokenGoto",
            "TokenAssign",
            "TokenIf"
        )
    );

    function getXml($name = null)
    {
        return parent::getXml("else");
    }
}

class TokenGoto extends UnaryToken
{

    var $stickness = 80;

    var $regexp = "\bgo\s*to\b";

    var $content = "goto";

    var $allowedChildren = array(
        array(
            "TokenVariable"
        )
    );

    function getXml($name = null)
    {
        $dom = new DOMDocument();
        $node = $dom->createElement("goto");
        $dom->appendChild($node);
        // echo $this->children->get(0)->content." !!x!!\n";
        $node->setAttribute("itemGroup", $this->children->get(0)->content);
        return $dom;
    }
}

class TokenVariable extends Token
{
    
    // /!\ "^variable" and "^ variable" are correct
    var $regexp = "[A-Z]_[A-Z0-9_]+|\^\s*([A-Z0-9_]+)|\bRANDOM\b|\bdirect assessment\b";

    function getXml($name = null)
    {
        $dom = new DOMDocument();
        $node = $dom->createElement("variable");
        $dom->appendChild($node);
        $node->setAttribute("name", $this->content);
        return $dom;
    }
}

class TokenConstant extends Token
{

    var $regexp = "[0-9]+(?:\.[0-9]+)?|\bDK\b|\bRF\b|\bNULL\b|\bNI\b";
    // var $regexp = "[0-9]+(?:\.[0-9]+)?|\bDK\b|\bRF\b|\bNULL\b|(?<!\w)['\"](.*?)['\"](?!\w)"; //NOTICE: strings things are also constant
    function getXml($name = null)
    {
        $dom = new DOMDocument();
        $node = $dom->createElement("constant");
        $dom->appendChild($node);
        $node->appendChild($dom->createTextNode($this->content));
        return $dom;
    }
    
    // function tokenStringImport($token) {
    // if (is_numeric($token->content)) {
    // $this->content = $token->content;
    // } else {
    // return false;
    // }
    // }
}

class TokenLessEqual extends BinaryToken
{

    var $stickness = 50;

    var $regexp = "<=|=<";

    var $allowedChildren = array(
        array(
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        ),
        array(
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        )
    );

    function getXml($name = null)
    {
        return parent::getXmlOperator("lessEqual");
    }
}

class TokenGreaterEqual extends BinaryToken
{

    var $stickness = 50;

    var $regexp = ">=|=>";

    var $allowedChildren = array(
        array(
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        ),
        array(
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        )
    );

    function getXml($name = null)
    {
        return parent::getXmlOperator("greaterEqual");
    }
}

class TokenNotEqual extends BinaryToken
{

    var $stickness = 50;

    var $regexp = "!=|<>";

    var $allowedChildren = array(
        array(
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        ),
        array(
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        )
    );

    function getXml($name = null)
    {
        return $this->getXmlOperator("notEqual");
    }
}

class TokenEqual extends BinaryToken
{

    var $stickness = 50;

    var $regexp = "==";

    var $content = "==";

    var $allowedChildren = array(
        array(
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        ),
        array(
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        )
    );

    /**
     * turns an assign into an equal
     */
    function tokenAssignImport($tokenAssign)
    {
        // print_r($tokenAssign);
        $left = $tokenAssign->children->get(0);
        $right = $tokenAssign->children->get(1);
        $this->children->add($left);
        $this->children->add($right);
    }

    function getXml($name = null)
    {
        return $this->getXmlOperator("equal");
    }
}

class TokenAssign extends BinaryToken
{

    var $stickness = 50;

    var $regexp = ":=|=";

    var $content = ":=";

    var $allowedChildren = array(
        array(
            "TokenVariable"
        ),
        array(
            "TokenConcat",
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        )
    );

    function getXml($name = null)
    {
        return parent::getXml("assignment");
    }
}

class TokenLess extends BinaryToken
{

    var $stickness = 50;

    var $regexp = "<";

    var $allowedChildren = array(
        array(
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        ),
        array(
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        )
    );

    function getXml($name = null)
    {
        return parent::getXmlOperator("less");
    }
}

class TokenGreater extends BinaryToken
{

    var $stickness = 50;

    var $regexp = ">";

    var $allowedChildren = array(
        array(
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        ),
        array(
            "TokenString",
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        )
    );

    function getXml($name = null)
    {
        return parent::getXmlOperator("greater");
    }
}

class TokenPlus extends BinaryToken
{

    var $stickness = 60;

    var $regexp = "\+";

    var $allowedChildren = array(
        array(
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        ),
        array(
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        )
    );

    function getXml($name = null)
    {
        return parent::getXmlOperator("+");
    }
}

class TokenMinus extends BinaryToken
{

    var $stickness = 60;

    var $regexp = "\-";

    var $allowedChildren = array(
        array(
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        ),
        array(
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        )
    );

    function getXml($name = null)
    {
        return parent::getXmlOperator("-");
    }
}

class TokenTimes extends BinaryToken
{

    var $stickness = 70;

    var $regexp = "\*";

    var $allowedChildren = array(
        array(
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        ),
        array(
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        )
    );

    function getXml($name = null)
    {
        return parent::getXmlOperator("*");
    }
}

class TokenDivide extends BinaryToken
{

    var $stickness = 70;

    var $regexp = "\/";

    var $allowedChildren = array(
        array(
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        ),
        array(
            "TokenVariable",
            "TokenConstant",
            "TokenPlus",
            "TokenMinus",
            "TokenTimes",
            "TokenDivide"
        )
    );

    function getXml($name = null)
    {
        return parent::getXmlOperator("/");
    }
}

class TokenConcat extends BinaryToken
{

    var $stickness = 70;

    var $regexp = "&";

    var $allowedChildren = array(
        array(
            "TokenVariable",
            "TokenConstant",
            "TokenString",
            "TokenConcat"
        ),
        array(
            "TokenVariable",
            "TokenConstant",
            "TokenString",
            "TokenConcat"
        )
    );

    function getXml($name = null)
    {
        return parent::getXmlOperator(".");
    }
}

class TokenOpenParenthesis extends Token
{

    var $stickness = 100;

    var $regexp = "\(|\[";

    function reduce(&$tokens)
    {
        $level = 1;
        $tokenCloseParenthesis = new TokenCloseParenthesis();
        $begin = 1 + $tokens->pos($this); // first token after the parenthesis
                                        
        // echo "REDUCE $tokens @$begin\n";
        /*
         * echo "call\n"; print_r($tokens);
         */
        for ($cursor = $begin; $cursor < $tokens->count(); $cursor ++) {
            $token = $tokens->get($cursor);
            // echo "@ '$token'\n";
            // echo "@ ".get_class($token)." - '".get_class($this)."'\n";
            if ($token->typeOf($this)) {
                $level ++;
                // echo "i+\n";
            } else 
                if ($token->typeOf($tokenCloseParenthesis)) {
                    $level --;
                    // echo "i-\n";
                }
            if (0 == $level) {
                $begin --; // $begin is at the position of the opening parenthesis
                $end = $cursor; // $end is at the position of the closing parenthesis
                                
                // echo "'$begin' '$end'\n";
                                // $toto = $tokens->subSet($begin, $end);
                
                /*
                 * print_r($toto); exit();
                 */
                $analyser = new Analyser();
                $subReduce = $analyser->analyse($tokens->subSet($begin + 1, $end - 1));
                // exit();
                $tokens->delSet($begin, $end, $subReduce);
                return;
            }
        }
        
        // TODO: tell which was the opening parenthesis
        throw new Exception("Closing parenthesis not found");
    }
}

class TokenCloseParenthesis extends Token
{

    var $regexp = "\)|\]";
}

class TokenCrap extends AbsTokenGarbage
{

    var $regexp = "[,;.]";
}

class TokenExpr extends Token
{

    var $stickness = 0; // to be read the last
    var $content = "EXPR";

    function reduce(&$tokens)
    {
        $pos = $tokens->pos($this);
        $right = $tokens->list[$pos + 1];
        $this->children->add($right);
        $tokens->del($right);
        // echo "LISTO $tokens<br>\n";
        try {
            $this->checkTypes($tokens);
        } catch (Exception $e) {
            // echo "Type error in $tokens\n";
            throw new Exception("Type error in $tokens.\n" . $e->getMessage());
        }
    }

    /**
     * Checks the types in tokens
     */
    function checkTypes($tokens)
    {
        if (empty($tokens))
            return;
        foreach ($tokens as $token) {
            switch (get_class($token)) {
                case "TokenString":
                    // Turns 1+"2" into 1+2 to pass the type check
                    if (is_numeric($token->content)) {
                        $newToken = new TokenConstant(floatval($token->content));
                        $tokens->rep($token, $newToken);
                        $token = $newToken;
                    }
                    break;
            }
            $this->checkTypes($token->children);
            $token->checkTypesOfToken();
        }
    }

    function getXml($name = null)
    {
        // takes the XML of the child, not that of this token, which is no sense
        $dom = $this->children->get(0)->getXml();
        
        $assign = new TokenAssign();
        if ("if" != $dom->documentElement->tagName) {
            $xmlText = "
        <if>
          <condition>
            <operator type=\"equal\">
              <constant>true</constant>
              <constant>true</constant>
            </operator>
          </condition>
          <then/>
        </if>
      ";
            $fakeDom = new DOMDocument();
            $fakeDom->loadXML($xmlText);
            $then = $fakeDom->getElementsByTagName("then")->item(0);
            $then->appendChild($fakeDom->importNode($dom->documentElement, true));
            $dom = $fakeDom;
        }
        
        // guesses the type of the conditional expression
        $xpath = new DOMXpath($dom);
        if ($xpath->query("//goto")->length > 0) {
            $keyWord = "routing";
        } else {
            if ($xpath->query("//assignment")->length > 0) {
                $keyWord = "inferenceRule";
            } else 
                if (! $xpath->query("//then")->length > 0) {
                    $keyWord = "consistencyCheck";
                } else {
                    throw new Exception("Unable to determine the type of conditional expression '$this'");
                }
        }
        
        $elements = $xpath->query("//if");
        
        foreach ($elements as $element) {
            $this->renameNode($dom, $element, $keyWord);
        }
        
        return $dom;
    }
}

class Tokenizer
{

    static $regexp;

    /**
     * Builds the global regular expression
     * Must be called once when the scripts loads
     */
    static function setRegexp()
    {
        $regexp = "";
        foreach (Token::$tokenObjects as $tokenObject) {
            // echo "hou ";
            $reg = $tokenObject->regexp;
            $regexp .= "|{$reg}";
        }
        self::$regexp = substr($regexp, 1); // removes the first pipe
    }

    /**
     * Create an array containing the tokens based on $expression
     */
    function tokenize($expression)
    {
        preg_match_all(
            // "is" stands for case Insensitive and on many lineS
            "@" . self::$regexp . "@is", $expression, $tokens, PREG_SET_ORDER);
        
        // echo "<p>".self::$regexp."</p>";
        // echo "<p>$expression</p>";
        // echo "<pre>[";print_r($tokens);echo "]</pre>";
        // exit();
        
        $notRead = trim(preg_replace("@" . self::$regexp . "@is", "_", $expression));
        $notReadToTest = trim(preg_replace("/_/", "", $notRead));
        
        if (! empty($notReadToTest)) {
            // echo $notReadToTest." ççççççççççççççççççç\n";
            throw new Exception("Syntax error: '$notRead' in '$expression'");
        }
        
        $tokenList = new ListOfToken();
        $tokenList->add(new TokenExpr()); // very first token, for post-processing
        
        foreach ($tokens as $token) {
            
            // Distingish between "^myVariable" and "myVariable"
            // First in at [0] at the second at [1]
            $completeToken = $token[0]; // with syntaxic decoration
            $token = $token[count($token) - 1]; // the token itself
            
            $tokenElement = null;
            foreach (Token::$tokenObjects as $tokenObject) {
                $regexp = $tokenObject->regexp;
                // print "testing $regexp $token\n";
                if (preg_match("@$regexp@is", $completeToken)) {
                    $tokenElement = new $tokenObject($token);
                    break;
                }
            }
            if (is_null($tokenElement)){
                // FIXME: useful?
                throw new Exception("Internal error: '$token' not recognized");
            }
            else{
                $tokenList->add($tokenElement);
            }
        }
        // echo "Vidu '$tokenList'<br>\n";
        return $tokenList;
    }

    function __toString()
    {
        return self::$regexp;
    }
}
Tokenizer::setRegexp();

class Analyser
{

    static function analyse($expression)
    {
        if (is_string($expression)) {
            $tokenizer = new Tokenizer();
            $tokens = $tokenizer->tokenize($expression);
        } else { // a yet formed ListOfToken
            $tokens = $expression;
        }
        
        // echo "Testaĵo unu: '$tokens'<br>";
        
        foreach (Token::$tokenStickness as $operator) {
            // echo "Testaĵo du: '$tokens'<br>";
            if ($operator->reversedOrderParsing) {
                $tokensToParse = $tokens->reverse();
            } else {
                $tokensToParse = $tokens;
                // echo "Testaĵo tri: '$tokens'<br>";
            }
            do {
                $operatorFound = false;
                foreach ($tokensToParse as $token) {
                    // echo "Testaĵo tri kaj unu: '$tokens'<br>";
                    if ($token->typeOf($operator) && ! $token->hasChildren()) {
                        $operatorFound = true;
                        // echo "Elektita ".get_class($operator)."<br>";
                        // echo "$tokens [$token] videbla antauxen !!<br>\n";
                        $token->reduce($tokens);
                        // echo "$tokens videbla posten !!<br>\n";
                        break; // redo the foreach because tokens changed
                    }
                    // echo "Testaĵo tri kaj du: '$tokens'<br>";
                }
                // echo "Testaĵo kvar: '$tokens'<br>";
            } while ($operatorFound);
        }
        // TODO: catch the error when it occurs
        // e.g. "1+2)*3" gives two roots "+(1)(2) *())(3)"
        // in fact, the "times" should not have a parenthesis as a child
        if ($tokens->count() > 1) {
            throw new Exception("Analysis error '{$tokens}'\nhas more than one root");
            // echo "Analysis notice '{$tokens}' has more than one root\n";
            // echo "Analysis notice, there's more than one root\nSee Tokenizer!\n";
            // we keep only the first element
            $tokens->list = array_slice($tokens->list, 0, 1);
        }
        return $tokens;
    }
}

class ConditionalExpression
{

    const EDIT = 1;

    const DERIVED_VARIABLE = 2;

    const ROUTING = 3;

    const DYNAMIC_TEXT = 4;
}

/*
 * var $allowedChildren = array( array( "TokenString", "TokenElseIf", "TokenEndIf", "TokenCrap", "TokenAnd", "TokenOrOrIf", "TokenIf", "TokenThen", "TokenElse", "TokenGoto", "TokenVariable", "TokenConstant", "TokenLessEqual", "TokenGreaterEqual", "TokenNotEqual", "TokenEqual", "TokenAssign", "TokenLess", "TokenGreater", "TokenPlus", "TokenMinus", "TokenTimes", "TokenDivide", "TokenConcat", "TokenOpenParenthesis", "TokenCloseParenthesis", "TokenExpr", "Tokenizer" ), array( "TokenString", "TokenElseIf", "TokenEndIf", "TokenCrap", "TokenAnd", "TokenOrOrIf", "TokenIf", "TokenThen", "TokenElse", "TokenGoto", "TokenVariable", "TokenConstant", "TokenLessEqual", "TokenGreaterEqual", "TokenNotEqual", "TokenEqual", "TokenAssign", "TokenLess", "TokenGreater", "TokenPlus", "TokenMinus", "TokenTimes", "TokenDivide", "TokenConcat", "TokenOpenParenthesis", "TokenCloseParenthesis", "TokenExpr", "Tokenizer" ) );
 */

?>