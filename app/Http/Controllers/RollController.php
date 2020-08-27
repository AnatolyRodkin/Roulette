<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Winning;
use App\User;
use Carbon\Carbon;

class RollController extends Controller
{
    public function saveResult($name, $money_amount){
        $user = User::all()->where('name', $name)->first(); //пользователь с именем name
        $win = new Winning; //экземпляр модели выигрышей
        $win->user_id = $user->id; //задаем айди пользователя для новой записи в таблице выигрышей
        $win->money = $money_amount; //задаем количество выигранных за roll денег
        $win->save(); //добавляем запись в таблицу
        echo 'You are win '.$money_amount.' rub.'; //выводим клиенту результат
    }

    public function roll($name){
        // $tmp = rand(1,3);
        $tmp = 1; //1 - выпадут деньги, 2 - выпадут бонусы, 3 - выпадет физ. вещь
        $money_amount = 0; //количество денег (расчитывается дальше)
        //ниже в каждом кейсе пишется логика выигрыша разных видов призов
        switch ($tmp) {
            case 1: //логика выигрыша денег
                $money_amount = rand(50,1000);
                break;
            case 2: //логика выигрыша бонусов (не описана)
                echo "i равно 2";
                break;
            case 3: //логика выигрыша физ. вещи (не описана)
                echo "i равно 3";
                break;
        }
        $this->saveResult($name, $money_amount); //вызов функции сохранения результата roll'а
        return;
    }

    public function index(){
        $allow_roll_time = 172800; //время (в секундах) с момента последней прокрутки, через которое пользователь может крутить рулетку
        $name = (string)$_REQUEST['name']; //получаем имя пользователя, который нажал на кнопку ROLL (не уверен на счет безопасности данного способа)
        $diff = (int)$this->timeCheck($name);
        if($diff > $allow_roll_time) //проверяем, прошло ли заданное время с момента последнего roll'а
        $this->roll($name); //если да
        else
        {
            $tmp = (int)($allow_roll_time - $diff); //считаем, сколько осталось времени до след roll'а
            echo 'You can roll again in '.$this->formatTime($tmp).'.'; //вывод оставшегося до roll'а времени
        }
    }

    public function timeCheck($name){
        $user = User::all()->where('name', $name)->first(); //пользователь с именем name
        $current_time = Carbon::now(); //текущее время
        $last_win = Winning::all()->where('user_id', $user->id)->last(); //последний выигрыш
        $last = new Carbon($last_win->created_at); //время и дата последнего выигрыша
        $diff = $last->diff($current_time); //разница м/у последним roll'ом и временем сейчас
        $diff_s = (int)$last->diffInSeconds($current_time); //то же, что и выше, но в секундах (int)
        $diff_format = $diff->format('%h:%I:%s'); //что и выше, отформатировано
        return $diff_s; //
    }

    public function formatTime($sec){ //получаем время в секундах и форматируем для вывода
        $result = ''; //строк результата форматирования
        if($sec > 2592000){ //больше, чем месяц
            $tmp = intdiv($sec, 2592000);
            $result .= $tmp.' month ';
            $sec = $sec%2592000;
        }
        if($sec > 86400){ //больше, чем день
            $tmp = intdiv($sec, 86400);
            if($tmp<2)
                $result .= $tmp.' day and ';
            else
                $result .= $tmp.' days and ';
            $sec = $sec%86400;
        }
        if($sec > 3600){ //больше, чем час
            $tmp = intdiv($sec, 3600);
            if($tmp<10)
                $result .= '0'.$tmp;
            else
                $result .= $tmp.':';
            $sec = $sec%3600;
        }
        if($sec > 60){ //больше, чем минута
            $tmp = intdiv($sec, 60);
            if($tmp<10)
                $result .= '0'.$tmp;
            else
                $result .= $tmp.':';
            $sec = $sec%60;
        }
        //вывод секунд
        if($sec<10)
            $result .= '0'.$sec;
        else
            $result .= $sec;
        return $result;
    }
}
