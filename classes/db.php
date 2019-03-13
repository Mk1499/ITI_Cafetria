<?php
// require_once('user.php');
// require_once('product.php');
// require_once('order.php');
class DbManager
{
    private $host = 'sql2.freemysqlhosting.net';
    private $db = 'sql2283138';
    private $user = 'sql2283138';
    private $pass = 'yF4!iH7*';
    private $charset = 'utf8mb4';
    private $dsn = '';
    private $pdo;

//      private $host = '127.0.0.1';
//      private $db = 'iti_cafe';
//      private $user = 'Motaz';
//      private $pass = 'motaz';
//      private $charset = 'utf8mb4';
//      private $dsn = "";
//      private $pdo;

    // private $host = 'localhost';
    // private $db = 'cafetria';
    // private $user = 'root';
    // private $pass = '';
    // private $charset = 'utf8mb4';
    // private $dsn = "";
    // private $pdo;

    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    public function __construct()
    {
        try {
            $this->dsn = "mysql:host=$this->host;dbname=$this->db";
            $this->pdo = new PDO($this->dsn, $this->user, $this->pass, $this->options);
            // echo "Success" ;
        } catch (PDOException $e) {
            // echo "ERROR";/PName
            var_dump($this->pdo);
        }
    }

    public function checks($start, $end, $uid, $page)
    {
        $dateCondition = '';
        $userCondition = '';
        $limitCondition = '';
        if (isset($start) && !empty($start) && isset($end) && !empty($end)) {
            $dateCondition = " and ( o.time BETWEEN CAST( '$start' AS DATETIME ) and CAST( '$end' AS DATETIME ) ) ";
        }
        if (isset($uid) && !empty($uid)) {
            $userCondition = " and u.id = $uid ";
        }
        if (isset($page) && !empty($page)) {
            $offset = $page > 0 ? ($page - 1) * 4 : 0;
            $limitCondition = " LIMIT 4 OFFSET $offset ";
        }
        $stmt = $this->pdo->prepare("SELECT u.id as UId ,u.name as UName,o.o_id As ONum , o.time as OTime , o.total as OTotal, po.price as PPrice , p.name as PName ,  po.number as PCount , p.img as PImg ,p.p_id as PId 
        FROM orders o,(select u.id as id , u.name as name from users u,orders o 
        where u.id = o.user_id  $dateCondition $limitCondition ) as u,products p,products_orders po WHERE
        o.o_id = po.order_id and p.p_id = po.product_id and u.id = o.user_id".$userCondition);
        $users = array();
        $stmt->execute();
        $user = $stmt->fetchAll();
        foreach ($user as $row) {
            if (!isset($users[$row['UId']]['Orders'][$row['ONum']]['Products'])) {
                $users[$row['UId']]['Orders'][$row['ONum']]['Products'] = array();
            }
            $users[$row['UId']]['UName'] = $row['UName'];
            $row['PImg'] = '../assets/images'.$row['PImg'];
            $users[$row['UId']]['Orders'][$row['ONum']]['OTime'] = $row['OTime'];
            $users[$row['UId']]['Orders'][$row['ONum']]['OTotal'] = $row['OTotal'];
            array_push($users[$row['UId']]['Orders'][$row['ONum']]['Products'], (array($row['PName'], $row['PCount'], $row['PPrice'], $row['PImg'])));
        }
        // print_r($users);
        return $users;
    }

    //pagination
    //SELECT o.o_id As oNum , o.time as OTime , o.total as total ,
    //o.status as status, po.price as PPrice , p.name as PName ,
    //po.number as PCount,p.img as img FROM
    //(SELECT ord.o_id, ord.time, ord.total ,
    //ord.status, ord.user_id FROM orders ord limit 4 OFFSET 4 ) as o,
    //products p,
    //products_orders po WHERE o.o_id = po.order_id
    //and p.p_id = po.product_id and o.user_id = 3

    public function userOrders($userId, $start, $end, $page)
    {
        $dateCondition = " and ( o.time BETWEEN CAST( '$start' AS DATETIME ) and CAST( '$end' AS DATETIME ) ) ";
        $offset = $page > 0 ? ($page - 1) * 4 : 0;
        $limitCondition = " LIMIT 4 OFFSET $offset ";
        $stmt = $this->pdo->prepare("SELECT o.o_id As oNum , o.time as OTime , o.total as total ,
                                              o.status as status, po.price as PPrice , p.name as PName ,
                                              po.number as PCount,p.img as img FROM
                                              (SELECT ord.o_id, ord.time, ord.total ,
                                              ord.status, ord.user_id FROM orders ord limit 4 OFFSET $offset ) as o,
                                              products p,
                                              products_orders po WHERE o.o_id = po.order_id
                                              and p.p_id = po.product_id and o.user_id = $userId
                                              ".$dateCondition);

        $orders = array();
        $stmt->execute();
        $order = $stmt->fetchAll();
        foreach ($order as $row) {
            if (!isset($orders[$row['oNum']]['Products'])) {
                $orders[$row['oNum']]['Products'] = array();
            }
            $orders[$row['oNum']]['oNum'] = $row['oNum'];
            $orders[$row['oNum']]['time'] = $row['OTime'];
            $orders[$row['oNum']]['status'] = $row['status'];
            $orders[$row['oNum']]['total'] = $row['total'];
            array_push($orders[$row['oNum']]['Products'], (array('PName' => $row['PName'], 'count' => $row['PCount'],
                    'price' => $row['PPrice'], 'img' => $row['img'], )));
            // print_r($users);
        }

        return $orders;
    }

    // Return All Product Function  Khaled
    public function allProduct()
    {
        $q = $this->pdo->query('SELECT * FROM `products` ');

        return $q;
    }

    public function createProduct($name, $price, $img, $category_id, $timestamp)
    {
        $stmt = $this->pdo->prepare('INSERT INTO products
                    VALUES ( DEFAULT , ? , ? , ? , ? , ? )');

        return $stmt->execute(array($name, $price, $img, $category_id, 'available'));
    }

    // Return Latest Product Function  Khaled

    public function latestProduct()
    {
        $q = $this->pdo->query('SELECT * FROM `products` LIMIT 1,3');

        return $q;
    }

    public function readCategory()
    {
        $query = 'SELECT
                    cat_id, name
                FROM
                    categories
                ORDER BY
                    name';
        $stmt = $this->pdo->prepare( $query );
        $stmt->execute();
        return $stmt;
        
}
//inserting user
public function insertUser ($name,$email,$password,$img,$room){
    $stmt = $this->pdo->prepare("INSERT INTO `users`(`name`, `email` , `password` , `img` , `room`) VALUES
            ('$name','$email' , '$password' , '$img' ,'$room')");
    $stmt->execute();
}

public function getRooms(){
    $query = "SELECT `room_num` FROM `rooms`";
    $stmt = $this->pdo->prepare( $query );
    $stmt->execute();
    return $stmt;

}

public function cancelOrder ($id) {
    $query = "DELETE FROM orders WHERE o_id = $id";
    $stmt = $this->pdo->prepare( $query );
    $stmt->execute();
}

public function getUsers(){
    $query = "SELECT `id` as UID, `name` as UName , `img` , `room` , `ext` FROM users , rooms  WHERE is_admin =0 and room_num = room";
    $users = array();
    $stmt = $this->pdo->prepare($query);
    $stmt->execute();
    $user = $stmt->fetchAll();
    foreach ($user as $row) {
        $users[$row['UID']]['UName']=$row['UName'];
        $users[$row['UID']]['img']=$row['img'];
        $users[$row['UID']]['room']=$row['room'];
        $users[$row['UID']]['ext']=$row['ext'];
    }
    return $users;
    }
  
  public function login($email , $password){
    $query =  $this->pdo->query( "SELECT `name` , `id` from users where email = '$email' and password = '$password' "); 
    return $query ; 
  }  

  public function updateUser($name , $img , $room , $uid){
    $query = "UPDATE users set `name` = $name , `img` = $img , `room` = $room  WHERE `id` = $uid ";
    // $users = array();
    $stmt = $this->pdo->prepare($query);
    $stmt->execute();
  }

  public function deleteUser($uid){
    $query = "DELETE FROM users WHERE `id` = $uid ";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute();
  }

}


