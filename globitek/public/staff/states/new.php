<?php
require_once('../../../private/initialize.php');
//require_once('../../../private/csrf_functions.php');
require_login();
if(!isset($_GET['id'])) {
  redirect_to('../index.php');
}

// Set default values for all variables the page needs.
$errors = array();
$state = array(
  'name' => '',
  'code' => '',
  'country_id' => $_GET['id']
);
//Functions
function csrf_token_tag(){
    $token_tag = create_csrf_token();
    return '<input type="hidden" name="csrf_token" value='".$token_tag."'/>';
}

function create_csrf_token() {
    $token = csrf_token();
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();
    return $token;
  }

  function csrf_token_is_valid() {
    if(!isset($_POST['csrf_token'])) {
        return false;
    }
    if(!isset($_SESSION['csrf_token'])) {
      return false;
    }
    return ($_POST['csrf_token'] === $_SESSION['csrf_token']);
  }

if(is_post_request()) {
  if(!($_SERVER['REQUEST_METHOD'] == 'GET')){
    //First double check if requests are from same domain
    if(request_is_same_domain()){
      // Confirm that values are present before accessing them.
      if(isset($_POST['name'])) { $state['name'] = $_POST['name']; }
      if(isset($_POST['code'])) { $state['code'] = $_POST['code']; }

      //Need to generate session csrf token and store it
      // $token = csrf_token();
      // $_SESSION['csrf_token'] = $token;
      // $_SESSION['csrf_token_time'] = time();
      if(csrf_token_is_valid()){
        $result = insert_state($state);
        if($result === true) {
          $new_id = db_insert_id($db);
          redirect_to('show.php?id=' . $new_id);
        } else {
          $errors = $result;
        }
      } else{
        $errors = "Invalid Request";
      }
    } else{
      $errors= "Not from same domain";
    }
  } else{
      //Tried to access form data through GET request
      exit();
  }
}
?>
<?php $page_title = 'Staff: New State'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="main-content">
  <a href="../countries/show.php?id=<?php echo h($state['country_id']); ?>">Back to Country</a><br />

  <h1>New State</h1>

  <?php echo display_errors($errors); ?>

  <form action="new.php?id=<?php echo h($state['country_id']); ?>" method="post">
    Name:<br />
    <input type="text" name="name" value="<?php echo h($state['name']); ?>" /><br />
    Code:<br />
    <input type="text" name="code" value="<?php echo h($state['code']); ?>" /><br />
    <br />
    <input type="submit" name="submit" value="Create"  />
    <?php echo csrf_token_tag(); ?>
  </form>

</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
