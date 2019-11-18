index.php解析开始
<?php
class Read {
    private $var;
    public function file_get($value)
    {
        $text = base64_encode(file_get_contents($value));
        return $text;
    }

    public function __invoke(){
        $content = $this->file_get($this->var);
        echo $content;
    }
    public function __construct()
    {
        $this->var="flag";
    }
}

class Show
{
    public $source;
    public $str;
    public function __construct($file='index.php')
    {
        $this->source = $file;
        echo $this->source.'解析开始'."<br>";
    }

    public function __toString()
    {
        $this->str['str']->source;
    }

    public function _show()
    {
        if(preg_match('/http|https|file:|gopher|dict|\.\.|flag/i',$this->source)) {
            die('hacker!');
            //flag in /flag
        } else {
           highlight_file($this->source);
        }

    }

    public function __wakeup()
    {
        if(preg_match("/http|https|file:|gopher|dict|\.\./i", $this->source)) {
            echo "hacker~";
            $this->source = "index.php";
        }
    }
}

class Test
{
    public $params;
    public function __construct()
    {
        $this->params = array();
    }

    public function __get($key)
    {
        $func = $this->params;
        return $func();
    }
}

if(isset($_GET['chal']))
{
    $chal = unserialize($_GET['chal']);
}
else
{
    $show = new Show('index.php');
    $show->_show();
}
?> 