<?php
ob_clean();
header('Content-type: application/json');
header('Content-Disposition: attachement; filename='.$zona.'json');
header('Pragma: no-cache');
header('Expires: 0');
echo json_encode ($data);
exit(0);
