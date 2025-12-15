<?php
// check_config.php
echo "<h1>PHP Configuration Check</h1>";
echo "<strong>Loaded Configuration File:</strong> " . php_ini_loaded_file() . "<br>";
echo "<strong>SMTP:</strong> " . ini_get('SMTP') . "<br>";
echo "<strong>smtp_port:</strong> " . ini_get('smtp_port') . "<br>";
echo "<strong>sendmail_path:</strong> " . htmlspecialchars(ini_get('sendmail_path')) . "<br>";
echo "<strong>sendmail_from:</strong> " . ini_get('sendmail_from') . "<br>";

echo "<hr>";
echo "<h3>Diagnosis:</h3>";
if (ini_get('SMTP') == 'localhost') {
    echo "<span style='color:red'>ERROR: PHP is still using the default 'localhost'. Your changes have NOT been loaded.</span><br>";
    echo "Please RESTART Apache again.";
} elseif (strpos(ini_get('sendmail_path'), 'sendmail.exe') === false) {
    echo "<span style='color:red'>ERROR: sendmail_path is not set correctly. PHP is trying to send mail directly instead of using XAMPP's mailer.</span>";
} else {
    echo "<span style='color:green'>SUCCESS: Configuration is loaded correctly!</span>";
}
?>
