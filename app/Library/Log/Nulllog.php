<?php
namespace App\Library\Log;


use App\Library\Log\Contracts\Log;
use Illuminate\Http\Request;


class Nulllog implements Log
{
      public function save($data)
      {
          echo "Null log";
      }

      public function format($detail,$table,Request $request)
      {

      }
      public function show($name)
      {
          echo "Null log show";
      }
}