<?php
/**
 * Главный конфиг Kolendula.
 */
return array (
  'db_driver' => 'mysql',
  'sqlite' => 
  array (
    'path' => 'D:\\OSPanel\\home\\kolendula/data/kolendula.sqlite',
  ),
  'mysql' => 
  array (
    'host' => 'MySQL-8.4',
    'port' => 3306,
    'database' => 'kolendula',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
  ),
  'demo_password_reset' => true,
);
