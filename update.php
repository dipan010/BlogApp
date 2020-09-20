<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$topic = $post = $comment = "";
$topic_err = $post_err =  "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate topic
    $input_topic = trim($_POST["topic"]);
    if(empty($input_topic)){
        $topic_err = "Please enter a topic.";
    } elseif(!filter_var($input_topic, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $topic_err = "Please enter a valid topic.";
    } else{
        $topic = $input_topic;
    }
    
    // Validate post post
    $input_post = trim($_POST["post"]);
    if(empty($input_post)){
        $post_err = "Please enter an post.";     
    } else{
        $post = $input_post;
    }
    
    // Validate comment
    $input_comment = trim($_POST["comment"]);
    
    // Check input errors before inserting in database
    if(empty($topic_err) && empty($post_err) ){
        // Prepare an update statement
        $sql = "UPDATE employees SET topic=?, post=? WHERE id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssi", $param_topic, $param_post, $param_comment, $param_id);
            
            // Set parameters
            $param_topic = $topic;
            $param_post = $post;
            $param_comment = $comment;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
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
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM blog WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $topic = $row["topic"];
                    $post = $row["post"];
                    $comment = $row["comment"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
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
                        <h2>Update Record</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basetopic($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group <?php echo (!empty($topic_err)) ? 'has-error' : ''; ?>">
                            <label>topic</label>
                            <input type="text" name="topic" class="form-control" value="<?php echo $topic; ?>">
                            <span class="help-block"><?php echo $topic_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($post_err)) ? 'has-error' : ''; ?>">
                            <label>post</label>
                            <textarea name="post" class="form-control"><?php echo $post; ?></textarea>
                            <span class="help-block"><?php echo $post_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>comment</label>
                            <input type="text" name="comment" class="form-control" value="<?php echo $comment; ?>">
                            <span class="help-block"></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>