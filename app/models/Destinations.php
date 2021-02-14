<?php


class Destinations extends \DB\SQL\Mapper
{

    public function __construct(DB\SQL $db) {
        parent::__construct($db,'destinations');
        $this->users = new Users($this->db);
    }

    public function getByID($id){
        $this->load(array('id=?',$id));
        return $this->query;
    }

    public function getBySchool($school){
        $this->load(array('school=? AND status=?',$school,1));
        return $this->query;
    }

    public function getByCountry($countryName){
        $this->load(array('country=? AND status=?',$countryName,1));
        return $this->query;
    }

    public function all() {
        $this->load();
        return $this->query;
    }

    public function allUnlocked() {
        $this->load(array('status=?',1));
        return $this->query;
    }

    public function allCountries(){
        return array_column($this->select('country',array('status=?',1),array('group'=>'country')),'country');
    }

}