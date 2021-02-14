<?php


class Controller
{

    protected $f3;
    protected $db;

    function __construct()
    {
        $f3 = Base::instance();
        $this->f3 = $f3;
        $db = new DB\SQL(
            $f3->get('dbx'),
            $f3->get('dbxuser'),
            $f3->get('dbxpwd'),
            array( \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION)
        );
        $this->db = $db;
    }

    // function is called before every single routing!
    function beforeroute()
    {
        if($this->f3->get('SESSION.logged_in'))
        {
            if(strtotime('now') - $this->f3->get('SESSION.timestamp') > $this->f3->get('auto_logout'))
            {
                $this->f3->clear('SESSION');
                $this->f3->reroute('/');
            }
            else {
                $this->f3->set('SESSION.timestamp', strtotime('now'));
            }
        }
    }

    // function is called after every single routing!
    function afterroute() {
        echo Template::instance()->render('layout.htm');
    }

}