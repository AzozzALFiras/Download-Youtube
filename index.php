<?php



include 'youtube.class.php';

$Videoid = $_GET['Videoid'];
$youtube = new Youtube();
$url = $youtube->url($Videoid);

echo json_encode($url);
