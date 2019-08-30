<strong>Präferenzbasierte überschneidungsfreie Anmeldung</strong>
<br>
<? if ($rule->getDistributionTime()) : ?>
    <? if ($rule->getDistributionTime() > time()) : ?>
        <?= sprintf(_('Die Plätze in den betreffenden Veranstaltungen werden am %s '.
            'um %s verteilt.'), date("d.m.Y", $rule->getDistributionTime()),
            date("H:i", $rule->getDistributionTime())) ?>
    <? else : ?>
        <?= sprintf(_('Die Plätze in den betreffenden Veranstaltungen wurden am %s '.
            'um %s verteilt. Weitere Plätze werden evtl. über Wartelisten zur Verfügung gestellt.'), date("d.m.Y", $rule->getDistributionTime()),
            date("H:i", $rule->getDistributionTime())) ?>
    <? endif ?>
<? endif ?>
<br>
Es wird überschneidungsfrei jeweils eine (1) Veranstaltung pro Zuteilungsgruppe zugeteilt, wenn
Prioritäten dafür abgegeben wurden. Überschneidungen zu Veranstaltungen außerhalb dieses Anmeldesets werden
<strong>NICHT</strong> berücksichtigt!
<? if (!empty($rankinggroups)) : ?>
<ul>
    <? foreach ($rankinggroups as $group): ?>
    <li><?= $group['group_name'] ?> (Mindestanzahl abzugebener Prioritäten: <?= $group['min_amount_prios'] ?>)</li>
    <? endforeach; ?>
</ul>
<? endif ?>

