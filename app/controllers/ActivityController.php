<?php


class ActivityController extends Controller
{
    function createMobility() {

        if($this->f3->get('SESSION.logged_in')){

            if($this->f3->VERB === 'POST'){
                $post = $this->f3->get('POST');
                $post['student_id'] = $this->f3->get('SESSION.id');
                $post['submission_date'] = date('Y-m-d H:i:s',strtotime('now'));
                try{
                    $mobility = new Mobilities($this->db);
                    $mobility->copyfrom($post);
                    $mobility->save();
                    $student = $mobility->getStudent();
                    if ($this->f3->get('SESSION.id') != $student->id && $this->f3->get('SESSION.role') != 1) {
                        $this->f3->error(401);
                    }
                    $data = array(
                        "studentName" => $student->name,
                        "studentSurname" => $student->surname,
                        "imagePath" => $student->image_path,
                        "city" => $mobility->getCity(),
                        "country" => $mobility->getCountry(),
                        "school" => $mobility->getSchool(),
                        "programTitle" => $mobility->getProgramTitle(),
                        "submissionDate" => Utils::timeAgo($mobility->submission_date),
                        "startDate" => $mobility->getStartDate(),
                        "endDate" => $mobility->getEndDate(),
                        "status" => $mobility->status,
                        "id" => $mobility->id
                    );
                    $this->f3->set('action',["success"=>true,"response"=>'Mobilité créée avec succès !']);
                    $this->f3->set('data', $data);
                    $this->f3->set('view','mobility/view.htm');
                }catch(Exception $e){
                    $this->f3->set('created',false);
                    $destinations = new Destinations($this->db);
                    $countries = $destinations->allCountries();
                    sort($countries, SORT_STRING);

                    $this->f3->set('countries', $countries);

                    $this->f3->set('view', 'mobility/create.htm');
                }
            }else{
                $destinations = new Destinations($this->db);
                $countries = $destinations->allCountries();
                sort($countries, SORT_STRING);

                $this->f3->set('countries', $countries);

                $this->f3->set('view', 'mobility/create.htm');
            }
        }else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    function modifyMobility() {

        if($this->f3->get('SESSION.logged_in')){

            if($this->f3->VERB === 'GET'){
                $id = $this->f3->get('GET.id');
                $mobilities = new Mobilities($this->db);
                $mobility = $mobilities->getById($id);
                if(empty($mobility))
                    $this->f3->error(404);
                if($mobility->status != 0){
                    $this->f3->error(405);
                }else{
                    $destinations_ = new Destinations($this->db);
                    $countries = $destinations_->allCountries();
                    sort($countries, SORT_STRING);
                    $destinationsArray = $destinations_->getByCountry($mobility->getCountry());
                    foreach($destinationsArray as $destination){
                        $destinations[] = array("id"=>$destination->id,"school"=>$destination->school,"city"=>$destination->city);
                    }
                    $programs_ = new Programs($this->db);
                    $programsArray = $programs_->getByDestinationID($destinations_->getBySchool($mobility->getSchool())[0]->id);
                    foreach($programsArray as $program){
                        $programs[] = array("id"=>$program->id,"title"=>$program->title);
                    }
                    $this->f3->set('surname',$mobility->getStudent()->surname);
                    $this->f3->set('name',$mobility->getStudent()->name);
                    $this->f3->set('student_id',$mobility->getStudent()->id);
                    $this->f3->set('M_country',$mobility->getCountry());
                    $this->f3->set('M_school',$mobility->getSchool());
                    $this->f3->set('M_program',$mobility->getProgramTitle());
                    $this->f3->set('countries', $countries);
                    $this->f3->set('destinations',$destinations);
                    $this->f3->set('programs',$programs);
                    $this->f3->set('start_date',$mobility->start_date);
                    $this->f3->set('end_date',$mobility->end_date);

                    $this->f3->set('view', 'mobility/modify.htm');
                }
            }
            elseif($this->f3->VERB === 'POST'){
                $post = $this->f3->get('POST');
                try{
                    $mobilities = new Mobilities($this->db);
                    $mobility = $mobilities->getById($post['id']);
                    if($mobility->status != 0){
                        $this->f3->error(404);
                    }else{
                        $mobility->program_id = $post['program_id'];
                        $mobility->start_date = $post['start_date'];
                        $mobility->end_date = $post['end_date'];
                        $mobility->update();
                    }
                }catch(Exception $e){
                    $this->f3->set('created',false);
                    $this->f3->set('view', 'mobility/modify.htm');
                }
                $this->f3->reroute('/mobility/view?id='.$post['id']);
            }
        }else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    function viewMobility(){
        if($this->f3->get('SESSION.logged_in')){

            $id = $this->f3->get('GET.id');
            $mobilities = new Mobilities($this->db);
            $mobility = $mobilities->getById($id);
            if($mobility){
                $student = $mobility->getStudent();
                if($this->f3->get('SESSION.id') != $student->id && $this->f3->get('SESSION.role') != 1){
                    $this->f3->error(401);
                }
                $data = array(
                    "studentName" => $student->name,
                    "studentSurname" => $student->surname,
                    "imagePath" => $student->image_path,
                    "city" => $mobility->getCity(),
                    "country" => $mobility->getCountry(),
                    "school" => $mobility->getSchool(),
                    "programTitle" => $mobility->getProgramTitle(),
                    "submissionDate" => date('d/m/Y',strtotime($mobility->submission_date)),
                    "startDate" => $mobility->getStartDate(),
                    "endDate" => $mobility->getEndDate(),
                    "status" => $mobility->status,
                    "id" => $mobility->id
                );
                $this->f3->set('data',$data);
            }else{
                $this->f3->error(404);
            }

            $this->f3->set('view','mobility/view.htm');
        }else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    function viewMobilities(){
        if($this->f3->get('SESSION.logged_in')){

            $query = array();
            $studentLastName = $this->f3->get('GET.studentLastName');
            if($studentLastName)
                $query[] = 'studentLastName='.$studentLastName;
            $studentFirstName = $this->f3->get('GET.studentFirstName');
            if($studentFirstName)
                $query[] = 'studentFirstName='.$studentFirstName;
            $yearGroup = $this->f3->get('GET.yearGroup');
            if($yearGroup)
                $query[] = 'yearGroup='.$yearGroup;
            $city = $this->f3->get('GET.city');
            if($city)
                $query[] = 'city='.$city;
            $country = $this->f3->get('GET.country');
            if($city)
                $query[] = 'country='.$country;
            $date = $this->f3->get('GET.date');
            $dateOption = $this->f3->get('GET.dateOption');
            if($date){
                $query[] =  'date='.$date;
                if($dateOption != 0)
                    $query[] = 'dateOption='.$dateOption;
            }
            $status = $this->f3->get('GET.status');
            if($status != null){
                if($status != -1)
                    $query[] = 'status='.$status;
            }else{
                $status =-1;
            }
            $page = $this->f3->get('GET.page')?$this->f3->get('GET.page'):1;
            $this->f3->set('page',intval($page));
            if(sizeof($query)>=1)
                $query = implode("&", $query).'&';
            else
                $query = '';
            $this->f3->set('query',$query);
            $results_per_page = $this->f3->get('GET.pagination')?$this->f3->get('GET.pagination'):5;
            $mobilities = new Mobilities($this->db);

            if($this->f3->get('SESSION.role') == 1){
                if($studentLastName || $studentFirstName || $yearGroup || $city || $country || $date || ($status != -1)){
                    $this->f3->set('isSearch',true);
                    $mobilities_array = $mobilities->search(null,$studentLastName,$studentFirstName,$yearGroup,$city,$country,$date,$status,$dateOption);
                }else{
                    $this->f3->set('isSearch',false);
                    $mobilities_array = $mobilities->all();
                }
            }else{
                $mobilities_array = $mobilities->getByStudentId($this->f3->get('SESSION.id'));
            }
            $results_size = sizeof($mobilities_array);
            $number_of_pages = ceil($results_size/$results_per_page);
            $this->f3->set('number_of_pages',$number_of_pages);
            if($page<1)
                $page = 1;
            elseif($page>$number_of_pages)
                $page = $number_of_pages;
            $start = ($page-1)*$results_per_page;
            if($results_size > 0) {
                $this->f3->set("M_EXISTS",true);
                $data = array();
                $mobility_array_sliced = array_slice($mobilities_array,$start,$results_per_page);
                foreach($mobility_array_sliced as $mobility){
                    $student = $mobility->getStudent();
                    $data[] = array(
                        "studentName" => $student->name,
                        "studentSurname" => $student->surname,
                        "imagePath" => $student->image_path,
                        "city" => $mobility->getCity(),
                        "country" => $mobility->getCountry(),
                        "submissionDate" => Utils::timeAgo($mobility->submission_date),
                        "status" => $mobility->status,
                        "id" => $mobility->id
                    );
                }
                $this->f3->set('M_', $data);
                $this->f3->set('M_SIZE',$results_size);
            }
            else{
                $this->f3->set("M_EXISTS",false);
            }
            $this->f3->set('view','mobility/all.htm');
        }else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    function validateMobility(){
        if($this->f3->get('SESSION.logged_in')){
            if($this->f3->get('SESSION.role') == 1){
                $id = $this->f3->get('GET.id');
                $mobilities = new Mobilities($this->db);
                $mobility = $mobilities->getById($id);
                $mobility->status = 1;
                $mobility->save();
                $student = $mobility->getStudent();
                if ($this->f3->get('SESSION.id') != $student->id && $this->f3->get('SESSION.role') != 1) {
                    $this->f3->error(401);
                }
                $data = array(
                    "studentName" => $student->name,
                    "studentSurname" => $student->surname,
                    "imagePath" => $student->image_path,
                    "city" => $mobility->getCity(),
                    "country" => $mobility->getCountry(),
                    "school" => $mobility->getSchool(),
                    "programTitle" => $mobility->getProgramTitle(),
                    "submissionDate" => Utils::timeAgo($mobility->submission_date),
                    "startDate" => $mobility->getStartDate(),
                    "endDate" => $mobility->getEndDate(),
                    "status" => $mobility->status,
                    "id" => $mobility->id
                );
                $this->f3->set('action',["success"=>true,"response"=>'Mobilité validée avec succès !']);
                $this->f3->set('data', $data);
                $this->f3->set('view','mobility/view.htm');
            }else{
                $this->f3->reroute('/');
            }
        }else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }

    function deleteMobility(){
        if($this->f3->get('SESSION.logged_in')){
            if($this->f3->get('SESSION.role') == 1){
                $id = $this->f3->get('GET.id');
                $mobilities = new Mobilities($this->db);
                $mobility = $mobilities->getById($id);
                if(empty($mobility))
                    $this->f3->error(404);
                if($mobility->status != 0)
                    $this->f3->error(405);
                $mobility->erase();
                $this->f3->set('mobilityErased',true);
                $this->viewMobilities();
            }else{
                $this->f3->reroute('/');
            }
        }else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }

    }

    function archiveMobility(){
        if($this->f3->get('SESSION.logged_in')){
            if($this->f3->get('SESSION.role') == 1){
                $id = $this->f3->get('GET.id');
                $mobilities = new Mobilities($this->db);
                $mobility = $mobilities->getById($id);
                if(empty($mobility))
                    $this->f3->error(404);
                if($mobility->status != 1)
                    $this->f3->error(405);
                $mobility->status = 2;
                $mobility->save();
                $student = $mobility->getStudent();
                if ($this->f3->get('SESSION.id') != $student->id && $this->f3->get('SESSION.role') != 1) {
                    $this->f3->error(401);
                }
                $data = array(
                    "studentName" => $student->name,
                    "studentSurname" => $student->surname,
                    "imagePath" => $student->image_path,
                    "city" => $mobility->getCity(),
                    "country" => $mobility->getCountry(),
                    "school" => $mobility->getSchool(),
                    "programTitle" => $mobility->getProgramTitle(),
                    "submissionDate" => Utils::timeAgo($mobility->submission_date),
                    "startDate" => $mobility->getStartDate(),
                    "status" => $mobility->status,
                    "id" => $mobility->id
                );
                $this->f3->set('action',['success'=>true,"response"=>'Mobilité archivée avec succès !']);
                $this->f3->set('data', $data);
                $this->f3->set('view','mobility/view.htm');
            }else{
                $this->f3->reroute('/');
            }
        }else{
            $this->f3->clear('SESSION');
            $this->f3->reroute('/');
        }
    }
}