<?php
ob_clean();
header('Content-type: text/plain');
header('Content-Disposition: attachement; filename='.$zona.'conf');
header('Pragma: no-cache');
header('Expires: 0');
$soa = explode(' ', $data['soa']['rdata']);
?>
;
; Configuration file for zone <?=$data['zone']['name'],"\n"?>
; @author Bind10 module for SowerPHP (http://sowerphp.org)
; @version <?=date(\sowerphp\core\Configure::read('time.format')),"\n"?>
;

; Time to live for the zone
$TTL <?=$data['soa']['ttl'],"\n"?>

; Authority for the zone
<?=$data['zone']['name']?> IN SOA <?=$soa[0]?> <?=$soa[1]?> (
    <?=$soa[2]?> ; Serial
    <?=$soa[3]?> ; Refresh
    <?=$soa[4]?> ; Retry
    <?=$soa[5]?> ; Expire
    <?=$soa[6]?> ; TTL
)

; Records
<?php
foreach ($data['records'] as &$register) {
    echo $register['name'],' ',$data['zone']['rdclass'],' ',$register['rdtype'],' ',$register['rdata'],"\n";
}

exit(0);
