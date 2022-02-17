<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_Controller extends CI_Controller
{
    public function __construct()
    {
        // session don't start PHP_SESSION_NONE / session start PHP_SESSION_ACTIVE
        parent::__construct();
        if(session_status()==PHP_SESSION_NONE) session_start();
        if(isset($_SESSION['role_id'])){
            $page_class_name = get_called_class();
            $role_id = $_SESSION['role_id'];
            $this->load->middleware('secaccess');
            $access = $this->secaccess->accesspage($page_class_name, $role_id);
            if($access==0){
                $this->s->assign('username', $_SESSION['fio']);
                $this->s->assign('userrole', $_SESSION['role_title'] ?? '');
                session_write_close();
                $this->s->display('header.tpl');
                $this->s->display('menu.tpl');
                $this->s->display('notentry.tpl');
                $this->s->display('footer.tpl');
                die();
            }else $this->s->assign('access', $access);
        }
        else $role_id = null;
        if(is_null($role_id)) header('Location: /login');
        $this->s->assign('username', $_SESSION['fio']);
        $this->s->assign('userrole', $_SESSION['role_title']);
    }
}
