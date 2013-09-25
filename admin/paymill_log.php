<?php
require_once ('includes/application_top.php');

$sql = "SELECT * FROM `pi_paymill_logging` WHERE id = '" . tep_db_input($_GET['id']) . "'";
$logs = tep_db_fetch_array(tep_db_query($sql));

require(DIR_WS_INCLUDES . 'template_top.php'); 
?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr>
        <td width="100%" valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="2" height="40">
                            <tr>
                                <td class="pageHeading">PAYMILL Log Entry</td>
                            </tr>
                            <tr>
                                <td><img width="100%" height="1" border="0" alt="" src="images/pixel_black.gif"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <pre><?php echo $logs['message']; ?><hr/><?php echo $logs['debug']; ?></pre>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php
require(DIR_WS_INCLUDES . 'template_bottom.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
