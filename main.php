<?php
    include "class.phpmailer.php"; //mail functions
 
    if (isset($_GET['reason'])) {
 
        $reason=htmlspecialchars($_GET['reason']);
 
        if ($reason == "xss" || $reason == "sql" || $reason == "lfi" || $reason == "rfi") {
 
        
        
   
        }
        else
        {
 
        }
    }
    else
    {
 
    }
 
    ?>
 
 
</body>
 
<?php
######################
#Coded By SquadronZ#
######################
#error_reporting(0);
 
Interface IGuard
    {
        public function set_db_information($db_name,$db_host,$db_username,$db_password);
        public function set_attack_limit($limit);
        public function email_report($select);
        public function set_email_data($from,$to,$password,$smtp,$port);
        public function set_outdate_limit($limit);
    }
 
class Guard implements IGuard
{
    public $db_name;
    public $db_username;
    public $db_password;
    public $db_host;
    public $ip;
    public $date;
    public $referer_address;
    public $attack;
    public $reason;
    public $db_connection;
 
 
 
    /*settings*/
    public $attack_limit; 
    public $auth_password; 
    public $email_from; 
    public $email_to;
    public $email_password;
    public $email_option;
    public $email_smtp;
    public $email_port;
    public $outdate_limit;
 
 
 
 
 
 
    public function __construct() 
        {
 
            $this->date=date('d.m.Y - H:i:s');
           
            $this->ip=$this->getIp();
            
            $this->referer_address=$this->get_Referer();
            
            $this->attack=$this->get_Reason();
            //type of attack(like xss,sql,lfi)
 
            $this->current_year=substr($this->date,6,4);
            $this->current_month=substr($this->date,3,2);
            $this->current_day=substr($this->date,0,2);
            $this->current_hour=substr($this->date,13,2);
 
        }
 
 
 
protected function getIp()  
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
      {
          $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
          $ip=$_SERVER['REMOTE_ADDR'];
    }
        return $ip;
}
 
protected function get_Reason() 
{
    if (isset($_GET['reason']))
    {
        $this->reason =htmlspecialchars($_GET['reason']);
 
 
        if ($this->reason=="xss")
        {
            return "XSS";
        }
 
        else if($this->reason=="sql")
        {
 
            return "SQL Injection";
 
        }
 
        else if($this->reason=="lfi")
        {
            return "LFI";
 
        }
 
        else if($this->reason=="rfi")
        {
            return "RFI";
 
        }
 
        else
        {
            return "Unknown";
 
        }
 
    }
    else
    {
        return "Unknown";
    }
 
 
}
 
protected function get_Referer() 
{
    if(isset($_SERVER['HTTP_REFERER']))
    {
        return htmlspecialchars($_SERVER['HTTP_REFERER']);
    }
    else
    {
        return "Undefined";
    }
}
 
 
public function set_db_information($db_name,$db_host,$db_username,$db_password)
{
 
   
    $this->db_name=$db_name;
    $this->db_host=$db_host;
    $this->db_username=$db_username;
    $this->db_password=$db_password;
    $dsn="mysql:host=$this->db_host;dbname=$this->db_name;charset=utf8";
    $this->db_connection=new PDO($dsn,$this->db_username,$this->db_password);
}
 
 
public function set_attack_limit($limit) 
{
    $this->attack_limit=$limit;
}
 
public function set_auth_password($password) 
{
    $this->auth_password=$password;
 
}
 
 
public function set_time_zone($time_zone) 
{
    date_default_timezone_set($time_zone);
}
 
public function email_report($select) 
{
    $this->email_option=$select;
 
}
 
public function set_email_data($from,$to,$password,$smtp,$port)
{
 
$this->email_from=$from;
$this->email_to=$to;
$this->email_password=$password;
$this->email_smtp=$smtp;
$this->email_port=$port;
 
}
 
public function set_outdate_limit($limit)
{
    $this->outdate_limit=$limit;
   
}
 
