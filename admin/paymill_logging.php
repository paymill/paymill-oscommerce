<?php
require_once('includes/application_top.php');
require_once (DIR_FS_CATALOG . 'ext/modules/payment/paymill/lib/Services/Paymill/Log.php');

$recordLimit = 10;

if (isset($_GET['page'])) {
    $page = $_GET['page'] + 1;
    $offset = $recordLimit * $page;
} else {
    $page = 0;
    $offset = 0;
}

$sql = "SELECT * FROM `pi_paymill_logging`";
if (isset($_POST['submit'])) {
    $sql = "SELECT * FROM `pi_paymill_logging` WHERE debug like '%" . xtc_db_input($_POST['search_key']) . "%' LIMIT $offset, $recordLimit";
}

$logs = xtc_db_query($sql);
$recordCount = xtc_db_num_rows($logs);
$leftRecords = $recordCount - ($page * $recordLimit);
$logModel = new Services_Paymill_Log();
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
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input value="" name="search_key"/><input type="submit" value="Search..." name="submit"/>
                        </form>
                        <?php if( $page > 0 ) : ?>
                           <?php $last = $page - 2; ?>
                           <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $last; ?>">Last 10 Records</a> |
                           <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $page; ?>">Next 10 Records</a>
                        <?php elseif( $page == 0 ): ?>
                           <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $page; ?>">Next 10 Records</a>
                        <?php elseif( $leftRecords < $recordLimit ): ?>
                           <?php $last = $page - 2; ?>
                           <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $last; ?>">Last 10 Records</a>
                        <?php endif; ?>
                        <table>
                            <tr class="dataTableHeadingRow">
                                <th class="dataTableHeadingContent">ID</th>
                                <th class="dataTableHeadingContent">Debug</th>
                                <th class="dataTableHeadingContent">Date</th>
                            </tr>
                            <?php while ($log = xtc_db_fetch_array($logs)): ?>
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
                                                <?php if (strlen($value) > 300): ?>
                                                    <a href="<?php echo xtc_href_link('paymill_log.php', 'id=' . $log['id'] . '&key=' . $key, 'SSL', true, false); ?>">See more</a>
                                                <?php else: ?>
                                                    <pre><?php echo $value; ?></pre>
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
                        <?php if( $page > 0 ) : ?>
                           <?php $last = $page - 2; ?>
                           <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $last; ?>">Last 10 Records</a> |
                           <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $page; ?>">Next 10 Records</a>
                        <?php elseif( $page == 0 ): ?>
                           <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $page; ?>">Next 10 Records</a>
                        <?php elseif( $leftRecords < $recordLimit ): ?>
                           <?php $last = $page - 2; ?>
                           <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $last; ?>">Last 10 Records</a>
                        <?php endif; ?>
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
