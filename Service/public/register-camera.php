<?php

require_once("../bootstrap.php");

$requestBody = file_get_contents('php://input');

$requestBody = json_decode($requestBody);

$configDir = "/app/conf/";

$configPath = $configDir . $requestBody->cameraName . ".yml";

$config = [];
foreach($requestBody as $k => $v){
    $config[$k] = $v;
}
$config = \Symfony\Component\Yaml\Yaml::dump($config);

echo "Writing to {$configPath}\n";

if(!file_exists(dirname($configPath))){
    mkdir(dirname($configPath), 0777, true);
}

$bytes = file_put_contents($configPath, $config);

if($bytes){
    echo "Wrote {$bytes} to {$configPath}\n";
}else{
    echo "Failed to write to {$configPath} :c\n";
}

$configs = [];
foreach(scandir($configDir) as $item){
    switch($item){
        case substr($item,-4,4) == ".yml":
            $configs[] = $configDir . $item;
            break;
        default:
    }
}

$maxClients = isset($environment['SERVER_MAX_CLIENTS']) ? $environment['SERVER_MAX_CLIENTS'] : "30";
$maxBandwidth = isset($environment['SERVER_MAX_BANDWIDTH']) ? $environment['SERVER_MAX_BANDWIDTH'] : "60000";
$videoBitRate = isset($environment['VIDEO_BIT_RATE']) ? $environment['VIDEO_BIT_RATE'] : "3000";

$ffserverConfig = "
HTTPPort 8080
HTTPBindAddress 0.0.0.0
MaxHTTPConnections 2000
MaxClients {$maxClients}
MaxBandwidth {$maxBandwidth}
CustomLog -
NoDefaults

<Stream index.html>
  Format status

  # Only allow local people to get the status
  ACL allow localhost
  ACL allow 10.0.0.0 10.0.0.255
</Stream>";

foreach($configs as $config){
    $settings = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($config));

    $audioCodec = [
        'NoAudio'
    ];

    $videoCodec = [
        "VideoCodec libvpx",
        "VideoSize 640x360",
        "VideoFrameRate 15",
        "VideoGopSize 15",
        "VideoBitRate {$videoBitRate}",

        "PreRoll 0",
        "StartSendOnKey",

        "AVOptionVideo flags +global_header",
        "AVOptionVideo cpu-used 0",
        "AVOptionVideo qmin 1",
        "AVOptionVideo qmax 31",
        "AVOptionVideo quality good",
    ];
    $audioCodec = implode("\n  ", $audioCodec);
    $videoCodec = implode("\n  ", $videoCodec);
    $ffserverConfig.= "
<Feed {$settings['cameraName']}.ffm>
  File /tmp/{$settings['cameraName']}.ffm
  FileMaxSize 20000K
  ACL allow 127.0.0.1
  ACL allow localhost
  ACL allow 10.0.0.0 10.255.255.255
</Feed>

<Stream {$settings['cameraName']}.webm>
  Feed {$settings['cameraName']}.ffm
  Format webm
  
  {$audioCodec}
  
  {$videoCodec}

  
</Stream>

<Stream {$settings['cameraName']}.jpg>
    Feed {$settings['cameraName']}.ffm
    Format jpeg
    VideoFrameRate 2
    VideoIntraOnly
    VideoSize 640x360
    NoAudio
    Strict -1
</Stream>

    ";
}

$bytesWrittenToTemp = file_put_contents("/app/conf/ffserver.conf", $ffserverConfig);

$bytesWrittenToEtc =  file_put_contents("/etc/ffserver.conf", $ffserverConfig);

if(!$bytesWrittenToTemp){
    echo "Could not write to /app/conf/ffserver.conf!\n";
}

if(!$bytesWrittenToEtc){
    echo "Could not write to /etc/ffserver.conf!\n";
}

if($bytesWrittenToTemp > 0 && $bytesWrittenToEtc > 0) {
    echo "Killing FFSERVER ... ";
    passthru("killall -9 ffserver");
    echo "DONE.\n\n";
}else{
    echo "Failed to write config to Register camera\n";
}