public function htaccess_blocker($ip_adress)
{
    $ban="\ndeny from ".$ip_adress;
    $file=".htaccess";
    $open_file=fopen($file, "a");
    $write_to_file=fwrite($open_file,$ban);
    $close_file=fclose($open_file);
    echo "CONNECTION BLOCKED";
    
 
}
public function htaccess_unblocker($ip_adress)
{
 
    $ban="deny from ".$ip_adress;
    $file=".htaccess";
    $open=fopen($file,"r");
    $read=fread($open,filesize($file));
    $str=str_replace($ban,"",$read);
    /***/
    $open_file=fopen($file,"w");
    fwrite($open_file,$str);
    fclose($open_file);
 
}
 
 
function send_mail($header,$content)
{
 
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = $this->email_smtp;
    $mail->Port = $this->email_port;
    $mail->SMTPSecure = 'tls';
    $mail->Username = $this->email_from;
    $mail->Password = $this->email_password;
    $mail->SetFrom($mail->Username, 'Website Reporter');
    $mail->AddAddress($this->email_to, 'Website Admin');
    $mail->CharSet = 'UTF-8';
    $mail->Subject = $header;
    $mail->MsgHTML($content);
           
    if($mail->Send())
          {
           
          }
          else
          {
              
            echo $mail->ErrorInfo;
          }
}
 
 
public function attack_controll() 
{
 
    $check_ip=$this->db_connection->prepare("SELECT id FROM controll where ip_adress=? ");
    $check_ip->execute(array($this->ip));
    
    $ip_reg_count=$check_ip->rowCount();
   
    if($ip_reg_count == 0){
 
   
        $add_to_db=$this->db_connection->prepare("INSERT INTO controll(ip_adress,first_conn,last_conn,attack_report) VALUES(?,?,?,?) ");
        $add_to_db->execute(array($this->ip,$this->date,$this->date,"Referer Adress:".$this->referer_address." Attack Type:".$this->attack."/"));
 
        
   
        
    }
    else
    {
 
 
 
    $get_info=$this->db_connection->prepare("SELECT id,ip_adress,first_conn,last_conn,attack_report FROM controll where ip_adress = ? ");
    $get_info->execute(array($this->ip));
    $informations=$get_info->fetchAll();
 
    
 
    foreach ($informations as $data)
    {
 
        $attack_report=$data['attack_report'];
        if(strpos($attack_report, ","))
        {
            $det_att_count=count(explode(",",$attack_report)); 
        }
        else
        {
            $det_att_count=1;
        }
   
        $first_det_time=$data['first_conn'];
        $last_det_time=$data['last_conn'];
        $id_number=$data['id'];
 
   
 
        $last_det_year=substr($last_det_time,6,4);
        $last_det_month=substr($last_det_time,3,2);
        $last_det_day=substr($last_det_time,0,2);
        $last_det_hour=substr($last_det_time,13,2);
 
   
   
        if ($last_det_hour != $this->current_hour || $last_det_day != $this->current_day || $last_det_month != $this->current_month || $last_det_year != $this->current_year)
        {
            $delete_reg=$this->db_connection->prepare("DELETE FROM controll where ip_adress=? ");
            $delete_reg->execute(array($this->ip));
            
 
            $old_reg="IP Adress:$this->ip\nFirst Connection:$first_conn - Last Connection:$last_conn\nAttack Report:$attack_report    Detected Attack Number:$det_att_count\n(This informations have been archived because new attack detected from same adress)\n";
 
            $report_old=$this->db_connection->prepare("INSERT INTO reports(report,date) VALUES(?,?)");
            $report_old->execute(array($old_reg,$this->date));
           
            
            
 
            $new_reg=$this->db_connection->prepare("INSERT INTO controll(ip_adress,first_conn,last_conn,attack_report) VALUES(?,?,?,?) ");
            $new_reg->execute(array($this->ip,$this->date,$this->date,"Referer Adress:".$this->referer_address." Attack Type:".$this->attack."/"));
           
        }
        else
        {
 
        if($det_att_count >= $this->attack_limit)
            {
           
 
           
                $remove_reg=$this->db_connection->prepare("DELETE FROM controll where ip_adress=?");
                $remove_reg->execute(array($this->ip));
               
 
                $report_old=$this->db_connection->prepare("INSERT INTO block(ip_adress,ban_date,ban_reason,first_conn,last_conn) VALUES(?,?,?,?,?)");
                $report_old->execute(array($this->ip,$this->date,$attack_report,$first_det_time,$last_det_time));
                
           
           
                $this->htaccess_blocker($this->ip);
               
            }
            else
            {
                $new_reg=$attack_report.","."Referer Adress:".$this->referer_address." Attack Type:".$this->attack."/";
                $update_reg=$this->db_connection->prepare("UPDATE controll SET attack_report = ? ,last_conn = ? where id = ?");
                $update_reg->execute(array($new_reg,$this->date,$id_number));
                
           
 
            }
 
        }
       
   
 
        }
 
        }
 
 
 
}
 
 
 
