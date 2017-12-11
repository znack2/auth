<?php 

namespace App\Repository;

use Carbon\Carbon;

abstract class AbstractRepository
{
    public $categories;
    public $now;
    public $user;

    function __construct()
    {
        $this->now = new Carbon; //date('Y-m-d H:i:s')
//        $this->user = \Auth::user()->user();
    }
}