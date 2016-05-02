<?php
require_once "core/environment_init.php";

$array = [
  "name" => "Alessio",
  "surname" => "Debernardi",
  "email" => "alessio.debernardi@netatlas.it",
];

$user1 = new ExampleConcreteClass();
$user1->fill($array);
$user1->save();

$user2 = new ExampleConcreteClass();
$user2->load(1);
$user2->name = "Pippo";
$user2->save();

$user3 = new ExampleConcreteClass();
$user3->fill($array);
$user3->save();
$user3->delete();

echo "it's all ok!";
