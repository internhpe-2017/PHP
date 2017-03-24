<?php
class usermodel {
private $username;
function usermodel($username){
$this->username=$username;

}
function get_username() {
return $this->$username;
}
function set_username($username) {
$this->username=$username;
}
}
?>
