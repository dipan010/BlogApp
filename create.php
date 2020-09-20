<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$topic = $post = $comment = "";
$topic_err = $post_err= "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $input_topic = trim($_POST["topic"]);
    if(empty($input_topic)){
        $topic_err = "Enter a Topic.";
    } elseif(!filter_var($input_topic, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $topic_err = "Please enter a topic.";
    } else{
        $topic = $topic_name;
    }
    
    // Validate address
    $input_post = trim($_POST["post"]);
    if(empty($input_post)){
        $post_err = "Enter your post.";     
    } else{
        $post = $input_post;
    }
    
    // Validate salary
    $input_comment = trim($_POST["comment"]);
    /*if(empty($input_salary)){
        $salary_err = "Please enter the salary amount.";     
    //} elseif(!ctype_digit($input_salary)){
        $salary_err = "Please enter a positive integer value.";
    //} else{
        $salary = $input_salary;
    }*/
    
    // Check input errors before inserting in database
    if(empty($topic_err) && empty($post_err) ){
        // Prepare an insert statement
        $sql = "INSERT INTO blog (topic, post, comment) VALUES (?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_topic, $param_post, $param_comment);
            
            // Set parameters
            $param_topic = $topic;
            $param_post = $post;
            $param_comment = $comment;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Create a Blog</h2>
                    </div>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($topic_err)) ? 'has-error' : ''; ?>">
                            <label>Topic</label>
                            <input type="text" name="topic" class="form-control" value="<?php echo $topic; ?>">
                            <span class="help-block"><?php echo $topic_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($post_err)) ? 'has-error' : ''; ?>">
                            <label>Post</label>
                            <textarea name="post" class="form-control"><?php echo $post; ?></textarea>
                            <span class="help-block"><?php echo $post_err;?></span>
                        </div>
                        <div class="form-group ">
                            <label>Salary</label>
                            <input type="text" name="comment" class="form-control" value="<?php echo $comment; ?>">
                            <span class="help-block"></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>