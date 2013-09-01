<?php
    require_once('includes/application_top.php');
    $logs = tep_db_query("SELECT * FROM `pi_paymill_logging`");
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
                        <table>
                            <tr class="dataTableHeadingRow">
                                <th class="dataTableHeadingContent">ID</th>
                                <th class="dataTableHeadingContent">Debug</th>
                                <th class="dataTableHeadingContent">Message</th>
                                <th class="dataTableHeadingContent">Date</th>
                            </tr>
                            <?php while ($log = tep_db_fetch_array($logs)): ?>
                                <tr class="dataTableRow">
                                    <td class="dataTableContent"><?php echo $log['id']; ?></td>
                                    <td class="dataTableContent"><?php echo $log['debug']; ?></td>
                                    <td class="dataTableContent"><?php echo $log['message']; ?></td>
                                    <td class="dataTableContent"><?php echo $log['date']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
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
