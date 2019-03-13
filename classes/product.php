<?php

require_once 'classes/db.php';
class Product
{
    // database connection and table name
    private $db;
    // object properties
    public $id;
    public $name;
    public $price;
    public $category_id;
    public $image;
    public $timestamp;

    public function __construct()
    {
        $this->db = new DbManager();
    }

    // create product
    public function create()
    {
        // insert query
        $this->image = htmlspecialchars(strip_tags($this->image));

        return $this->db->createProduct($this->name, $this->price, $this->image, $this->category_id, $this->timestamp);
    }

    // used for paging products

    // will upload image file to server
    public function uploadPhoto()
    {
        $result_message = '';

        // now, if image is not empty, try to upload the image
        if ($this->image) {
            // sha1_file() function is used to make a unique file name
            $target_directory = 'uploads/';
            $target_file = $target_directory.$this->image;
            $file_type = pathinfo($target_file, PATHINFO_EXTENSION);

            // error message is empty
            $file_upload_error_messages = '';
            // make sure that file is a real image
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check !== false) {
                // submitted file is an image
            } else {
                $file_upload_error_messages .= '<div>Submitted file is not an image.</div>';
            }

            // make sure certain file types are allowed
            $allowed_file_types = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($file_type, $allowed_file_types)) {
                $file_upload_error_messages .= '<div>Only JPG, JPEG, PNG, GIF files are allowed.</div>';
            }

            // make sure file does not exist
            if (file_exists($target_file)) {
                $file_upload_error_messages .= '<div>Image already exists. Try to change file name.</div>';
            }

            // make sure submitted file is not too large, can't be larger than 1 MB
            if ($_FILES['image']['size'] > (1024000)) {
                $file_upload_error_messages .= '<div>Image must be less than 1 MB in size.</div>';
            }

            // make sure the 'uploads' folder exists
            // if not, create it
            if (!is_dir($target_directory)) {
                mkdir($target_directory, 0777, true);
                // if $file_upload_error_messages is still empty
                if (empty($file_upload_error_messages)) {
                    // it means there are no errors, so try to upload the file
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        // it means photo was uploaded
                    } else {
                        $result_message .= "<div class='alert alert-danger'>";
                        $result_message .= '<div>Unable to upload photo.</div>';
                        $result_message .= '<div>Update the record to upload photo.</div>';
                        $result_message .= '</div>';
                    }
                }

                // if $file_upload_error_messages is NOT empty
                else {
                    // it means there are some errors, so show them to user
                    $result_message .= "<div class='alert alert-danger'>";
                    $result_message .= "{$file_upload_error_messages}";
                    $result_message .= '<div>Update the record to upload photo.</div>';
                    $result_message .= '</div>';
                }
            }
        }

        return $result_message;
    }

    public function readOne()
    {
        $query = 'SELECT name, price, description, category_id, image
            FROM '.$this->table_name.'
            WHERE id = ?
            LIMIT 0,1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $row['name'];
        $this->price = $row['price'];
        $this->description = $row['description'];
        $this->category_id = $row['category_id'];
        $this->image = $row['image'];
    }
}
