<strong>Präferenzbasierte überschneidungsfreie Anmeldung</strong>
<br>
<? if ($rule->getDistributionTime()) : ?>
    <? if ($rule->getDistributionTime() > time()) : ?>
        <?= sprintf(_('Die Plätze in den betreffenden Veranstaltungen werden am %s ' .
            'um %s verteilt.'), date("d.m.Y", $rule->getDistributionTime()),
            date("H:i", $rule->getDistributionTime())) ?>
    <? else : ?>
        <?= sprintf(_('Die Plätze in den betreffenden Veranstaltungen wurden am %s ' .
            'um %s verteilt. Weitere Plätze werden evtl. über Wartelisten zur Verfügung gestellt.'), date("d.m.Y", $rule->getDistributionTime()),
            date("H:i", $rule->getDistributionTime())) ?>
    <? endif ?>
<? endif ?>
<br>
Es wird überschneidungsfrei jeweils eine (1) Veranstaltung pro Zuteilungsgruppe zugeteilt, wenn
Prioritäten dafür abgegeben wurden. Überschneidungen zu Veranstaltungen außerhalb dieses Anmeldesets werden
<strong>NICHT</strong> berücksichtigt!
<? if ($rankinggroups = $rule->getRankingGroups()) : ?>
    <ul>
        <? foreach ($rankinggroups as $group): ?>
            <li><?= $group->getName() ?> (Mindestanzahl abzugebener Prioritäten: <?= $group->getMinAmountPrios() ?>)</li>
        <? endforeach; ?>
    </ul>
<? endif ?>

<? if (match_route('dispatch.php/admission/courseset/configure')
    && ($GLOBALS['perm']->have_perm('admin') || ($GLOBALS['perm']->have_perm('dozent') && get_config('ALLOW_DOZENT_COURSESET_ADMIN')))
): ?>
    <a data-dialog=auto class="button" href="<?= PluginEngine::getLink('BundleAllocationPlugin', [], 'admission/applications/' . $coursesetId) ?>">Anmeldungen verwalten</a>
<? endif ?>
