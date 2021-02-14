<?php


class Utils {

    public static function timeAgo($time_ago)
    {
        $time_ago = strtotime($time_ago);
        $cur_time   = strtotime('now');
        $time_elapsed   = $cur_time - $time_ago;
        $seconds    = $time_elapsed ;
        $minutes    = round($time_elapsed / 60 );
        $hours      = round($time_elapsed / 3600);
        $days       = round($time_elapsed / 86400 );
        $weeks      = round($time_elapsed / 604800);
        $months     = round($time_elapsed / 2600640 );
        $years      = round($time_elapsed / 31207680 );
        //Futur
        if($seconds < 0){
            return "À venir";
        }
        // Seconds
        else if($seconds <= 60){
            return "À l'instant";
        }
        //Minutes
        else if($minutes <=60){
            if($minutes==1){
                return "Il y a une minute";
            }
            else{
                return "Il y a $minutes minutes";
            }
        }
        //Hours
        else if($hours <=24){
            if($hours==1){
                return "Il y a une heure";
            }else{
                return "Il y a $hours heures";
            }
        }
        //Days
        else if($days <= 7){
            if($days==1){
                return "Hier";
            }else{
                return "Il y a $days jours";
            }
        }
        //Weeks
        else if($weeks <= 4.3){
            if($weeks==1){
                return "Il y a une semaine";
            }else{
                return "Il y a $weeks semaine";
            }
        }
        //Months
        else if($months <=12){
            if($months==1){
                return "Il y a un mois";
            }else{
                return "Il y a $months mois";
            }
        }
        //Years
        else{
            if($years==1){
                return "Il y a un an";
            }else{
                return "Il y a $years ans";
            }
        }
    }
}
