<?php

class Session  {

    private $db;
    private $islogin;
    private $user_id;
    private $php_session_id;
    private $session_timeout = 600;
    private $session_lifespan=10;
    private $salt='asdasdasdasd';
    public function isLogin(){

        return $this->islogin;
    }
    public function getUserID() {
        if ($this->islogin) {
            return($this->user_id);
        } else {
            return(false);
        };
    }
    public function sessionid(){
        return $this->php_session_id;
    }
    public function __construct(){
        // Instantiate new Database object
        $this->db = new Database;
        if(isset($_COOKIE['PHPSESSID'])){
            $this->php_session_id = $_COOKIE["PHPSESSID"];
            $query="SELECT * FROM sessions WHERE id='$this->php_session_id' AND (NOW()- created)<$this->session_lifespan";

            $this->db->query($query);
            $this->db->execute();
            if($this->db->rowCount()==0){
                $maxlifetime = $this->session_lifespan;
                $query="DELETE FROM sessions WHERE id ='$this->php_session_id' OR (NOW() - created) >$maxlifetime";
                $this->db->query($query);
                $this->db->execute();
                unset($_COOKIE["PHPSESSID"]);

            }
        }

           // Start the session

        session_set_save_handler(
            array(&$this, "_open"),
            array(&$this, "_close"),
            array(&$this, "_read"),
            array(&$this, "_write"),
            array(&$this, "_destroy"),
            array(&$this, "_gc")
        );


        session_set_cookie_params($this->session_lifespan);
        session_start();
    }
    public function _read($id){
        $this->php_session_id = $id;
        $stmt="select id, logged_in, user_id from sessions where id = '$id'";
        $this->db->query($stmt);
        $this->db->execute();
        $this->db->rowCount();
        if($this->db->rowCount()>0){
            $row = $this->db->single();
            if ($row["logged_in"]==true) {
                $this->islogin = true;
                $this->user_id = $row["user_id"];
            } else {
                $this->islogin = false;
            };
        }else{
            $this->islogin = false;
            $stmt="INSERT INTO sessions(id, logged_in,user_id, created) VALUES ('$id',false,0,now())";
            $this->db->query($stmt);
            $this->db->execute();
        }
        return "";
    }
    public function Login($strUsername, $strPlainPassword)
    {
        echo 'login';
        $strMD5Password = md5($strPlainPassword.$this->salt);;
        $stmt = "select * FROM user WHERE username = '$strUsername' AND md5_pw = '$strMD5Password'";
        $this->db->query($stmt);
        $this->db->execute();
        if($this->db->rowCount()>0){
            $row = $this->db->single();
            $this->user_id = $row["id"];
            $this->islogin = true;
            $stmt="UPDATE sessions SET logged_in = true, user_id = '$this->user_id' WHERE id = '$this->php_session_id'";
            $this->db->query($stmt);
            $this->db->execute();
            return true;
        }
        else{
            return false;
        }
    }
   public  function _open($save_path, $name)
   {



       // TODO: Implement open() method.
       return true;
   }

    public function _close(){

        // Close the database connection
        // If successful
        if($this->db->close()){
            // Return True
            return true;
        }
        // Return False
        return false;
    }



    public function _write($id, $data){



//        // Create time stamp
//        $access = time();
//        // Set query
//        $this->db->query('REPLACE INTO sessions VALUES (:id, :access, :data)');
//        // Bind data
//        $this->db->bind(':id', $id);
//        $this->db->bind(':access', $access);
//        $this->db->bind(':data', $data);
//        // Attempt Execution
//        // If successful
//        if($this->db->execute()){
//            // Return True
//            return true;
//        }
//        // Return False
//        return false;
        return true;
    }
    public function _destroy($id){

        echo "<br>".$id;
        // Set query
        $this->db->query('DELETE FROM sessions WHERE id = :id');
        // Bind data
        $this->db->bind(':id', $id);
        // Attempt execution
        // If successful
        if($this->db->execute()){
            // Return True
            return true;
        }
        // Return False
        return false;
    }
    public function _gc($max){
        // Calculate what is to be deemed old
        $old = time() - $max;
        // Set query
        $this->db->query('DELETE FROM sessions WHERE access < :old');
        // Bind data
        $this->db->bind(':old', $old);
        // Attempt execution
        if($this->db->execute()){
            // Return True
            return true;
        }
        // Return False
        return false;
    }
}

?>