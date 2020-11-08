<?php


$names = ['a','b','c'];

print_r(json_encode($names, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));