<?php
require_once ('functions.php');
require_once('data.php');

// Подключаемся к MySQL

$con = mysqli_connect('localhost', 'root', '', 'yeticave');
mysqli_set_charset($con, "utf8");

// Проверяем успешность соединения

if ($con == false) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
    print($content);
    die;
}

// Пишем запрос

$sql = 'SELECT * FROM categories';

// Запускаем проверку выполнения запроса

$result = checkQuery($con, $sql);

// Записываем в переменную полученные из базы данные и передаем в шаблон

$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql = 'SELECT DISTINCT l.id, l.name AS title, price_start, picture AS image_url, c.name AS category, dt_end, MAX(IF(rate_sum IS NULL, price_start, rate_sum)) AS price, COUNT(lot_id) AS rates_number
  FROM lots l
  JOIN categories c ON l.cat_id = c.id
  LEFT JOIN rates r ON l.id = r.lot_id
  WHERE dt_end > CURRENT_TIMESTAMP and winner_id IS NULL
  GROUP BY l.id, l.name, price_start, picture, c.name
  ORDER BY l.id DESC;';

$result = checkQuery($con, $sql);

$lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Подключаем шаблоны

$page_content = include_template('index.php', [
    'categories' => $categories,
    'lots' => $lots
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'site_name' => $site_name[0],
    'categories' => $categories ?? [],
    'user_name' => $user_name,
    'user_avatar' => $user_avatar,
    'is_auth' => $is_auth
]);
print($layout_content);