function cleaner()
{
 
    $inf_report ="--Deleted row texts given below.--\n\n";
    $removed_cntrl_row_count=0;
    $removed_rprt_row_count=0;
 
 
    $controll_tbl=$this->db_connection->query("SELECT id,ip_adress,first_conn,last_conn,attack_report FROM controll");
    $content=$controll_tbl->fetchAll();
    
 
    foreach($content as $data)
    {
        
        $id_number=$data['id'];
        $ip_adress=$data['ip_adress'];
        $first_conn=$data['first_conn'];
        $last_conn=$data['last_conn'];
        $attack_report=$data['attack_report'];
 
        
 
        $last_det_month=substr($last_conn,3,2);
        $last_det_day=substr($last_conn,0,2);
 
        
 
        if ($last_det_month != $this->current_month || $this->current_day >= $last_det_day+$this->outdate_limit)
        {
            $inf_report .= "<br><br>@Controll : IP Adress:$ip_adress<br>First Connection:$first_conn - Last Connection:$last_conn<br>Detected Attacks and Adresses:$attack_report<br>";
 
            $remove_reg=$this->db_connection->prepare("DELETE FROM controll where id=? ");
            $remove_reg->execute(array($id_number));
            $removed_cntrl_row_count++;
            
 
        }
 
        else
        {
 
        }
 
        
        }
 
 
 
 
        $report_tbl=$this->db_connection->query("SELECT id,report,date FROM reports"); 
        $get_data=$report_tbl->fetchAll();
       
        foreach($get_data as $report_tbl_data)
        {
 
            $id_number=$report_tbl_data['id'];
            $reported_data=$report_tbl_data['report'];
            $report_date=$report_tbl_data['date'];
 
 
 
            $report_month=substr($report_date,3,2);
            $report_day=substr($report_date,0,2);
 
            if($report_month != $this->current_month || $this->current_day >= $report_day+$this->outdate_limit)
            {
                $inf_report .= "<br>@Reports_Tbl : $reported_data<br>Report Date : $report_date<br>";
 
                $remove_reg=$this->db_connection->prepare("DELETE FROM reports where id =? ");
                $remove_reg->execute(array($id_number));
                $removed_rprt_row_count++;
               
            }
            else
            {
 
            }
 
            
 
 
        }
 
 
        $statistics = "\nDeleted $removed_cntrl_row_count row from controll table , Deleted $removed_rprt_row_count row from reports table.Information mail has sent.($this->date)";
        $inf_report .= "\n\n\n--($this->date)--";
 
        $write_report=$this->db_connection->prepare("INSERT INTO reports(report,date) VALUES(?,?)");
        $write_report->execute(array($statistics,$this->date));
        
        if (isset($this->email_option))
        {
 
            if($this->email_option=="send")
            {
                $this->send_mail("Website Reporter",$inf_report);
                
            }
        }
 
 
 
}
 
 
 
 
public function block_checker()
{
 
    $get_data=$this->db_connection->query("SELECT id,ip_adress,ban_date,ban_reason,first_conn,last_conn FROM block");
    $informations=$get_data->fetchAll();           
 
 
    foreach($informations as $data)
        {
 
            $id_number=$data['id'];
            $ip_adress=$data['ip_adress'];
            $attack_report=$data['ban_reason'];
            $first_conn=$data['first_conn'];
            $last_conn=$data['last_conn'];
   
   
            $ban_date=$data['ban_date'];
 
            $ban_year=substr($ban_date,6,4);
            $ban_month=substr($ban_date,3,2);
            $ban_day=substr($ban_date,0,2);
            $ban_hour=substr($ban_date,13,2);
   
 
        if($ban_year != $this->current_year || $ban_month != $this->current_month || $ban_day != $this->current_day || $ban_hour != $this->current_hour)
        {
           
 
                $ban_rep="IP Adress: $ip_adress First Connection: $first_conn  Last Connection: $last_conn  Ban Reason: $attack_report    Ban period finished.($this->date) ";
           
                $this->htaccess_unblocker($ip_adress);
           
                $delete_reg=$this->db_connection->prepare("DELETE FROM block where ip_adress=?");
                $delete_reg->execute(array($ip_adress));
               
 
           
                $write_report=$this->db_connection->prepare("INSERT INTO reports(report,date) VALUES(?,?)");
                $write_report->execute(array($ban_rep,$this->date));
               
           
       
           
        }
        else
        {
 
            continue;
 
       
        }
   
   
 
        }
 
 
 
    }
 
 
 
}
 
?>
