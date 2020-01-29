<?php

include_once "lib/helper.php";
include_once "lib/globals.php";


if(!$_POST)
{
    header("HTTP/1.0 404 Not Found");
    echo "Invalid Request Sent!!";
    die();
}

/**
 * return Error if the parameters is not
 * available on current request
 */


$request = ajax::post_strict([
    "operation",
    "x",
    "y"
],false);


abstract class operator
{
    protected $x =0;
    protected $y = 0;

    public function __construct(int $x, int $y)
    {

        $this->x = $x ? $x : 0;
        $this->y = $y ? $y : 0;
    }

    public function clean()
    {
        return 0;
    }

}



class addition extends operator
{
    public function init()
    {
        return $this->x + $this->y;
    }

}


class substraction extends operator
{
    public function init()
    {
        return $this->x - $this->y;
    }
}


class multiplacation extends operator
{
    public function init()
    {
        return $this->x * $this->y;
    }
}


class division extends operator
{

    public function clean()
    {
        if($this->y > 0) return;

        echo "Cannot devide by zero!!";
        exit();
    }


    public function init()
    {
        return $this->x / $this->y;
    }
}

class modulo extends operator
{

    public function clean()
    {
        if($this->y > 0) return;

        echo "Cannot mod by 0";
        exit();
    }

    public function init()
    {
        return $this->x % $this->y;
    }
}




/** process the request */


if(!class_exists($request["operation"]))
{
    header("HTTP/1.0 404 Not Found");
    echo "Invalid operator!";
    die();
}


$operator = new $request["operation"]($request['x'] , $request['y']);
$operator->clean();
echo $operator->init();




