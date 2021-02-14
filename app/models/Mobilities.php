<?php


class Mobilities extends \DB\SQL\Mapper
{

    private $users;
    private $programs;
    const EXACT_DATE = 0;
    const LATER_THAN = 1;
    const EARLIER_THAN = 2;

    public function __construct(DB\SQL $db) {
        parent::__construct($db,'mobilities');
        $this->users = new Users($this->db);
        $this->programs = new Programs($this->db);
    }

    public function all() {
        $this->load(null,array('order'=>'submission_date DESC'));
        return $this->query;
    }

    public function allValid() {
        $this->load('status=1');
        return $this->query;
    }

    public function getById($id) {
        $this->load(array('id=?',$id));
        return $this->query[0];
    }

    public function getByStudentId($id) {
        $this->load(array('student_id=?', $id),array('order'=>'submission_date DESC'));
        return $this->query;
    }

    public function getByStudentFirstName($name) {
        $name = ucfirst(strtolower($name));
        $this->load(array('student_id IN (SELECT id FROM users WHERE name = ?)',$name));
        return $this->query;
    }

    public function getByStudentLastName($surname){
        $surname = ucfirst(strtolower($surname));
        $this->load(array('student_id IN (SELECT id FROM users WHERE surname = ?)',$surname));
        return $this->query;
    }

    public function getByYearGroup($yearGroup) {
        $this->load(array('student_id IN (SELECT id FROM users WHERE year_group = ?)\'', $yearGroup));
        return $this->query;
    }

    public function getByDate($timestamp) {
        $this->load(array('submission_date=?', $timestamp));
        return $this->query;
    }

    public function getByCity($city) {
        $this->load(array('program_id IN (SELECT id from programs WHERE destination_id IN (SELECT id from destinations
        WHERE city = ?))', $city));
        return $this->query;
    }

    public function getByCountry($country) {
        $this->load(array('program_id IN (SELECT id from programs WHERE destination_id IN (SELECT id from destinations
        WHERE country = ?))', $country));
        return $this->query;
    }

    public function getLatestMobility($student_id = null){
        if($student_id){
            $this->load(array('submission_date = (SELECT MAX(submission_date) from mobilities WHERE student_id = :student_id) AND student_id = :student_id',':student_id'=>$student_id));
        }else{
            $this->load('submission_date = (SELECT MAX(submission_date) from mobilities)');
        }
        return $this->query[0];
    }

    public function getStudent(){
        return $this->users->getById($this->student_id)[0];
    }

    public function getCity(){
        return $this->programs->getById($this->program_id)[0]->getCity();
    }

    public function getCountry(){
        return $this->programs->getById($this->program_id)[0]->getCountry();
    }

    public function getSchool(){
        return $this->programs->getById($this->program_id)[0]->getSchool();
    }

    public function getProgramTitle(){
        return $this->programs->getById($this->program_id)[0]->title;
    }

    public function getCoordinates(){
        return $this->programs->getById($this->program_id)[0]->getCoordinates();
    }

    public function getStartDate(){
        return date('d/m/Y',strtotime($this->start_date));
    }

    public function getEndDate(){
        return date('d/m/Y',strtotime($this->end_date));
    }

    public function toJSON(){
        $user = $this->getStudent();
        $data = array(
            'first name' => $user->name,
            'last name' => $user->surname,
            'city' => $this->getCity(),
            'country' => $this->getCountry(),
            'school' => $this->getSchool(),
            'program title' => $this->getProgramTitle(),
            'start date' => $this->start_date,
            'end date' => $this->end_date,
            'submission date' => $this->submission_date,
        );
        return json_encode($data);
    }

    public function search($id,$studentLastName,$studentFirstName,$yearGroup,$city,$country,$date,$status=-1,$dateOption=self::EXACT_DATE){
        $args = [];
        $vals = [];
        if($id){
            $args[] = 'id=?';
            $vals[] = $id;
        }
        if($studentLastName || $studentFirstName) {
            if($studentLastName){
                if($studentFirstName){
                    $args[] = 'student_id IN (SELECT id FROM users WHERE surname LIKE ? AND name LIKE ?)';
                    $vals[] = $studentLastName.'%';
                    $vals[] = $studentFirstName.'%';
                }else{
                    $args[] = 'student_id IN (SELECT id FROM users WHERE surname LIKE ?)';
                    $vals[] = $studentLastName.'%';
                }

            }
            elseif($studentFirstName){
                $args[] = 'student_id IN (SELECT id FROM users WHERE name LIKE ?)';
                $vals[] = $studentFirstName.'%';
            }
        }
        if($yearGroup) {
            $args[] = 'student_id IN (SELECT id FROM users WHERE year_group = ?)';
            $vals[] = $yearGroup;
        }
        if($city) {
            $args[] = 'program_id IN (SELECT id from programs WHERE destination_id IN (SELECT id from destinations
        WHERE city = ?))';
            $vals[] = $city;
        }
        if($country) {
            $args[] = 'program_id IN (SELECT id from programs WHERE destination_id IN (SELECT id from destinations
        WHERE country = ?))';
            $vals[] = $country;
        }
        if($date) {
            if($dateOption == self::EXACT_DATE){
                $args[] = 'DATE(submission_date)=?';
            }
            elseif($dateOption == self::LATER_THAN){
                $args[] = 'DATE(submission_date)>=?';
            }
            elseif($dateOption == self::EARLIER_THAN){
                $args[] = 'DATE(submission_date)<=?';
            }
            else{
                throw new Exception('dateOption is not valid !');
            }
            $vals[] = $date;
        }
        if($status == 0){
            $args[] = 'status=?';
            $vals[] = '0';
        }
        elseif($status == 1){
            $args[] = 'status=?';
            $vals[] = '1';
        }
        elseif($status == 2){
            $args[] = 'status=?';
            $vals[] = '2';
        }
        elseif($status != -1){
            throw new Exception('status is not valid !');
        }
        $fields = array_merge(array(implode(" AND ", $args)),$vals);
        $this->load($fields,array('order'=>'submission_date DESC'));
        return $this->query;

    }
}