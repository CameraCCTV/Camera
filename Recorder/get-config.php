<?php
$environment = array_merge($_ENV, $_SERVER);
ksort($environment);

require_once("vendor/autoload.php");

$service = parse_url($environment['SERVICE_HOST']);
$baseUrl = "http://{$service['host']}:{$service['port']}";
echo "Getting camera data...\n";
$client = new GuzzleHttp\Client();
$res = $client->request('GET', "{$baseUrl}/get-cameras.php");

$response = json_decode($res->getBody(), true);
foreach($response['cameras'] as $camera){
    $cameraName = $camera['cameraName'];
    $runScript = "#!/usr/bin/env bash
mkdir -p /video/{$cameraName};
while true; do
    sleep 15;
    ffmpeg \\
        -i http://service:8080/{$cameraName}.webm \\
        -loglevel info \\
        -map 0 \\
        -c:a copy \\
        -c:v copy \\
        -f segment \\
        -strftime 1 \\
        -segment_time 900 \\
        -segment_format webm \\
        /video/{$cameraName}/%Y-%m-%d_%H-%M-%S.webm
done;
    ";
    file_put_contents("/app/process.{$cameraName}.sh", $runScript);
    chmod("/app/process.{$cameraName}.sh", 0755);
    if(!file_exists("/etc/service/record-{$cameraName}")){
        mkdir("/etc/service/record-{$cameraName}", 0777);
    }
    if(copy("/app/process.{$cameraName}.sh", "/etc/service/record-{$cameraName}/run")){
        echo "Copied /app/process.{$cameraName}.sh to /etc/service/record-{$cameraName}/run successfully.\n";
    }else{
        echo "Failed to copy /app/process.{$cameraName}.sh to /etc/service/record-{$cameraName}/run\n";
    }
    chmod("/etc/service/record-{$cameraName}/run", 0755);

}
