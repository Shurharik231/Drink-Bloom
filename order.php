<?php
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $comment = $_POST['comment'] ?? '';

    $screenshotPath = '';
    if(isset($_FILES['screenshot']) && $_FILES['screenshot']['error']===0){
        $ext = pathinfo($_FILES['screenshot']['name'], PATHINFO_EXTENSION);
        $filename = 'uploads/'.time().'_'.rand(1000,9999).'.'.$ext;
        if(!is_dir('uploads')) mkdir('uploads',0777,true);
        move_uploaded_file($_FILES['screenshot']['tmp_name'],$filename);
        $screenshotPath = $filename;
    }

    $orders = json_decode(file_get_contents('orders.json'),true) ?? [];
    $orders[] = [
        'product_id'=>$id,
        'name'=>$name,
        'phone'=>$phone,
        'address'=>$address,
        'comment'=>$comment,
        'screenshot'=>$screenshotPath,
        'date'=>date('Y-m-d H:i:s')
    ];
    file_put_contents('orders.json',json_encode($orders,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

    http_response_code(200);
} else { http_response_code(405); }