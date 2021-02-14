<?php


class Users extends \DB\SQL\Mapper
{
    public function __construct(DB\SQL $db) {
        parent::__construct($db,'users');
    }

    public function all() {
        $this->load();
        return $this->query;
    }

    public function getById($id) {
        $this->load(array('id=?',$id));
        return $this->query;
    }

    public function getByUsername($username) {
        $this->load(array('username=?', $username));
        return $this->query;
    }

    public function getByFirstName($firstName) {
        return $this->find(array('name LIKE ?', $firstName.'%'));
    }

    public function getByLastName($lastName){
        return $this->find(array('surname LIKE ?', $lastName.'%'));
    }

    public function add() {
        $this->copyFrom('POST');
        $this->save();
    }

    public function edit($id) {
        $this->load(array('id=?',$id));
        $this->copyFrom('POST');
        $this->update();
    }

    public function delete($id) {
        $this->load(array('id=?',$id));
        $this->erase();
    }

}