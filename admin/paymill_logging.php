<?php
require_once('includes/application_top.php');

$recordLimit = 10;
$page = $_GET['seite'];
if (!isset($_GET['seite'])) {
    $page = 1;
}

$start = $page * $recordLimit - $recordLimit;

$sql = "SELECT * FROM `pi_paymill_logging` LIMIT $start, $recordLimit";

if (isset($_POST['reset_filter'])) {
    unset($_SESSION['connected']);
    unset($_SESSION['search_key']);
}

if (isset($_POST['submit']) || isset($_SESSION['search_key'])) {
    if (!isset($_SESSION['search_key'])) {
        $_SESSION['search_key'] = true;
    }

    isset($_POST['submit']) ? $searchKey = $_POST['search_key'] : $searchKey = $_SESSION['search_key'];
    if (array_key_exists('connected', $_POST) || array_key_exists('connected', $_SESSION)) {
        $_SESSION['connected'] = true;
        $sql = "SELECT identifier FROM `pi_paymill_logging` WHERE debug like '%" . tep_db_input($searchKey) . "%' LIMIT $start, $recordLimit";
        $identifier = tep_db_fetch_array(tep_db_query($sql));
        $sql = "SELECT * FROM `pi_paymill_logging` WHERE identifier = '" . tep_db_input($identifier['identifier']) . "' LIMIT $start, $recordLimit";
    } else {
        $sql = "SELECT * FROM `pi_paymill_logging` WHERE debug like '%" . tep_db_input($searchKey) . "%' LIMIT $start, $recordLimit";
    }
}

$data = tep_db_query($sql);

$recordCount = tep_db_num_rows($data);
$pageCount = $recordCount / $recordLimit;
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
                            <td width="100%">
                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="pageHeading">PAYMILL Log</td>
                                        <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <b>Page: </b>
                                    <?php for ($a = 0; $a <= $pageCount; $a++) : ?>
                                        <?php $b = $a + 1; ?>
                                        <?php if ($page == $b) : ?>
                                            <b><?php echo $b; ?></b>
                                        <?php else : ?>
                                            <a href="<?php echo tep_href_link('paymill_logging.php'); ?>?seite=<?php echo $b; ?>"><?php echo $b; ?></a>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <form action="<?php echo tep_href_link('paymill_logging.php'); ?>" method="POST">
                                    <input value="" name="search_key"/><input type="submit" value="Search..." name="submit"/>
                                    <input type="checkbox" name="connected" value="true">&nbsp;Connected Search
                                </form>
                                <form action="<?php echo tep_href_link('paymill_logging.php'); ?>" method="POST">
                                    <input type="submit" value="Reset Filter..." name="reset_filter"/>
                                </form>
                                <table width="100%">
                                    <tr class="dataTableHeadingRow">
                                        <th class="dataTableHeadingContent">ID</th>
                                        <th class="dataTableHeadingContent">Connector ID</th>
                                        <th class="dataTableHeadingContent">Message</th>
                                        <th class="dataTableHeadingContent">Debug</th>
                                        <th class="dataTableHeadingContent">Date</th>
                                    </tr>

                                    <?php while ($log = tep_db_fetch_array($data)): ?>
                                        <tr class="dataTableRow">
                                            <td class="dataTableContent"><center><?php echo $log['identifier']; ?></center></td>
                                            <td class="dataTableContent"><center><?php echo $log['id']; ?></center></td>
                                            <td class="dataTableContent"><?php echo $log['message']; ?></td>
                                            <td class="dataTableContent">
                                                <?php if (strlen($log['debug']) < 500): ?>
                                                    <pre><?php echo $log['debug']; ?></pre>
                                                <?php else: ?>
                                                    <center>
                                                        <a href="<?php echo tep_href_link('paymill_log.php', 'id=' . $log['id'], 'SSL', true, false); ?>">See more</a>
                                                    </center>
                                                <?php endif; ?>
                                            </td>
                                            <td class="dataTableContent">
                                                <center><?php echo $log['date']; ?></center>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </table>
                                <form action="<?php echo tep_href_link('paymill_logging.php'); ?>" method="POST">
                                    <input name="search_key"/><input type="submit" value="Search..." name="submit"/>
                                    <input type="checkbox" name="connected" value="true">&nbsp;Connected Search
                                </form>
                                <form action="<?php echo tep_href_link('paymill_logging.php'); ?>" method="POST">
                                    <input type="submit" value="Reset Filter..." name="reset_filter"/>
                                </form>
                                <div>
                                    <b>Page: </b>
                                    <?php for ($a = 0; $a <= $pageCount; $a++) : ?>
                                        <?php $b = $a + 1; ?>
                                        <?php if ($page == $b) : ?>
                                            <b><?php echo $b; ?></b>
                                        <?php else : ?>
                                            <a href="<?php echo tep_href_link('paymill_logging.php'); ?>?seite=<?php echo $b; ?>"><?php echo $b; ?></a>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
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
