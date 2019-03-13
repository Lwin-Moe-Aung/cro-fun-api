<?php
namespace App\Library\Log\Contracts;
use Illuminate\Http\Request;

interface Log
{

  public function save($data);
  public function show($name);
  public function format($detail,$table,Request $request);

}