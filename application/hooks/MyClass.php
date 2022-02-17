<?php


class MyClass
{
    public function Myfunction($uri){
        session_start();
        $systemuin = $_SESSION['systemuin'] ?? null;
        session_write_close();
        $db = new PDO(DSN, USER, PASSWORD);
        $sql = "SELECT access, block FROM auth WHERE systemuin LIKE :systemuin";
        $sth = $db->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindValue(':systemuin', $systemuin, PDO::PARAM_STR);
        $sth->execute();
        $block_array = $sth->fetch(PDO::FETCH_ASSOC);
        if($block_array) {
            if ($block_array['block'] != 10) die('<h3 style="color: #0e0e0e; text-align: center; background: #e5894c; border: solid 1px #070707; width: 600px; margin-left: auto; margin-right: auto; height: 50px; margin-top: 200px; padding: 5px;">Ваш аккаунт заблокирован<br>Обратитесь к администратору</h3>');
            // =============== Список всех страниц с разграничением прав ===============================
            switch ($uri) {
                // =============== Права не требуются =======================
                case '/test':
                    $flag = 0;
                    break;
                case '/authentication':
                    $flag = 0;
                    break;
                case '/registered':
                    $flag = 0;
                    break;
                // =============== Требуются минимальные права ====================
                case '/statistic':
                    $flag = 10;
                    break;
                case '/main':
                    $flag = 10;
                    break;
                case '/createproject':
                    $flag = 10;
                    break;
                default :
                    $flag = 10;
            }
            if ($flag <= $block_array['access']) {
                session_start();
                $_SESSION['access'] = 'Ok';
                session_write_close();
            } else {
                session_start();
                $_SESSION['access'] = 'Error';
                session_write_close();
                die('<h3 style="color: #0e0e0e; text-align: center; background: #e5894c; border: solid 1px #070707; width: 600px; margin-left: auto; margin-right: auto; height: 100px; margin-top: 200px; padding: 5px;">У вас не достаточно прав для просмотра данной страницы<br>Обратитесь к администратору<p><a class="btn btn-primary" href="javascript:window.history.back();">Вернуться назад</a></p></h3>');
            }
        }
    }
}