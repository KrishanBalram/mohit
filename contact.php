<?php
session_start();
include("connection.php");

class CustomerSession
{
    private $db;
    private $custId = "";

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
        $this->initializeCustomer();
    }

    private function initializeCustomer()
    {
        if (isset($_SESSION['cust_id'])) {
            $this->custId = $_SESSION['cust_id'];
            $query = $this->db->prepare("SELECT * FROM tblcustomer WHERE fld_email = ?");
            $query->bind_param("s", $this->custId);
            $query->execute();
            $query->get_result()->fetch_array();
        }
    }

    public function getCustomerId()
    {
        return $this->custId;
    }
}

class FoodVendor
{
    private $db;
    private $foodArray = [];

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function fetchVendorsWithFood()
    {
        $query = $this->db->query("SELECT tblvendor.fld_name, tblvendor.fldvendor_id, tblvendor.fld_email,
            tblvendor.fld_mob, tblvendor.fld_address, tblvendor.fld_logo, tbfood.food_id, tbfood.foodname, 
            tbfood.cost, tbfood.cuisines, tbfood.paymentmode 
            FROM tblvendor 
            INNER JOIN tbfood ON tblvendor.fldvendor_id = tbfood.fldvendor_id");

        while ($row = $query->fetch_array()) {
            $this->foodArray[] = $row['food_id'];
        }

        shuffle($this->foodArray);
    }

    public function getFoodArray()
    {
        return $this->foodArray;
    }
}

class CartActions
{
    private $db;
    private $custId;

    public function __construct($dbConnection, $custId)
    {
        $this->db = $dbConnection;
        $this->custId = $custId;
    }

    public function addToCart($productId)
    {
        if (!empty($this->custId)) {
            $_SESSION['cust_id'] = $this->custId;
            header("Location: form/cart.php?product=$productId");
            exit;
        } else {
            header("Location: form/?product=$productId");
            exit;
        }
    }

    public function fetchCartDetails()
    {
        $query = $this->db->prepare("SELECT tbfood.foodname, tbfood.fldvendor_id, tbfood.cost, tbfood.cuisines, 
            tbfood.fldimage, tblcart.fld_cart_id, tblcart.fld_product_id, tblcart.fld_customer_id 
            FROM tbfood 
            INNER JOIN tblcart ON tbfood.food_id = tblcart.fld_product_id 
            WHERE tblcart.fld_customer_id = ?");
        $query->bind_param("s", $this->custId);
        $query->execute();
        $result = $query->get_result();

        return $result->num_rows;
    }
}

class MessageHandler
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function sendMessage($name, $email, $phone, $message)
    {
        $query = $this->db->prepare("INSERT INTO tblmessage(fld_name, fld_email, fld_phone, fld_msg) 
            VALUES (?, ?, ?, ?)");
        $query->bind_param("ssss", $name, $email, $phone, $message);

        if ($query->execute()) {
            echo "<script>alert('We will be Connecting You shortly')</script>";
        } else {
            echo "Failed to send message.";
        }
    }
}

class UserActions
{
    public static function login()
    {
        header("Location: form/index.php");
        exit;
    }

    public static function logout()
    {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}

// Initialize objects
$customerSession = new CustomerSession($con);
$foodVendor = new FoodVendor($con);
$cartActions = new CartActions($con, $customerSession->getCustomerId());
$messageHandler = new MessageHandler($con);

// Fetch vendors and food
$foodVendor->fetchVendorsWithFood();
$foodArray = $foodVendor->getFoodArray();

// Handle requests
if (isset($_POST['addtocart'])) {
    $cartActions->addToCart($_POST['addtocart']);
}

if (isset($_POST['login'])) {
    UserActions::login();
}

if (isset($_POST['logout'])) {
    UserActions::logout();
}

if (isset($_POST['message'])) {
    $messageHandler->sendMessage($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['msgtxt']);
}

// Get cart details
$cartItemsCount = $cartActions->fetchCartDetails();
?>

<html>
  <head>
     <title>Contact-us</title>
	 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	 <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
     <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
      <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
     <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
	 <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	 <link href="https://fonts.googleapis.com/css?family=Great+Vibes|Permanent+Marker" rel="stylesheet">
     
