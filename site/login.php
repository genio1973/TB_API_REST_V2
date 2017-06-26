<?php
session_start();
require_once 'config/db.php';
require_once 'class/ApiSimpleGetRestClient.php';

$msgError="";
if(isset($_POST['users_pass']))
{
    // Grab User submitted information
    $email = filter_var($_POST["users_email"], FILTER_SANITIZE_EMAIL);
    $pass = filter_var($_POST["users_pass"], FILTER_SANITIZE_STRING);

    //Use API to connect
    $client = new  ApiSimpleGetRestClient('http://test.romandvolley.ch/api/v1/public/login');
    $response = $client->get($email.'/'.$pass);
    $user = json_decode(($response), false);
    if($user!==null)
    {
        $msgError = 'Connexion réussie - ' . $user->email;
        $_SESSION['user']=$user;
    }
    else
    {
        $msgError = 'Utilisateur non reconnu !';
    }
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RomandVolley</title>
	<!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
     <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

</head>
<body>
    <div class="container">
        <div class="row text-center ">
            <div class="col-md-12">
                <br /><br />
                <h2> RomandVolley : Connexion (email)</h2>
               
                <h5>( Accèder à la zone privée )</h5>
                 <br />
            </div>
        </div>
         <div class="row ">
               
                  <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                        <strong>   Enter Details To Login </strong>  
                            </div>
                            <div class="panel-body">
                                <form role="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                       <br />
                                     <div class="form-group input-group">
                                            <span class="input-group-addon"><i class="fa fa-tag"  ></i></span>
                                            <input type="text" name="users_email" class="form-control" placeholder="Adresse email " />
                                        </div>
                                        <div class="form-group input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"  ></i></span>
                                            <input type="password" name="users_pass" class="form-control"  placeholder="Mot de passe" />
                                        </div>
    
                                     <?php
                                     if($msgError!=="")
                                     {
                                            echo ' <div class="form-group input-group"> <span class="text-danger">'.$msgError.'</span></div>';
                                     }
                                     ?>
                                    <div class="form-group">
                                            <span class="pull-right">
                                                   <a href="#" >Mot de passe oublié ? </a> 
                                            </span>
                                        </div>

                                     <input type="submit" value="Connecter" class="btn btn-primary"/>
                                    <hr />
                                    Par encore de compte ? <a href="registeration.php" >cliquer ici </a> 
                                    </form>
                            </div>
                           
                        </div>
                    </div>
                
                
        </div>
    </div>


     <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS -->
    <script src="assets/js/jquery-1.10.2.js"></script>
      <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="assets/js/jquery.metisMenu.js"></script>
      <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
   
</body>
</html>
