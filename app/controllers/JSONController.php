<?php


class JSONController extends Controller
{
    //Utilisé pour envoyer à la view des données JSON uniquement;

    function afterroute() {
        ;
    }

    function getSchoolsByCountry() {

        if($this->f3->get('SESSION.logged_in')){

            $destinations = new Destinations($this->db);
            $country = $this->f3->get('GET.country');
            if($country){
                $destinationsArray = $destinations->getByCountry($country);
                $response = array("status"=>"ok","data"=>array());
                foreach($destinationsArray as $destination){
                    $response["data"][] = array("id"=>$destination->id,"school"=>$destination->school,"city"=>$destination->city);
                }
            }
        }else{
            $this->f3->clear('SESSION');
            $response = array("status"=>"fail","error"=>"user not connected");
        }
        echo json_encode($response);
    }

    function getProgramsByDestination(){
        if($this->f3->get('SESSION.logged_in')){

            $programs = new Programs($this->db);
            $destination_id = $this->f3->get('GET.destination_id');
            if($destination_id){
                $programsArray = $programs->getByDestinationIDActive($destination_id);
                $response = array("status"=>"ok","data"=>array());
                foreach($programsArray as $program){
                    $response["data"][] = array("id"=>$program->id,"title"=>$program->title);
                }
            }
        }else{
            $this->f3->clear('SESSION');
            $response = array("status"=>"fail","error"=>"user not connected");
        }
        echo json_encode($response);
    }

    function getAllValidMobilities(){
        if($this->f3->get('SESSION.logged_in')){
            $mobilities = new Mobilities($this->db);
            $mb = $mobilities->allValid();
            $response = array();
            foreach($mb as $mbv){
                $student = $mbv->getStudent();
                $response[json_encode($mbv->getCoordinates())][] = array(
                    "studentName" => $student->name,
                    "studentSurname" => $student->surname,
                    "imagePath" => $student->image_path,
                    "city" => $mbv->getCity(),
                    "country" => $mbv->getCountry(),
                    "coordinates" => $mbv->getCoordinates(),
                    "school" => $mbv->getSchool(),
                    "programTitle" => $mbv->getProgramTitle(),
                    'startDate' => $mbv->getStartDate(),
                    'endDate' => $mbv->getEndDate()
                );
            }
        }else{
            $this->f3->clear('SESSION');
            $response = array("status"=>"fail","error"=>"user not connected");
        }
        echo json_encode($response);
    }
}