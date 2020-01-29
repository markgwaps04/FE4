<?php

include_once "ofDb.php";


/**
 * Created by PhpStorm.
 * User: Sunlee
 * Date: 1/15/2019
 * Time: 12:59 AM
 */


final class database extends OfDbSql\OfDbSql
{


    protected $database = null;


    public function __construct()
    {

        $this->database = parent::__construct([

            'database_type' => 'mysql',
            'database_name' => 'cspmo',
            'charset' => 'utf8',
            'server' => 'localhost',
            'username' => 'root',
	    'password' => '',


        ],true);


        return $this->database;

    }

    /**
     * @return database
     */

    public static function getInstance()
    {

        return new self();

    }


}