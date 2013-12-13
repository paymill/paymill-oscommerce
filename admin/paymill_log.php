<?php
require_once ('includes/application_top.php');

$sql = "SELECT * FROM `pi_paymill_logging` WHERE id = '" . tep_db_input($_GET['id']) . "'";
$logs = tep_db_fetch_array(tep_db_query($sql));
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
        <title><?php echo TITLE; ?></title>
        <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
        <script language="javascript" src="includes/general.js"></script>
    </head>
    <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
        <!-- header //-->
        <?php
        require(DIR_WS_INCLUDES . 'header.php');
        ?>
        <table border="0" width="100%" cellspacing="2" cellpadding="2">
            <tr>
                <td width="<?php echo BOX_WIDTH; ?>" valign="top">
                    <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
                        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
                    </table>
                </td>
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
        <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
        <br>
    </body>
</html>
<?php
require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
