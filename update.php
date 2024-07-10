<?php
include_once 'dbConnection.php';
session_start();
$email=$_SESSION['email'];
//delete feedback
if(isset($_SESSION['key'])){
if(@$_GET['fdid'] && $_SESSION['key']=='sunny7785068889') {
$id=@$_GET['fdid'];
$result = mysqli_query($con,"DELETE FROM feedback WHERE id='$id' ") or die('Error');
header("location:dash.php?q=3");
}
}

//delete user
if(isset($_SESSION['key'])){
if(@$_GET['demail'] && $_SESSION['key']=='sunny7785068889') {
$demail=@$_GET['demail'];
$r1 = mysqli_query($con,"DELETE FROM rank WHERE email='$demail' ") or die('Error');
$r2 = mysqli_query($con,"DELETE FROM history WHERE email='$demail' ") or die('Error');
$result = mysqli_query($con,"DELETE FROM user WHERE email='$demail' ") or die('Error');
header("location:dash.php?q=1");
}
}
//remove quiz
if(isset($_SESSION['key'])){
if(@$_GET['q']== 'rmquiz' && $_SESSION['key']=='sunny7785068889') {
$eid=@$_GET['eid'];
$result = mysqli_query($con,"SELECT * FROM questions WHERE eid='$eid' ") or die('Error');
while($row = mysqli_fetch_array($result)) {
	$qid = $row['qid'];
$r1 = mysqli_query($con,"DELETE FROM options WHERE qid='$qid'") or die('Error');
$r2 = mysqli_query($con,"DELETE FROM answer WHERE qid='$qid' ") or die('Error');
}
$r3 = mysqli_query($con,"DELETE FROM questions WHERE eid='$eid' ") or die('Error');
$r4 = mysqli_query($con,"DELETE FROM quiz WHERE eid='$eid' ") or die('Error');
$r4 = mysqli_query($con,"DELETE FROM history WHERE eid='$eid' ") or die('Error');

header("location:dash.php?q=5");
}
}

//add quiz
if(isset($_SESSION['key'])){
if(@$_GET['q']== 'addquiz' && $_SESSION['key']=='sunny7785068889') {
$name = $_POST['name'];
$name= ucwords(strtolower($name));
$total = $_POST['total'];
$sahi = $_POST['right'];
$wrong = $_POST['wrong'];
$time = $_POST['time'];
$tag = $_POST['tag'];
$desc = $_POST['desc'];
$id=uniqid();
$q3=mysqli_query($con,"INSERT INTO quiz VALUES  ('$id','$name' , '$sahi' , '$wrong','$total','$time' ,'$desc','$tag', NOW())");

header("location:dash.php?q=4&step=2&eid=$id&n=$total");
}
}

//add question
if(isset($_SESSION['key'])){
if(@$_GET['q']== 'addqns' && $_SESSION['key']=='sunny7785068889') {
$n=@$_GET['n'];
$eid=@$_GET['eid'];
$ch=@$_GET['ch'];

for($i=1;$i<=$n;$i++)
 {
 $qid=uniqid();
 $qns=$_POST['qns'.$i];
$q3=mysqli_query($con,"INSERT INTO questions VALUES  ('$eid','$qid','$qns' , '$ch' , '$i')");
  $oaid=uniqid();
  $obid=uniqid();
$ocid=uniqid();
$odid=uniqid();
$a=$_POST[$i.'1'];
$b=$_POST[$i.'2'];
$c=$_POST[$i.'3'];
$d=$_POST[$i.'4'];
$qa=mysqli_query($con,"INSERT INTO options VALUES  ('$qid','$a','$oaid')") or die('Error61');
$qb=mysqli_query($con,"INSERT INTO options VALUES  ('$qid','$b','$obid')") or die('Error62');
$qc=mysqli_query($con,"INSERT INTO options VALUES  ('$qid','$c','$ocid')") or die('Error63');
$qd=mysqli_query($con,"INSERT INTO options VALUES  ('$qid','$d','$odid')") or die('Error64');
$e=$_POST['ans'.$i];
switch($e)
{
case 'a':
$ansid=$oaid;
break;
case 'b':
$ansid=$obid;
break;
case 'c':
$ansid=$ocid;
break;
case 'd':
$ansid=$odid;
break;
default:
$ansid=$oaid;
}


$qans=mysqli_query($con,"INSERT INTO answer VALUES  ('$qid','$ansid')");

 }
header("location:dash.php?q=0");
}
}

