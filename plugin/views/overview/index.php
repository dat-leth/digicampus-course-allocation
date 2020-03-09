<div id="bps_overview">
    <? foreach ($waiting as $set): ?>
        <table class="default">
            <caption><?= $set['set_name'] ?> (<?= date('d.m.Y H:i', $set['distribution_time']) ?>)</caption>
            <colgroup>
                <col width="1px">
                <col width="83%">
                <col width="9%">
                <col width="5%">
                <col width="3%">
            </colgroup>
            <thead>
            <tr>
                <th></th>
                <th><?= _('Name') ?></th>
                <th><?= _('Inhalt') ?></th>
                <th><?= _('Priorität') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <? foreach ($set['groups'] as $group): ?>
                <tr class="table_header header-row">
                    <th style="white-space: nowrap; text-align: left"></th>
                    <th style="text-align: left" colspan="2"><?= $group['group_name'] ?></th>
                    <th></th>
                    <th></th>
                </tr>
                <? foreach ($group['items'] as $item): ?>
                    <?
                    uasort($item['courses'], function ($a, $b) {
                        return strnatcmp($a['seminar_name'], $b['seminar_name']);
                    })
                    ?>
                    <? foreach ($item['courses'] as $course): ?>
                        <tr>
                            <? if (array_search($course['seminar_id'], array_keys($item['courses'])) == 0): ?>
                                <td title="<?= $group['group_name'] ?>" rowspan="<?= count($item['courses']) ?>"
                                    class="gruppe<?= array_search($group['group_id'], array_keys($set['groups'])) % 9; ?>"></td>
                            <? endif; ?>
                            <td>
                                <a href="/dispatch.php/course/details/?sem_id=<?= $course['seminar_id'] ?>&send_from_search_page=plugins.php%2Fbundleallocationplugin%2Foverview%2Findex&send_from_search=TRUE"><?= $course['seminar_name'] ?></a>
                            </td>
                            <td>
                                <a data-dialog="size=auto"
                                   href="/dispatch.php/course/details/index/<?= $course['seminar_id'] ?>">
                                    <img alt="info-circle" title="<?= _('Veranstaltungsdetails') ?>>"
                                         style="cursor: pointer"
                                         src="/assets/images/icons/grey/info-circle.svg"
                                         class="icon-role-inactive icon-shape-info-circle" width="20" height="20"> </a>
                            </td>
                            <? if (array_search($course['seminar_id'], array_keys($item['courses'])) == 0): ?>
                                <td rowspan="<?= count($item['courses']) ?>"
                                    <? if (count($item['courses']) > 1): ?>style="border-left: 3px solid #e7ebf1"<? endif; ?>>
                                    <?= $item['priority'] + 1 ?>
                                </td>
                                <td rowspan="<?= count($item['courses']) ?>" style="text-align: right">
                                    <a data-dialog="size=big"
                                       href="/plugins.php/bundleallocationplugin/enrollment/apply/<?= $course['seminar_id'] ?>">
                                        <img title="<?= _('Prioritäten anpassen') ?>"
                                             src="/assets/images/icons/grey/admin.svg"
                                             class="icon-role-inactive icon-shape-admin" width="20" height="20">
                                    </a>
                                </td>
                            <? endif; ?>
                        </tr>
                    <? endforeach; ?>
                <? endforeach; ?>
            <? endforeach; ?>
            </tbody>
        </table>
    <? endforeach; ?>
</div>