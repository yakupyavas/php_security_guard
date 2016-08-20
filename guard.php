<?php
#Example usage
#example.php?authpass=yourauthpass&command=clear          --> Clear outdated regs.
#example.php?authpass=yourauthpass&command=block_check    --> Check block periods
require_once("main.php");   
 
$Guard=new Guard();
$Guard->set_db_information("db_name","host","username","password"); #db_name->host>user_name->user_pw
$Guard->set_auth_password("authpass");
$Guard->set_attack_limit(5); #attack limit per hour
$Guard->set_outdate_limit(5); #outdate limit
$Guard->set_time_zone("Europe/Istanbul");
$Guard->set_email_data("sender@hotmail.com","reciever@gmail.com","password","smtp.live.com",587); #sender->reciever->password->smtp->port
$Guard->email_report("send"); 

 
if (isset($_GET['reason']))
{
 
    if (htmlspecialchars($_GET['reason']) =="xss" || htmlspecialchars($_GET['reason']) =="sql" || htmlspecialchars($_GET['reason']) =="lfi" || htmlspecialchars($_GET['reason']) =="rfi" )
    {
   
   
        $Guard->attack_controll();
        
        
   
   
    }
    else
    {
   
    }
}
 
 
if (isset($_GET['authpass']) && isset($_GET['command']))
    {
        if (htmlspecialchars($_GET['authpass'])==$Guard->auth_password && htmlspecialchars($_GET['command'])=="clear")
        {
       
            $Guard->cleaner();
           
        }
        else
        {
         
        }
    }
 
if (isset($_GET['authpass']) && isset($_GET['command']))
    {
        if (htmlspecialchars($_GET['authpass'])==$Guard->auth_password && htmlspecialchars($_GET['command'])=="block_check")
        {
       
            $Guard->block_checker();
                   
           
 
        }
        else
        {
            
        }
    }
 
 
?>
