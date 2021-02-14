<?php

class DestinationController extends Controller{

    public function viewDestinations(){
        if($this->f3->get('SESSION.logged_in')){
            $destinations_ = new Destinations($this->db);
            $destinations = $destinations_->all();
            $this->f3->set('D_',$destinations);
            $this->f3->set('view','destination/all.htm');
        }
        else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    public function viewDestination(){
        if($this->f3->get('SESSION.logged_in')) {
            $id = $this->f3->get('GET.id');
            $destinations = new Destinations($this->db);
            $programs_ = new Programs($this->db);
            $destination = $destinations->getByID($id)[0];
            if(empty($destination)){
                $this->f3->error(404);
            }
            $programs = $programs_->getByDestinationID($id);
            $this->f3->set('destination',$destination);
            $this->f3->set('programs',$programs);
            $this->f3->set('view','destination/view.htm');
        }
        else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    public function createDestination(){
        if($this->f3->get('SESSION.logged_in') && $this->f3->get('SESSION.role') == 1) {
            $destinations = new Destinations($this->db);
            $data = $this->f3->get('POST');
            var_dump($data);
            if(!empty($data["school"]) && !empty($data["city"]) && !empty($data["country"]) && !empty($data["latitude"]) &&
                !empty($data["longitude"])){
                $data["longitude"] = doubleval($data["longitude"]);
                $data["latitude"] = doubleval($data["latitude"]);
                $destinations->copyfrom($data);
                $destinations->save();
                $this->f3->reroute('/destination/all');
            }else{
                $this->f3->error(403);
            }
        }
        else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    public function deleteDestination(){
        if($this->f3->get('SESSION.logged_in') && $this->f3->get('SESSION.role') == 1) {
            $id = $this->f3->get('GET.id');
            $destinations = new Destinations($this->db);
            $destination = $destinations->getByID($id)[0];
            if(empty($destination)){
                $this->f3->error(404);
            }
            $destination->erase();
            $this->f3->reroute('/destination/all');
        }
        else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    public function createProgram(){
        if($this->f3->get('SESSION.logged_in') && $this->f3->get('SESSION.role') == 1) {
            $programs = new Programs($this->db);
            if(!empty($this->f3->get('POST.destination_id')) && !empty($this->f3->get('POST.title'))){
                $programs->copyfrom($this->f3->get('POST'));
                $programs->save();
                $this->f3->reroute('/destination/view?id='.$this->f3->get('POST.destination_id'));
            }else{
                $this->f3->error(403);
            }

        }
        else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    public function toggleDestinationStatus(){
        if($this->f3->get('SESSION.logged_in') && $this->f3->get('SESSION.role') == 1) {
            $id = $this->f3->get('GET.id');
            $destinations = new Destinations($this->db);
            $destination = $destinations->getByID($id)[0];
            if(empty($destination)){
                $this->f3->error(404);
            }
            $destination->status = $destination->status ? 0:1;
            $destination->update();
            $this->f3->reroute('/destination/all');
        }else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    public function toggleProgramStatus(){
        if($this->f3->get('SESSION.logged_in') && $this->f3->get('SESSION.role') == 1) {
            $programs = new Programs($this->db);
            $program = $programs->getByID($this->f3->get('GET.id'))[0];
            if(empty($program)){
                $this->f3->error(404);
            }
            $program->status = $program->status ? 0:1;
            $program->update();
            $this->f3->reroute('/destination/view?id='.$program->destination_id);
        }
        else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    public function deleteProgram(){
        if($this->f3->get('SESSION.logged_in') && $this->f3->get('SESSION.role') == 1) {
            $programs = new Programs($this->db);
            $program = $programs->getByID($this->f3->get('GET.id'))[0];
            if(empty($program)){
                $this->f3->error(404);
            }
            $destination_id = $program->destination_id;
            $program->erase();
            $this->f3->reroute('/destination/view?id='.$destination_id);
        }
        else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

}