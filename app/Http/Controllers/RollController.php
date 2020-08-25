<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Winning;
use App\User;
use Carbon\Carbon;

class RollController extends Controller
{


    public function saveResult($name, $money_amount){
        $user = User::all()->where('name', $name)->first();
        $win = new Winning;
        $win->user_id = $user->id;
        $win->money = $money_amount;
        $win->save();
        echo 'You are win '.$money_amount.' rub';
    }

    public function roll($name){
        // $tmp = rand(1,3);
        $tmp = 1;
        $money_amount = 0;
        switch ($tmp) {
            case 1:
                $money_amount = rand(50,1000);
                break;
            case 2:
                echo "i равно 1";
                break;
            case 3:
                echo "i равно 2";
                break;
        }
        $this->saveResult($name, $money_amount);
        return;
    }

    public function index(){
        $name = (string)$_REQUEST['name'];
        if($this->timeCheck($name))
        $this->roll($name);
    }

    public function timeCheck($name){
        $user = User::all()->where('name', $name)->first();
        $id = $user->id;
        $current = Carbon::now();
        $last_win = Winning::all()->where('user_id', $id)->last();
        $last = new Carbon($last_win->created_at);
        $result = $last->diff($current)->format('%h:%I:%s');
        echo $result;
        return true;
    }
}
