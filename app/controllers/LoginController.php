<?php


class LoginController extends Controller
{
    function render(){

        if($this->f3->get('SESSION') AND !empty($this->f3->get('SESSION.user')) AND strtotime('now') - $this->f3->get('SESSION.timestamp') < $this->f3->get('auto_logout'))
        {
            $this->f3->reroute('/dashboard');
        }
        else
        {
            $this->f3->clear('SESSION');
            $this->f3->set('view','index.htm');
            //echo password_hash('admin', PASSWORD_DEFAULT);
        }

    }

    function login() {

        $username = $this->f3->get('POST.username');
        $password = $this->f3->get('POST.password');

        $user = new Users($this->db);
        $user->getByUsername($username);

        if(password_verify($password, $user->password))
        {
            $this->f3->set('SESSION.id', $user->id);
            $this->f3->set('SESSION.user', $user->username);
            $this->f3->set('SESSION.name', $user->name);
            $this->f3->set('SESSION.surname', $user->surname);
            $this->f3->set('SESSION.role', $user->role);
            $this->f3->set('SESSION.avatar',$user->image_path);
            $this->f3->set('SESSION.timestamp', strtotime('now'));
            $this->f3->set('SESSION.logged_in', 1);
            $this->f3->reroute('/dashboard');
        }
        else
        {
            $this->f3->set('warn', 'Identifiants inconnus !');
            $this->f3->set('view','index.htm');
        }
    }

    function logout() {

        $this->f3->clear('SESSION');
        $this->f3->reroute('/');
    }

}