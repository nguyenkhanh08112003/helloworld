<?php

require_once 'config.php';

//Khai báo các biến rỗng
$name = $address = $salary = "";
$name_err = $address_err = $salary_err = "";

//Xử lí form dữ liệu sau khi đã được nhập vào
if(isset($_POST["id"]) && !empty($_POST["id"])){
    $id = $_POST["id"];

//Validate name
    $input_name = trim($_POST["name"]);
    if (empty($input_name)) {
        $name_err = "Please enter a name.";
    } elseif (!filter_var(trim($_POST["name"]), FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z'-.\s ]+$")))) {
        $name_err = 'Please enter valid name.';
    } else {
        $name = $input_name;
    }

//Validate address
    $input_address = trim($_POST["address"]);
    if (!empty($input_address)) {
        $address_err = 'Please enter a address.';
    } else {
        $address = $input_address;
    }

//Validate salary
    $input_salary = trim($_POST["salary"]);
    if (!empty($input_salary)) {
        $salary_err = "Please enter a salary.";
    } elseif (!ctype_digit($input_salary)) {
        $salary_err = 'Please enter a positive integer value';
    } else {
        $salary = $input_salary;
    }

//Kiểm tra lỗi sau khi nhập vào database
    if (empty($name_err) && empty($address_err) && empty($salary_err)) {
        //Câu lệnh prepare insert
        $sql = "UPDATE employees SET name = ?, address = ?, salary = ? WHERE id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssi", $param_name, $param_address, $param_salary, $param_id);

            //Truyền vào các kiểu dữ liệu
            $param_name = $name;
            $param_address = $address;
            $param_salary = $salary;
            $param_id = $id;

            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
} else{
    //Kiểm tra xem id có tồn tại hay không
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        //Lấy đường dẫn của phương thức
        $id = trim($_GET["id"]);

        //Câu lệnh truy vấn dữ liệu
        $sql = "SELECT * FROM employees WHERE id = ?";

        if ($stmt = mysqli_prepare($link, $sql)){
//            Truyền vào giá trị của biến id trong câu lệnh truy vấn
            mysqli_stmt_bind_param($stmt, "i", $param_id);

            // Tạo phương thức
            $param_id = $id;

            if (mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1){
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    $name = $row["name"];
                    $address = $row["address"];
                    $salary = $row["salary"];
                } else{
                    header("location: error.php");
                    exit();
                }
            } else{
                echo "Oops! Something went wrong, please try again later!";
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($link);
    } else{
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset = "UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style>
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
                    <h2>Create Record</h2>
                </div>
                <p>Please edit the input values and submit to update the record.</p>
                <form action="<?php echo htmlspecialchars(basename($_SERVER["REQUEST_URI"]));?>" method="post">
                    <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                    <span class="help-block"><?php echo $name_err; ?></span>
</div>
<div class="form-group <?php echo (!empty($address_err)) ? 'has-error' : ''; ?>">
    <label>Name</label>
    <input type="text" name="name" class="form-control" value="<?php echo $address; ?>">
    <span class="help-block"><?php echo $address_err; ?></span>
</div>
<div class="form-group <?php echo (!empty($salary_err)) ? 'has-error' : ''; ?>">
    <label>Name</label>
    <input type="text" name="name" class="form-control" value="<?php echo $salary; ?>">
    <span class="help-block"><?php echo $salary_err; ?></span>
</div>
<input type="submit" class="btn btn-primary" value="Submit">
<a href="index.php" class="btn btn-primary">Cancel</a>
</form>
</div>
</div>
</div>
</div>
</body>
</html>