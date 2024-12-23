<?php
session_start();
include("connection.php"); 
class User {
    private $conn;

    // Constructor to initialize the database connection
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Method to check login credentials
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM tblvendor WHERE fld_email = ? AND fld_password = ?");
        $stmt->bind_param("ss", $email, $password); // Bind parameters
        $stmt->execute(); // Execute the query
        $result = $stmt->get_result(); // Get the result

        if ($result->num_rows > 0) {
            return true; // Valid login
        } else {
            return false; // Invalid login
        }
    }
}

// Check if the user is already logged in
if (isset($_SESSION['id'])) {
    header("Location: food.php");
    exit();
}

// Initialize the User class and handle login
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? ''; // Fetch the username from POST
    $pswd = $_POST['pswd'] ?? ''; // Fetch the password from POST

    $user = new User($con); // Create an instance of the User class

    if ($user->login($username, $pswd)) {
        $_SESSION['id'] = $username; // Set session
        header("Location: food.php");
        exit();
    } else {
        $admin_login_error = "Invalid Username or Password"; // Login error message
    }
}
?>


<head>
  <meta charset="UTF-8">
    <title>Restaurant Login</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
		
		<style>
		ul li{}
		ul li a {color:white;padding:40px; }
		ul li a:hover {color:white;}
		</style>
</head>
<body>
<div style="background-image: url('img/login1.avif'); height: 100vh; width: 100vw;">
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
  
    <a class="navbar-brand" href="index.php"><span style="color:red;font-family: 'Permanent Marker', cursive;">Foodie Express</span></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
	
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="index.php">Home
                
              </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="aboutus.php">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="services.php">Services</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contact.php">Contact</a>
        </li>
		
		
      </ul>
	  
    </div>
	
</nav>

<div class="middle" style=" position:fixed; padding:40px; border:1px solid #ED2553; left:30%; top:30%; width:400px;">
       <ul class="nav nav-tabs nabbar_inverse" id="myTab" style="background:#ED2553;border-radius:10px 10px 10px 10px;" role="tablist">
          <li class="nav-item">
             <a class="nav-link active" id="home-tab" data-toggle="tab" href="#login" role="tab" aria-controls="home" aria-selected="true">Restaurant Login</a>
          </li>
         
              <a class="nav-link" id="profile-tab" style="color:white;"    aria-controls="profile" aria-selected="false">Welcome</a>
          
       </ul>
	   <br><br>
	   <div class="tab-content" id="myTabContent">
	   <!--login Section-- starts-->
            <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="home-tab">
			    <div class="footer" style="color:red;"><?php if(isset($admin_login_error)){ echo $admin_login_error;}?></div>
			  <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                           <label for="username">Username:</label>
                           <input type="text" class="form-control" id="username" placeholder="Enter Username" name="username" required/>
                        </div>
                        <div class="form-group">
                             <label for="pwd">Password:</label>
                             <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="pswd" required/>
                        </div>
                        
                          <button type="submit" name="login" class="btn btn-primary">Submit</button>
                          <a href="vendor-new.php"><button type="button" name="new" class="btn btn-warning">Sign Up for New Account</button></a>
                 </form>
			</div>
			<!--login Section-- ends-->
			
			
            
      </div>
	  </div>
	   
</body>