//quiz start
if (isset($_GET['q']) && $_GET['q'] == 'quiz' && isset($_GET['step']) && $_GET['step'] == 2) {
  $eid = mysqli_real_escape_string($con, $_GET['eid']);
  $sn = (int)$_GET['n'];
  $total = (int)$_GET['t'];
  $ans = isset($_POST['ans']) ? mysqli_real_escape_string($con, $_POST['ans']) : '';
  $qid = mysqli_real_escape_string($con, $_GET['qid']);

  // Obteniendo respuesta correcta
  $q = mysqli_query($con, "SELECT * FROM answer WHERE qid='$qid'");
  $ansid = '';
  while ($row = mysqli_fetch_array($q)) {
      $ansid = $row['ansid'];
  }

  if ($ans == $ansid) {
      $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid'");
      $sahi = 0;
      while ($row = mysqli_fetch_array($q)) {
          $sahi = $row['sahi'];
      }
      if ($sn == 1) {
          $q = mysqli_query($con, "INSERT INTO history (email, eid, score, sahi, wrong, level, date) VALUES('$email','$eid', 0, 0, 0, 0, NOW())");
          if (!$q) die('Error');
      }

      $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$eid' AND email='$email'");
      if (!$q) die('Error115');
      $s = 0;
      $r = 0;
      while ($row = mysqli_fetch_array($q)) {
          $s = $row['score'];
          $r = $row['sahi'];
      }
      $r++;
      $s += $sahi;
      $q = mysqli_query($con, "UPDATE history SET score=$s, level=$sn, sahi=$r, date=NOW() WHERE email='$email' AND eid='$eid'");
      if (!$q) die('Error124');
  } else {
      $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid'");
      if (!$q) die('Error129');
      $wrong = 0;
      while ($row = mysqli_fetch_array($q)) {
          $wrong = $row['wrong'];
      }
      if ($sn == 1) {
          $q = mysqli_query($con, "INSERT INTO history (email, eid, score, sahi, wrong, level, date) VALUES('$email','$eid', 0, 0, 0, 0, NOW())");
          if (!$q) die('Error137');
      }

      $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$eid' AND email='$email'");
      if (!$q) die('Error139');
      $s = 0;
      $w = 0;
      while ($row = mysqli_fetch_array($q)) {
          $s = $row['score'];
          $w = $row['wrong'];
      }
      $w++;
      $s -= $wrong;
      $q = mysqli_query($con, "UPDATE history SET score=$s, level=$sn, wrong=$w, date=NOW() WHERE email='$email' AND eid='$eid'");
      if (!$q) die('Error147');
  }

  if ($sn != $total) {
      $sn++;
      header("Location: account.php?q=quiz&step=2&eid=$eid&n=$sn&t=$total");
      exit();
  } else {
      if ($_SESSION['key'] != 'sunny7785068889') {
          $q = mysqli_query($con, "SELECT score FROM history WHERE eid='$eid' AND email='$email'");
          if (!$q) die('Error156');
          $s = 0;
          while ($row = mysqli_fetch_array($q)) {
              $s = $row['score'];
          }
          $q = mysqli_query($con, "SELECT * FROM rank WHERE email='$email'");
          if (!$q) die('Error161');
          $rowcount = mysqli_num_rows($q);
          if ($rowcount == 0) {
              $q2 = mysqli_query($con, "INSERT INTO rank (email, score, time) VALUES('$email','$s',NOW())");
              if (!$q2) die('Error165');
          } else {
              $sun = 0;
              while ($row = mysqli_fetch_array($q)) {
                  $sun = $row['score'];
              }
              $sun += $s;
              $q = mysqli_query($con, "UPDATE rank SET score=$sun, time=NOW() WHERE email='$email'");
              if (!$q) die('Error174');
          }
      }
      header("Location: account.php?q=result&eid=$eid");
      exit();
  }
}


//restart quiz
if(@$_GET['q']== 'quizre' && @$_GET['step']== 25 ) {
$eid=@$_GET['eid'];
$n=@$_GET['n'];
$t=@$_GET['t'];
$q=mysqli_query($con,"SELECT score FROM history WHERE eid='$eid' AND email='$email'" )or die('Error156');
while($row=mysqli_fetch_array($q) )
{
$s=$row['score'];
}
$q=mysqli_query($con,"DELETE FROM `history` WHERE eid='$eid' AND email='$email' " )or die('Error184');
$q=mysqli_query($con,"SELECT * FROM rank WHERE email='$email'" )or die('Error161');
while($row=mysqli_fetch_array($q) )
{
$sun=$row['score'];
}
$sun=$sun-$s;
$q=mysqli_query($con,"UPDATE `rank` SET `score`=$sun ,time=NOW() WHERE email= '$email'")or die('Error174');
header("location:account.php?q=quiz&step=2&eid=$eid&n=1&t=$t");
}

?>



