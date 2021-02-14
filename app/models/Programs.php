<?php


class Programs extends \DB\SQL\Mapper
{

    private $destinations;

    public function __construct(DB\SQL $db) {
        parent::__construct($db,'programs');
        $this->destinations = new Destinations($this->db);
    }

    public function all() {
        $this->load();
        return $this->query;
    }

    public function getByID($id){
        $this->load(array('id=?',$id));
        return $this->query;
    }

    public function getByDestinationID($id){
        $this->load(array('destination_id=?',$id));
        return $this->query;
    }

    public function getByDestinationIDActive($id){
        $this->load(array('destination_id=? AND status=?',$id,1));
        return $this->query;
    }

    public function getCity(){
        return $this->destinations->getByID($this->destination_id)[0]->city;
    }

    public function getCountry(){
        return $this->destinations->getByID($this->destination_id)[0]->country;
    }

    public function getSchool(){
        return $this->destinations->getByID($this->destination_id)[0]->school;
    }

    public function getCoordinates(){
        $destination = $this->destinations->getByID($this->destination_id)[0];
        return array('latitude'=>$destination->latitude,'longitude'=>$destination->longitude);
    }
}