	 <style>
	 .carousel-item {
  height: 100vh;
  min-height: 350px;
  background: no-repeat center center scroll;
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}
	 </style>
	 
	 
	 <script>
	 //search product function
            $(document).ready(function(){
	
	             $("#search_text").keypress(function()
	                      {
	                       load_data();
	                       function load_data(query)
	                           {
		                        $.ajax({
			                    url:"fetch.php",
			                    method:"post",
			                    data:{query:query},
			                    success:function(data)
			                                 {
				                               $('#result').html(data);
			                                  }
		                                });
	                             }
	
	                           $('#search_text').keyup(function(){
		                       var search = $(this).val();
		                           if(search != '')
		                               {
			                             load_data(search);
		                                }
		                            else
		                             {
			                         load_data();			
		                              }
	                                });
	                              });
	                            });
</script>
<style>
ul li {list-style:none;}
ul li a{color:black; font-weight:bold;}
ul li a:hover{text-decoration:none;}
</style>
  </head>
  
    
<body>
<div id="result" style="position:fixed;top:100; right:50;z-index: 3000;width:350px;background:white;"></div>
<!--navbar start-->
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
  
    <a class="navbar-brand" href="index.php"><span style="color:red;font-family: 'Permanent Marker', cursive;">Foodie Express</span></a>
    <?php
	if(!empty($cust_id))
	{
	?>
	<a class="navbar-brand" style="color:black; text-decoratio:none;"><i class="far fa-user"><?php if(isset($cust_id)) { echo $qqr['fld_name']; }?></i></a>
	<?php
	}
	?>
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
		<li class="nav-item">
		  <form method="post">
          <?php
			if(empty($cust_id))
			{
			?>
			<a href="form/index.php?msg=you must be login first"><span style="color:red; font-size:30px;"><i class="fa fa-shopping-cart" aria-hidden="true"><span style="color:red;" id="cart"  class="badge badge-light">0</span></i></span></a>
			
			&nbsp;&nbsp;&nbsp;
			<button class="btn btn-outline-danger my-2 my-sm-0" name="login" type="submit">Log In</button>&nbsp;&nbsp;&nbsp;
            <?php
			}
			else
			{
			?>
			<a href="form/cart.php"><span style=" color:green; font-size:30px;"><i class="fa fa-shopping-cart" aria-hidden="true"><span style="color:green;" id="cart"  class="badge badge-light"><?php if(isset($re)) { echo $re; }?></span></i></span></a>
			<button class="btn btn-outline-success my-2 my-sm-0" name="logout" type="submit">Log Out</button>&nbsp;&nbsp;&nbsp;
			<?php
			}
			?>
			</form>
        </li>
		
      </ul>
	  
    </div>
	
</nav>
<!--navbar ends-->

<br><br><br>
<div class="container-fluid">
  <img src="img/contact.webp" width="100%">
</div>
<br>
<div class="container">
  <div class="row">
    <div class="col-sm-8" style="padding:20px; border:1px solid #F0F0F0;">
	    <form method="post">
            <div class="form-group">
                 <input type="text" class="form-control"  placeholder="Name*" name="name" required/>
            </div>
			<div class="form-group">
                 <input type="email" class="form-control"  placeholder="email*" value="<?php if(isset($cust_id)) echo $cust_id; ?>" name="email" required/>
            </div>
			<div class="form-group">
                 <input type="tel" class="form-control" pattern="[6-9]{1}[0-9]{9}"  name="phone" placeholder="Phone(optinal) EX 9213298761"/>
            </div>
			<div class="form-group">
                <textarea class="form-control"    placeholder="Message*" name="msgtxt" rows="3" col="10" required/></textarea/>
            </div>
			<div class="form-group">
                   <button type="submit" name="message" class="btn btn-danger">Send Message</button>
            </div>
        </form>
	</div>
    <div class="col-sm-4" style="padding:30px;">
	   <div class="form-group">
           <i class="fa fa-phone" aria-hidden="true"></i>&nbsp;<b>1234567890</b><br><br>
			<i class="fa fa-home" aria-hidden="true"></i>&nbsp; Pulincunno<br>(24*7 Days)
	       
	   </div>
	</div>
  </div>
</div>
<br><br>
  <?php
			include("footer.php");
			?>





	</body>
</html>