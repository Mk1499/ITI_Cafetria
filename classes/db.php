<?php
class dbManger
{

    private $host = 'sql2.freemysqlhosting.net';
    private $db = 'sql2282123';
    private $user = 'sql2282123';
    private $pass = 'gR5%qP3%';
    private $charset = 'utf8mb4';
    private $dsn = "";
    private $pdo;

    // private $host = 'localhost';
    // private $db = 'cafetria';
    // private $user = 'Shawkat';
    // private $pass = 'root';
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
            $this->dsn = "mysql:host=$this->host;port=3306;dbname=$this->db";
            $this->pdo = new PDO($this->dsn, $this->user, $this->pass,$this->options);
            //echo "Success"; Khaled
        } catch (PDOException $e) {
             var_dump($this->pdo);
        }
    }

    public function checks($start,$end)
    {
        $dateCondition='';
        if(isset($start)&&!empty($start)&&isset($end)&&!empty($end)){
            $dateCondition=" and ( o.time BETWEEN CAST( '$start' AS DATETIME ) and CAST( '$end' AS DATETIME ) ) ";
        }
        $stmt = $this->pdo->prepare('SELECT u.id as UId ,u.name as UName,o.o_id As ONum , o.time as OTime , o.total as OTotal, po.price as PPrice , p.name as PName ,  po.number as PCount,p.img,p.p_id as PId 
            FROM orders o,users u,products p,products_orders po WHERE
            o.o_id = po.order_id and p.p_id = po.product_id and u.id = o.user_id '.$dateCondition);
        $users = array();
        $stmt->execute();
        $user = $stmt->fetchAll();
        foreach ($user as $row) {
            if (!isSet($users[$row['UId']]['Orders'][$row['ONum']]['Products']))
                $users[$row['UId']]['Orders'][$row['ONum']]['Products'] = array();
            $users[$row['UId']]['UName'] = $row['UName'];
            $users[$row['UId']]['Orders'][$row['ONum']]['OTime'] = $row['OTime'];
            $users[$row['UId']]['Orders'][$row['ONum']]['OTotal'] = $row['OTotal'];
            array_push($users[$row['UId']]['Orders'][$row['ONum']]['Products'], (array($row['PName'], $row['PCount'], $row['PPrice'])));
        }
        // print_r($users);
        return $users;
    }

    //SELECT o.o_id o.time, o.status, o.total from orders o where o.user_id = 3 Date between 2011/02/25 and 2011/02/27;
    public function userOrders($userId,$start,$end,$page)
    {
        $offset = $page >= 0 ? $page*4 : 0;

        $stmt = $this->pdo->prepare("SELECT o.o_id As oNum , o.time as OTime , o.total as total ,
                                              o.status as status, po.price as PPrice , p.name as PName ,
                                              po.number as PCount,p.img as img FROM orders o,products p,
                                              products_orders po WHERE o.o_id = po.order_id 
                                              and p.p_id = po.product_id and o.user_id = $userId
                                              and o.time between ? AND ? LIMIT 0 , 4 ");
        $orders = array();
        $stmt->execute(array($start,$end));
        $order = $stmt->fetchAll();
        foreach ($order as $row) {
            if (!isSet($orders[$row['oNum']]["Products"])) {
                $orders[$row['oNum']]['oNum'] = $row['oNum'];
                $orders[$row['oNum']]['time'] = $row['OTime'];
                $orders[$row['oNum']]['status'] = $row['status'];
                $orders[$row['oNum']]["Products"] = array();
                $orders[$row['oNum']]['total'] = $row['total'];
                array_push($orders[$row['oNum']]['Products'], (array("product" =>$row['PName'], "count" => $row['PCount'],
                    "price"=>$row['PPrice'])));
            }
            // print_r($users);
        }
        return $orders;

    }


    // Return All Product Function  Khaled

    public function allProduct (){
        
        $q  = $this->pdo->query( 'SELECT * FROM `products` ') ; 
       return $q ; 
    }


    // Return Latest Product Function  Khaled

    public function latestProduct (){
        $q  = $this->pdo->query( 'SELECT * FROM `products` LIMIT 1,3') ; 
       return $q ; 
    }
}

