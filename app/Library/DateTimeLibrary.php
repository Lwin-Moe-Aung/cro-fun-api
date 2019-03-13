<?php
namespace App\Library;
use Illuminate\Support\Facades\Input;
use DB;

class DateTimeLibrary
{
    /*
     *Convert the date given from UI as "dd/mm/yyyy" to "Y-m-d H:i:s" for MySQL
    */
    public static function getMySQLDateTimeFromUIDate($dateValue)
    {

        $day = substr($dateValue,0,2);
        $month = substr($dateValue,3,3);
        $year = substr($dateValue,7,4);
        $formattedDateValue = $year . "-" . $month . "-" . $day . " 00:00:00";
        $timestamp = strtotime($formattedDateValue);
        $mySqlDate = date("Y-m-d H:i:s", $timestamp);
        return $mySqlDate;

    }


    /*
     * Convert the mysql date to UI date format (dd/mm/yyyy)
     */
    public static function changeDateFormat($date)
    {

        $dob = substr($date, 0, 10);
        $dob = date("d-M-Y", strtotime($dob));
        return $dob;

    }


    /*
     * This is function of changing date format of the date fields of the system
     */
    public static function changeInputDateFormat()
    {
        if (Input::get('dob')) {

            $date = DateTimeLibrary::getMySQLDateTimeFromUIDate(Input::get('dob'));
            Input::merge(['dob' => $date]);
        }
        if (Input::get('fund_closing_date')) {

            $date = DateTimeLibrary::getMySQLDateTimeFromUIDate(Input::get('fund_closing_date'));
            Input::merge(['fund_closing_date' => $date]);
        }
        if (Input::get('project_start_date')) {

            $date = DateTimeLibrary::getMySQLDateTimeFromUIDate(Input::get('project_start_date'));
            Input::merge(['project_start_date' => $date]);
        }
        if (Input::get('project_end_date')) {

            $date = DateTimeLibrary::getMySQLDateTimeFromUIDate(Input::get('project_end_date'));
            Input::merge(['project_end_date' => $date]);
        }
        if (Input::get('payment_date')) {

            $date = DateTimeLibrary::getMySQLDateTimeFromUIDate(Input::get('payment_date'));
            Input::merge(['payment_date' => $date]);
        }
        if (Input::get('profit_generated_date')) {

            $date = DateTimeLibrary::getMySQLDateTimeFromUIDate(Input::get('profit_generated_date'));
            Input::merge(['profit_generated_date' => $date]);
        }
        if (Input::get('profit_paid_date')) {

            $date = DateTimeLibrary::getMySQLDateTimeFromUIDate(Input::get('profit_paid_date'));
            Input::merge(['profit_paid_date' => $date]);
        }
        if (Input::get('progress_date')) {

            $date = DateTimeLibrary::getMySQLDateTimeFromUIDate(Input::get('progress_date'));
            Input::merge(['progress_date' => $date]);
        }

    }
    public static function searchCodeNo($table,$prefix)
    {
        $data = DB::table($table)
            ->select('code_no')
            ->orderBy('code_no', 'desc')
            ->limit('1')
            ->get();
        if(count($data)>0)
        {
            $code = $data[0]->code_no;
            $number = substr($code, strpos($code, $prefix) + 1);
            $new_number = intval($number) + 1;
            return str_pad($new_number, 6, "0", STR_PAD_LEFT);
        }
        return "000001";

    }


}