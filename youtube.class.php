<?php

date_default_timezone_set("Asia/Riyadh");

class Youtube {

// Variables
private $url_api = "http://youtube.com/get_video_info?video_id=";
private $url_ajax = "https://loader.to/ajax/download.php?start=1&end=20&format=mp&url=https://www.youtube.com/watch?v=nm0Ay13tEh4";
private $url_progress_id = "https://loader.to/ajax/progress.php?id=";
private $video_data;
private $video_formats;
private $last_video_check;
private $last_videoLabel_check;
private $video_list = array();
private $audio_list = array();
private $links_of_video = array();

// function call youtube api

function url($Videoid)
{
parse_str(file_get_contents($this->url_api . $Videoid), $info);
$this->video_data = json_decode($info['player_response'], true);
return $this->prepareLinks($this->video_data);
}

//  prepare links of video and audio
private function prepareLinks($video_data)
{
//  call function details video
$video_details = $this->videoDetails($video_data['videoDetails']);

//  extract video from url with audio and preparing links
$this->video_formats = $this->video_data['streamingData']['adaptiveFormats'];

// get audio information
$audio_video = $this->audioVideo($this->video_formats);

//  get video information
$video_information = $this->videoInformation($this->video_formats, $audio_video);


//  info of video on array 
$this->links_of_video = array(
'time_request' => date("Y-m-d h:i:s"),
'video_details' => $video_details,
'audio_details' => $audio_video,
'video_links' => $video_information
);

// return result
return $this->links_of_video;
}

// get details video
private function videoDetails($video_details)
{
$details_arr = array();
$title = $video_details['title'];
// get thumbnail
foreach($video_details['thumbnail']['thumbnails'] as $thumbnail)
{
$details_arr = array(
'title' => $title, 
'shortDescription' => $data['shortDescription'],
'thumbnail' => $thumbnail['url']);
}
return $details_arr;
}

//  get audio video
private function audioVideo($video_formats)
{
foreach ($video_formats as $data)
{
//  extract audio
if(stripos($data['mimeType'], "audio/mp4") !== false)
{
$this->audio_list = array(
'url' => $data['url'],
'mimeType' => 'mp3',
'size' => $this->videoSize($data['url'])
);
}
}
return $this->audio_list;
}

// get video information
private function videoInformation($video_formats)
{
foreach ($video_formats as $data)
{
// check type video is mp4 and check format not unrepeated
if(stripos($data['mimeType'], "video/mp4") !== false || stripos($data['mimeType'], "video/webm") !== false)
{
if($this->last_video_check != $data['width'])
{
$vide_size = $this->videoSize($data['url']);

if($vide_size != '0 B'){
$this->video_list[] = array(
'url' => $data['url'],
'mimeType' => 'mp4',
'quality' => $data['qualityLabel'],
'size' => $vide_size
);
$this->last_video_check = $data['width'];
}
}
}
}

return $this->video_list;
}

// calculate the size of the video
private function videoSize($url){
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, TRUE);
curl_setopt($ch, CURLOPT_NOBODY, TRUE);

$data = curl_exec($ch);
$size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

curl_close($ch);

switch ($size) {
case $size < 1024:
return $size .' B'; break;
case $size < 1048576:
return round($size / 1024, 2) .' KB'; break;
case $size < 1073741824:
return  round($size / 1048576, 2) . ' MB'; break;
case $size < 1099511627776:
return round($size / 1073741824, 2) . ' GB'; break;
}

return $size .' B';
}

}
