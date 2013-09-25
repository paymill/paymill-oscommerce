<?php
require_once('includes/application_top.php');
require_once (DIR_FS_CATALOG . 'ext/modules/payment/paymill/lib/Services/Paymill/Log.php');

$recordLimit = 3;
$page = $_GET['seite'];
if(!isset($_GET['seite'])) {
   $page = 1;
} 

$start = $page * $recordLimit - $recordLimit;

$sql = "SELECT * FROM `pi_paymill_logging` LIMIT $start, $recordLimit";
if (isset($_POST['submit'])) {
    $sql = "SELECT * FROM `pi_paymill_logging` WHERE debug like '%" . tep_db_input($_POST['search_key']) . "%' LIMIT $start, $recordLimit";
    
}

$logs        = tep_db_query($sql);
$recordCount = tep_db_num_rows($logs);
$pageCount = $recordCount / $recordLimit;
$logModel    = new Services_Paymill_Log();
require(DIR_WS_INCLUDES . 'template_top.php');
?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr>
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
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input value="" name="search_key"/><input type="submit" value="Search..." name="submit"/>
                        </form>
                        <table>
                            <tr class="dataTableHeadingRow">
                                <th class="dataTableHeadingContent">ID</th>
                                <th class="dataTableHeadingContent">Debug</th>
                                <th class="dataTableHeadingContent">Date</th>
                            </tr>
                            <?php while ($log = tep_db_fetch_array($logs)): ?>
                            <tr class="dataTableRow">
                                <td class="dataTableContent"><?php echo $log['id']; ?></td>
                                <td class="dataTableContent">
                                    <?php $logModel->fill($log['debug']) ?>
                                    <table>
                                        <tr class="dataTableHeadingRow">
                                            <?php foreach ($logModel->toArray() as $key => $value): ?>
                                                <th class="dataTableHeadingContent"><?php echo strtoupper(str_replace('_', ' ', $key)); ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                        <tr class="dataTableRow">
                                            <?php foreach ($logModel->toArray() as $key => $value): ?>
                                            <td class="dataTableContent">
                                                <?php if (strlen($value['debug']) > 300): ?>
                                                    <center><a href="<?php echo tep_href_link('paymill_log.php', 'id=' . $log['id'] . '&key=' . $key, 'SSL', true, false); ?>">See more</a></center>
                                                <?php else: ?>
                                                    <pre><?php echo $value['message']; ?><hr/><?php echo $value['debug']; ?></pre>
                                                <?php endif; ?>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    </table>
                                </td>
                                <td class="dataTableContent"><?php echo $log['date']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input value="" name="search_key"/><input type="submit" value="Search..." name="submit"/>
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
<?php
require(DIR_WS_INCLUDES . 'template_bottom.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
