Hallo Welt! <?= $course_id ?>
<div id="mount_me"></div>
<!-- TODO: Vue.js render timetable with ranking items and previous preferences, submit button axios POST to apply endpoint -->
<div data-dialog-button>
    <button>asdf</button>
    <?= Studip\Button::createAccept(_('Speichern'), '', ['data-dialog' => 'size=big']) ?>
    <?= Studip\Button::createCancel(_('Schließen'), 'cancel') ?>
</div>