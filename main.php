<?php
    include "class.phpmailer.php"; //for mail functions
 
    if (isset($_GET['reason'])) {
 
        $reason=htmlspecialchars($_GET['reason']);
 
        if ($reason == "xss" || $reason == "sql" || $reason == "lfi" || $reason == "rfi") {
 
        #hack attemp detected
        #logo etc whatever you want
   
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
    public $attack_limit; //If the specified limit is exceeded, the user will be blocked from the system
    public $auth_password; //authpassword to clear the tables or ban control
    public $email_from; //Required informations for send email
    public $email_to;
    public $email_password;
    public $email_option;
    public $email_smtp;
    public $email_port;
    public $outdate_limit;
 
 
 
 
 
 
    public function __construct() //contructor method
        {
 
            $this->date=date('d.m.Y - H:i:s');
            //date time now
            $this->ip=$this->getIp();
            //get ip adress
            $this->referer_address=$this->get_Referer();
            //referer adress(if not available will be unknown)
            $this->attack=$this->get_Reason();
            //type of attack(like xss,sql,lfi)
 
            $this->current_year=substr($this->date,6,4);
            $this->current_month=substr($this->date,3,2);
            $this->current_day=substr($this->date,0,2);
            $this->current_hour=substr($this->date,13,2);
 
        }
 
 
 
protected function getIp()  //function to get ip address
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
 
protected function get_Reason() //function to get reason
{
    if (isset($_GET['reason']))
    {
        $this->reason =htmlspecialchars($_GET['reason']);/*redirect to this page from htaccess and get reason*/
 
 
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
 
protected function get_Referer() //get referer page
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
 
    //database information
    $this->db_name=$db_name;
    $this->db_host=$db_host;
    $this->db_username=$db_username;
    $this->db_password=$db_password;
    $dsn="mysql:host=$this->db_host;dbname=$this->db_name;charset=utf8";
    $this->db_connection=new PDO($dsn,$this->db_username,$this->db_password);//database connection (PDO)
}
 
 
public function set_attack_limit($limit) //attack limit(per hour)
{
    $this->attack_limit=$limit;
}
 
public function set_auth_password($password) //authpassword to clear the tables or ban check
{
    $this->auth_password=$password;
 
}
 
 
public function set_time_zone($time_zone) //time zone set(for php)
{
    date_default_timezone_set($time_zone);
}
 
public function email_report($select) //email report option if you want to receive email $select must be "send"
{
    $this->email_option=$select;
 
}
 
public function set_email_data($from,$to,$password,$smtp,$port) //email settings (for send mail)
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
    /*deny request from banned user*/
 
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
            // e-post sent
          }
          else
          {
              // ops error
            echo $mail->ErrorInfo;
          }
}
 
 
public function attack_controll() //main attack control function
{
 
    $check_ip=$this->db_connection->prepare("SELECT id FROM controll where ip_adress=? ");
    $check_ip->execute(array($this->ip));
    /*check ip adress in database.*/
    $ip_reg_count=$check_ip->rowCount();
    /*if count = 0 create new registration*/
    if($ip_reg_count == 0){
 
   
        $add_to_db=$this->db_connection->prepare("INSERT INTO controll(ip_adress,first_conn,last_conn,attack_report) VALUES(?,?,?,?) ");
        $add_to_db->execute(array($this->ip,$this->date,$this->date,"Referer Adress:".$this->referer_address." Attack Type:".$this->attack."/"));
 
        /*Insert needed informations
   
        */
    }
    else
    { //if reg count higher than 0
 
 
 
    $get_info=$this->db_connection->prepare("SELECT id,ip_adress,first_conn,last_conn,attack_report FROM controll where ip_adress = ? ");
    $get_info->execute(array($this->ip));
    $informations=$get_info->fetchAll();
 
    //recieve information of this ip adress
 
    foreach ($informations as $data)
    {
 
        $attack_report=$data['attack_report'];
        if(strpos($attack_report, ","))
        {
            $det_att_count=count(explode(",",$attack_report)); //detected attack number
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
            /* delete old registration(banned period finished)*/
 
            $old_reg="IP Adress:$this->ip\nFirst Connection:$first_conn - Last Connection:$last_conn\nAttack Report:$attack_report    Detected Attack Number:$det_att_count\n(This informations have been archived because new attack detected from same adress)\n";
 
            $report_old=$this->db_connection->prepare("INSERT INTO reports(report,date) VALUES(?,?)");
            $report_old->execute(array($old_reg,$this->date));
            /*report to old informations*/
            #rapor=report
            #tarih=date
 
            $new_reg=$this->db_connection->prepare("INSERT INTO controll(ip_adress,first_conn,last_conn,attack_report) VALUES(?,?,?,?) ");
            $new_reg->execute(array($this->ip,$this->date,$this->date,"Referer Adress:".$this->referer_address." Attack Type:".$this->attack."/"));
            /*create new one*/
        }
        else
        {
 
        if($det_att_count >= $this->attack_limit)/*If the specified limit is exceeded, the user will be banned from the system*/
            {
           
 
           
                $remove_reg=$this->db_connection->prepare("DELETE FROM controll where ip_adress=?");
                $remove_reg->execute(array($this->ip));
                /*remove the control table data*/
 
                $report_old=$this->db_connection->prepare("INSERT INTO block(ip_adress,ban_date,ban_reason,first_conn,last_conn) VALUES(?,?,?,?,?)");
                $report_old->execute(array($this->ip,$this->date,$attack_report,$first_det_time,$last_det_time));
                /*create new report for the old control table data*/
           
           
                $this->htaccess_blocker($this->ip);
                /*deny request from banned user*/
            }
            else
            {
                $new_reg=$attack_report.","."Referer Adress:".$this->referer_address." Attack Type:".$this->attack."/";
                $update_reg=$this->db_connection->prepare("UPDATE controll SET attack_report = ? ,last_conn = ? where id = ?");
                $update_reg->execute(array($new_reg,$this->date,$id_number));
                //update current user attack data
           
 
            }
 
        }
       
   
 
        }
 
        }
 
 
 
}//function end
 
 
 
function cleaner()/*clear outdated regs*/
{
 
    $inf_report ="--Deleted rows texts given below.--\n\n";
    $removed_cntrl_row_count=0;
    $removed_rprt_row_count=0;
 
 
    $controll_tbl=$this->db_connection->query("SELECT id,ip_adress,first_conn,last_conn,attack_report FROM controll");
    $content=$controll_tbl->fetchAll();
    /*fetch all data from control table*/
 
    foreach($content as $data)
    {
        /*get datas from control table.do functions for each one*/
        $id_number=$data['id'];
        $ip_adress=$data['ip_adress'];
        $first_conn=$data['first_conn'];
        $last_conn=$data['last_conn'];
        $attack_report=$data['attack_report'];
 
        /* get needed data*/
        /* get days,month etc */
 
        $last_det_month=substr($last_conn,3,2);
        $last_det_day=substr($last_conn,0,2);
 
        /*Delete outdated regs(limit is optinal)*/
 
        if ($last_det_month != $this->current_month || $this->current_day >= $last_det_day+$this->outdate_limit)
        {
            $inf_report .= "<br><br>@Controll : IP Adress:$ip_adress<br>First Connection:$first_conn - Last Connection:$last_conn<br>Detected Attacks and Adresses:$attack_report<br>";
 
            $remove_reg=$this->db_connection->prepare("DELETE FROM controll where id=? ");
            $remove_reg->execute(array($id_number));
            $removed_cntrl_row_count++;
            //clear old data(day time is optional)
 
        }
 
        else
        {
 
        }
 
        //create report
        }
 
 
 
 
        $report_tbl=$this->db_connection->query("SELECT id,report,date FROM reports"); #rapor -> report tarih -> date tablo adı reports
        $get_data=$report_tbl->fetchAll();
        /*list all data from reports table*/
        foreach($get_data as $report_tbl_data)
        {
 
            $id_number=$report_tbl_data['id'];
            $reported_data=$report_tbl_data['report'];
            $report_date=$report_tbl_data['date'];
 
 
 
            $report_month=substr($report_date,3,2);
            $report_day=substr($report_date,0,2);
 
            if($report_month != $this->current_month || $this->current_day >= $report_day+$this->outdate_limit)//limit is optional
            {
                $inf_report .= "<br>@Reports_Tbl : $reported_data<br>Report Date : $report_date<br>";
 
                $remove_reg=$this->db_connection->prepare("DELETE FROM reports where id =? ");
                $remove_reg->execute(array($id_number));
                $removed_rprt_row_count++;
                //clear outdated reg
            }
            else
            {
 
            }
 
            //add to report
 
 
        }
 
 
        $statistics = "\nDeleted $removed_cntrl_row_count row from controll table , Deleted $removed_rprt_row_count row from reports table.Information mail has sent.($this->date)";
        $inf_report .= "\n\n\n--($this->date)--";
 
        $write_report=$this->db_connection->prepare("INSERT INTO reports(report,date) VALUES(?,?)");
        $write_report->execute(array($statistics,$this->date));
        /*insert report to database*/
        if (isset($this->email_option))
        {
 
            if($this->email_option=="send")
            {
                $this->send_mail("Website Reporter",$inf_report);
                /*send email to website owner(optional)*/
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
            /*check ban period*/
 
                $ban_rep="IP Adress: $ip_adress First Connection: $first_conn  Last Connection: $last_conn  Ban Reason: $attack_report    Ban period finished.($this->date) ";
           
                $this->htaccess_unblocker($ip_adress);
           
                $delete_reg=$this->db_connection->prepare("DELETE FROM block where ip_adress=?");
                $delete_reg->execute(array($ip_adress));
                /*remove old reg from block table*/
 
           
                $write_report=$this->db_connection->prepare("INSERT INTO reports(report,date) VALUES(?,?)");
                $write_report->execute(array($ban_rep,$this->date));
                /*create new report and insert to reports*/
           
       
           
        }
        else
        {
 
            continue;
 
       
        }
   
   
 
        }
 
 
 
    }
 
 
 
}
 
?>