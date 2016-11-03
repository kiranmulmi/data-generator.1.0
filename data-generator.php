<?php
session_start();
include_once "db-connection.php";

$decoded_data = json_decode($_GET["json"]);
$no_of_rows = $_GET["no_of_rows"];
$table = $_GET["table"];
$preview_or_generate = $_GET["preview_or_generate"];
$database = $_GET["database"];
$batch_size = $_GET["batch_size"];

$fakecompany = file('Dictionary/company');
$fakename = file('Dictionary/name');
$fakestreet = file('Dictionary/street');
$fake_long_text = file('Dictionary/long-text');
$fake_short_text = file('Dictionary/short-text');
$dummy_city = file('Dictionary/city');

$auto_inc = 1;
$connection = get_connection($database);
/*sql generate */
if ($preview_or_generate == "generate") {
    $status = "success";

    $sql = "INSERT INTO `$table` ( ";
    $coma = "";
    foreach ($decoded_data as $item) {
        if ($item->hidden_column_key != "PRI") {
            $sql .= $coma . "`" . $item->column . "`";
            $coma = ",";
        }
    }
    $sql .= ") VALUES ";
    for ($i = 0; $i < $no_of_rows; $i++) {
        $cc = "";
        $sub_sql = "";
        for ($j = 0; $j < $batch_size; $j++) {
            $coma = "";
            $sub_sql .= $cc." ( ";
            foreach ($decoded_data as $item) {
                if ($item->hidden_column_key != "PRI") {
                    $sub_sql .= $coma . '"' . randomize($item->selected_data_type, $item->data_type, $item->options) . '"';
                    $coma = ",";
                    $cc = ",";
                }
            }
            $sub_sql .= ")";

        }
        $exec = $sql.$sub_sql;
        //echo "<pre>".$exec."</pre>";
        $status = $connection->query($exec);
        if($status) {
            echo "success";
        } else {
            echo "error";
        }

    }

} elseif ($preview_or_generate == "preview") {
    $return_data = array();
    for ($i = 0; $i < 10; $i++) {
        $row_data = array();
        foreach ($decoded_data as $item) {
            $row_data[] = randomize($item->selected_data_type, $item->data_type, $item->options);
        }
        $return_data[] = $row_data;
    }
    echo json_encode($return_data);
}

//var_dump($fakename);


function randomize($selected_data_type, $data_type, $options)
{
    $O_to_9 = "0123456789";
    $a_to_z = "abcdefghijklmnopqrstuvwxyz";

    $domain = array("com", "net", "gov", "org", "edu", "biz", "info");

    $return_text = "";
    if ($selected_data_type == "name") {
        $options = explode("|", $options);
        global $fakename;
        $count = count($fakename);
        $key1 = rand(0, $count - 1);
        $key2 = rand(0, $count - 1);
        $first_name = explode(",", $fakename[$key1]);
        $last_name = explode(",", $fakename[$key2]);
        if (isset($options[0]) && !isset($options[1])) {
            $return_text = $first_name[0];
            if ($options[0] == "l" || $options[0] == "m") $return_text = $last_name[0];
        } elseif (isset($options[0]) && isset($options[1]) && !isset($options[2])) {
            $return_text .= $first_name[0] . " " . $last_name[1];
        } elseif (isset($options[0]) && isset($options[1]) && isset($options[2])) {
            $return_text .= $first_name[0] . " " . $last_name[0] . " " . $last_name[1];
        }
    } elseif ($selected_data_type == "phone") {
        $options = explode("|", $options);
        $prefixArray = explode(",", $options[0]);
        $key = rand(0, count($prefixArray) - 1);
        $digit = $options[1];
        $num = $prefixArray[$key] . substr(str_shuffle($O_to_9 . $O_to_9), 0, $digit - strlen($prefixArray[$key]));
        $return_text = $num;
    } elseif ($selected_data_type == "email") {
        global $fakename;
        $name_key = rand(0, count($fakename)-1);
        $name = explode(",",$fakename[$name_key]);
        $return_text = strtolower($name[0]).substr(str_shuffle($O_to_9), 0, rand(4, 6))."@".substr(str_shuffle($a_to_z),0, rand(4,6)).".".$domain[rand(0,count($domain)-1)];
        /*$key = rand(0, count($domain) - 1);
        $email = substr(str_shuffle($a_to_z), 0, rand(4, 10)) . substr(str_shuffle($O_to_9), 0, rand(4, 6)) . "@" . substr(str_shuffle($a_to_z), 0, rand(4, 10)) . "." . $domain[$key];
        return $email;*/
    } elseif ($selected_data_type == "date") {
        $dateArr = explode("|", $options);
        $min = strtotime($dateArr[0]);
        $max = strtotime($dateArr[1]);
        $val = rand($min, $max);
        $return_text = date('Y-m-d H:i:s', $val);;
    } elseif ($selected_data_type == "street") {
        global $fakestreet;
        $key = rand(0, count($fakestreet) - 1);
        $return_text = $fakestreet[$key];
    } elseif ($selected_data_type == "username") {
        $return_text = substr(str_shuffle($a_to_z), 0, rand(4, 10)) . substr(str_shuffle($O_to_9), 0, rand(4, 6));
    } elseif ($selected_data_type == "password") {
        $password = "admin";
        $encryption_salt = "8f5d0eae5947135741cd0aef3teg6eb2";
        $encrypted = hash("sha256", $encryption_salt . $password);
        $return_text = $encrypted;
    } elseif ($selected_data_type == "short_text") {
        global $fake_short_text;
        $key = rand(0, count($fake_short_text) - 1);
        $return_text = $fake_short_text[$key];
    } elseif ($selected_data_type == "long_text") {
        global $fake_long_text;
        $key = rand(0, count($fake_long_text) - 1);
        $return_text = $fake_long_text[$key];
    } elseif ($selected_data_type == "number") {
        $numArr = explode("|", $options);
        $min = $numArr[0];
        $max = $numArr[1];
        $return_text = rand($min, $max);
    } elseif ($selected_data_type == "custom_list") {
        $list = explode("|", $options);
        $key = rand(0, count($list) - 1);
        $return_text = $list[$key];
    } elseif ($selected_data_type == "auto_increment") {
        global $auto_inc;
        $return_text = $auto_inc++;
    } elseif ($selected_data_type == "city" || $selected_data_type == "region") {
        global $dummy_city;
        $key = rand(0, count($dummy_city) - 1);
        $return_text = $dummy_city[$key];
    } elseif ($selected_data_type == "postal") {
        $return_text = rand(100, 1000);
    } elseif ($selected_data_type == "country") {
        global $dummy_country;
        $key = rand(0, count($dummy_country) - 1);
        $return_text = $dummy_country[$key];
    } elseif ($selected_data_type == "lat") {
        $radius = 100;
        $angle = deg2rad(mt_rand(0, 359));
        $pointRadius = mt_rand(0, $radius);
        $return_text = sin($angle) * $pointRadius;
    } elseif ($selected_data_type == "lng") {
        $radius = 100;
        $angle = deg2rad(mt_rand(0, 359));
        $pointRadius = mt_rand(0, $radius);
        $return_text = cos($angle) * $pointRadius;
    } elseif ($selected_data_type == "url") {
        $key = rand(0, count($domain) - 1);
        $url = "http://www." . substr(str_shuffle($a_to_z), 0, rand(3, 5)) . "." . $domain[$key];
        $return_text = $url;
    }

    return $return_text;
}

