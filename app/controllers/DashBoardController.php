<?php

class DashBoardController extends Controller
{

    function render() {

        if($this->f3->get('SESSION.logged_in'))
        {
            $mobilities = new Mobilities($this->db);
            if($this->f3->get('SESSION.role')){
                $latestMobility = $mobilities->getLatestMobility();
            }
            else{
                $latestMobility = $mobilities->getLatestMobility($this->f3->get('SESSION.id'));
            }

            if($latestMobility){
                $student = $latestMobility->getStudent();
                $this->f3->set('LM_EXISTS',true);
                $this->f3->set('LM_AVATAR',$student->image_path);
                $this->f3->set('LM_NAME',$student->name.' '.$student->surname);
                $this->f3->set('LM_CITY',$latestMobility->getCity());
                $this->f3->set('LM_COUNTRY',$latestMobility->getCountry());
                $this->f3->set('LM_STATUS',$latestMobility->status);
                $this->f3->set('LM_SUBMISSION_DATE',Utils::timeAgo($latestMobility->submission_date));
                $this->f3->set('LM_ID',$latestMobility->id);
            }else{
                $this->f3->set('LM_EXISTS',false);
            }

            $this->f3->set('view','dashboard.htm');
        }
        else
        {
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    function mobilitiesMap(){
        if($this->f3->get('SESSION.logged_in')){
            $mobilities = new Mobilities($this->db);
            $mb = $mobilities->allValid();
            $data = array();
            foreach($mb as $mbv){
                $student = $mbv->getStudent();
                $data[] = array(
                    "studentName" => $student->name,
                    "studentSurname" => $student->surname,
                    "imagePath" => $student->image_path,
                    "city" => $mbv->getCity(),
                    "country" => $mbv->getCountry(),
                    "coordinates" => $mbv->getCoordinates(),
                    "school" => $mbv->getSchool(),
                    "programTitle" => $mbv->getProgramTitle(),
                    'startDate' => $mbv->getStartDate()
                );
            }
            $this->f3->set('data',json_encode($data));
            $this->f3->set('view','map.htm');
        }else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }
}