<?php

try {
    $data_generator_con = new PDO('sqlite:data.sqlite');
} catch (PDOException $e) {
    print 'Exception : ' . $e->getMessage();
}