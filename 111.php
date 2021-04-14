<?php

if (isset($_POST['user_name']) && isset($_POST['idtask'])) {
	print_r($_POST['user_name'].$_POST['idtask']);
} else {
    echo "Не верно переданы данные!";
}