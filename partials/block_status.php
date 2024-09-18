<?php
include_once '_dbconnect.php';
use PHPMailer\PHPMailer\PHPMailer;
//accessing the all php mailer files
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';
$smtp = array(
  //accessing the gmail 
  'host' => 'smtp.gmail.com',
  'port' => 587,
  'username' => 'roadhealthtracker@gmail.com',
  'password' => 'bssh gzmv wszb ecik',
  'SMTPSecure' => PHPMailer::ENCRYPTION_STARTTLS
);
$mail = new PHPMailer();
//calling the SMTP function
$mail->isSMTP();
$mail->SMTPDebug = 0;
//assigning the host,port,SMTPSecure,username,password
$mail->Host = $smtp['host'];
$mail->Port = $smtp['port'];
$mail->SMTPSecure = $smtp['SMTPSecure'];
$mail->SMTPAuth = true;
$mail->Username = $smtp['username'];
$mail->Password = $smtp['password'];

if (isset($_POST['id'])) {
    
    $message=$_POST['msg'];
    $id = $_POST['id'];

    // Prepare the query to update columns.
    $sql = "UPDATE complaintdb SET comfirm = ? ,status = 'Blocked' WHERE sr = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
       die('Error: ' . $conn->error);
    }
 
    // Bind parameters
    $bind_success = $stmt->bind_param('ss',$message, $id);

    if (!$bind_success) {
        die('Error: ' . $stmt->error);
    }

    // Execute the query

    if($stmt->execute()){
      //query to select all complaintdbn and search it.
        $msql="SELECT * from complaintdb WHERE sr = '$id'";
        $result = mysqli_query($conn, $msql);
        $row = mysqli_fetch_assoc($result);
        $email=$row['eid'];
        $fname=$row['name'];     
        //setting mail   
        $mail->setFrom('roadhealthtracker@gmail.com', 'Road Health Tracker');
        $mail->addAddress($email);
        $mail->addReplyTo('roadhealthtracker@gmail.com', 'Road Health Tracker');
        $mail->isHTML(true);
        //subject
        $mail->Subject = "Your comlaint is rejected.";
        //body
        $mail->Body = "<b>Your Complaint is Rejected.</b>
                        <br><br>
                        Hi,$fname.<br>
                        Your submitted complaint id C_00$id is rejected due to<b> $message.</b> 
                        <br>If any query is there then please contact us using helpdesk. 
                        <br>Please see your complaint or feedbacks status in your profile page.
                        <br>
                        <br><br>
                        <br><br><br><br>Thanks & regards,<br>RHT.";
        if ($mail->send()) 
          {
            $success = "A new password has been sent to your email address.";
          }
          else
          {
            die('Error: ' . $mail);
          }
    }
    else
    {
      die('Error: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
