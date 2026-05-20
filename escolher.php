<?php
include 'config.php';

$id = intval($_POST['id']);

$conn->query("UPDATE presentes SET escolhido = 1 WHERE id = $id");