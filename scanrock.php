<?php

/**
 * @package Marchenko
 * @version 1.0
 */

/*
 * Plugin Name: scanroc
 * Plugin URI: http://vreklame.com
 * Description:  Добавления категорий и картинок  CMS wordpress
 * Version: 1.0
 * Author: Ruslan Marchenko
 * Author URI: http://vreklame.com
 */


function add_admin_page()
{

    // Add a new submenu under Option
    add_options_page('(ПРОДУКЦИЯ-SCANROC', 'Admin-SCANROC', 8, 'scanroc', 'marchenko_options_page');


}

function marchenko_options_page()
{
    echo "<h1> Настройка категорий и добавление товаров</h1>";


    echo "<h3>Название категории</h3>";
    marchenko_add_category();

    echo "<br> <br> <br> <br>";

    echo "<h3>Добавление товара</h3>";
    marchenko_add_item();


}

//Save category

function marchenko_add_category()
{

    global $wpdb;
    $table_category = $wpdb->prefix . table_category;

    if (isset($_POST["category_btn"])) {

        $marchenko_category_name = $_POST["category_name"];

        $wpdb->insert(
            $table_category,
            array("name" => $marchenko_category_name)

        );
    }

    echo "
    <form name='marchenko_add_category' method='post' action='" . $_SERVER['PHP_SELF'] . "?page=scanroc&amp;update=true'>";

    if (function_exists("wp_nonce_fieled")) {
        wp_nonce_fieled("marchenko_add_category");
    }

    echo "
      <table>
          <tr>
            <td>Название:</td>
            <td><input type='text' name='category_name' style='width: 300px;' /> </td>
            <td></td>
          </tr>
          <tr>
           <td><input type='submit' name='category_btn' value='Submit' style='width: 140px; height: 25px;'/></td> 
         </tr>
     </table>
     </form>
    ";

}


//Save item
function marchenko_add_item()
{
    global $wpdb;

    $results = $wpdb->get_results('SELECT * FROM wp_table_category');

    $table_item = $wpdb->prefix . table_item;

    if (isset($_POST['item_btn'])) {
        $category_item = $_POST['input_id'];
        $add_icon_img = $_FILES['image_icon'] ['tmp_name'];

        if (!isset($add_icon_img))
            echo 'Pleace Select img';
        else {
            $image = addslashes_gpc(file_get_contents($_FILES['image_icon'] ['tmp_name']));
            $image_size = getimagesize($_FILES['image_icon'] ['tmp_name']);
            if ($image_size == FALSE)
                echo 'Thats not an image';
        }

        $add_img = $_FILES['img_all'] ['tmp_name'];
        if (!isset($add_img))
            echo 'Pleace Select img';
        else {
            $image2 = addslashes_gpc(file_get_contents($_FILES['img_all'] ['tmp_name']));
            $image_size2 = getimagesize($_FILES['img_all'] ['tmp_name']);
            if ($image_size2 == FALSE)
                echo 'Thats not an image';
        }

        $wpdb->insert(
            $table_item,
            array("id_category" => $category_item, "img_all" => $image2, "img_icon" => $image)

        );
    }


    echo "
    <form name='marchenko_add_item' enctype='multipart/form-data' method='post' action='" . $_SERVER['PHP_SELF'] . "?page=scanroc&amp;update=true' >";

    if (function_exists("wp_nonce_fieled")) {
        wp_nonce_fieled("marchenko_add_category");
    }

    foreach ($results as &$value) {
        echo "
        <br><lable>$value->name </lable>
        <input type='checkbox' name='input_id' value='$value->id'>";
    }

    echo "


<table>
  <tr>
          <td>
            <input type='file' name='image_icon'>
          </td>
          </tr>
           <tr>
             <td>
            <input type='file' name='img_all'>
          </td>
          </tr>
          <tr>
           <td><input type='submit' name='item_btn' value='Submit' style='width: 140px; height: 25px;'/></td>
         </tr>
     </table>   
     </form>
    ";
}


function my_run()
{
    $status_url = get_option("my_status_url");
    preg_match('/^http(s)?\:\/\/[^\/]+\/(.*)$/i', $status_url, $matches);


    $real_url = $_SERVER["REQUEST_URI"];
    preg_match('/^\/(.+)(\?.+)$/i', $real_url, $uri_matches);


    if ($uri_matches[1] == $matches[2]) {

        if (isset($_GET["dcode"])) {
            start_download();
        } else {
            interkassa_process();
        }

    }

}

add_action("admin_menu", "add_admin_page");
//add_action('init', 'my_run');



