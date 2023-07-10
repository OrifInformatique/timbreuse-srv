<?php
// to rename, this view is also use to create planning
?>
<?= view('Timbreuse\Views\planning\edit_planning_style') ?>
<section class="container">
    <h3><?= esc($h3title) ?></h3>
    <?php if (! empty(service('validation')->getErrors())) : ?>
        <div class="alert alert-danger" role="alert">
            <?= service('validation')->listErrors() ?>
        </div>
    <?php endif ?>
    <form class="grid-container" method="post">
        <?= csrf_field() ?>
        <div id="dateBeginEnd" class="form-group grid-4-cols-to-1-col">
            <?php if (!(isset($defaultPlanning) and $defaultPlanning)): ?>
                <label id="dateBeginLabel" for="dateBegin"><?=esc($labels['dateBegin'])?></label>
                <input id="dateBegin" class="form-control" type="date" name="dateBegin" value="<?=esc($date_begin)?>" required min="1948-04-17">
                <label id="dateEndLabel" for="dateEnd"><?=$labels['dateEnd']?></label>
                <input id="dateEnd" class="form-control" type="date" name="dateEnd" value="<?=esc($date_end)?>" min="1948-04-17">
                <label id="titlePlanningLabel" for="titlePlanning"><?=esc($labels['title'])?></label>
                <input id="titlePlanning" class="form-control" type="text" name="title" value="<?=esc($planningTitle)?>">
            <?php endif ?>
        </div>
        <div id="mondayLabel">
            <?= esc($labels['monday']) ?>
        </div>
        <div id="tuesdayLabel">
            <?= esc($labels['tuesday']) ?>
        </div>
        <div id="wednesdayLabel">
            <?= esc($labels['wednesday']) ?>
        </div>
        <div id="thursdayLabel">
            <?= esc($labels['thursday']) ?>
        </div>
        <div id="fridayLabel">
            <?= esc($labels['friday']) ?>
        </div>
        <div id="dueTimeLabel">
            <?= esc($labels['dueTime']) ?>
        </div>
        <div id="offeredTimeLabel">
            <?= esc($labels['offeredTime']) ?>
        </div>
        <div id="mondayDueTimeInput" class="form-group grid-hour-input grid-input-center" >
        <input id="dueHoursMonday" class="form-control " type="number" name="dueHoursMonday" list="defaultHours" step="1" min="0" max="10" required value="<?=esc($due_time_monday['hour'])?>">
            <span>:</span>
            <input id="dueMinutesMonday" class="form-control" type="number" name="dueMinutesMonday" list="defaultMinutes" step="1" min="0" max="59" required value="<?=esc($due_time_monday['minute'])?>">
        </div>
        <div id="tuesdayDueTimeInput" class="form-group grid-hour-input grid-input-center">
        <input id="dueHoursTuesday" class="form-control" type="number" name="dueHoursTuesday" list="defaultHours" step="1" min="0" max="10" required value="<?=esc($due_time_tuesday['hour'])?>">
            <span>:</span>
            <input id="dueMinutesTuesday" class="form-control" type="number" name="dueMinutesTuesday" list="defaultMinutes" step="1" min="0" max="59" required value="<?=esc($due_time_tuesday['minute'])?>">
        </div>
        <div id="wednesdayDueTimeInput" class="form-group grid-hour-input grid-input-center">
        <input id="dueHoursWednesday" class="form-control" type="number" name="dueHoursWednesday" list="defaultHours" step="1" min="0" max="10" required value="<?=esc($due_time_wednesday['hour'])?>">
            <span>:</span>
            <input id="dueMinutesWednesday" class="form-control" type="number" name="dueMinutesWednesday" list="defaultMinutes" step="1" min="0" max="59" required value="<?=esc($due_time_wednesday['minute'])?>">
        </div>
        <div id="thursdayDueTimeInput" class="form-group grid-hour-input grid-input-center">
        <input id="dueHoursThursday" class="form-control" type="number" name="dueHoursThursday" list="defaultHours" step="1" min="0" max="10" required value="<?=esc($due_time_thursday['hour'])?>">
            <span>:</span>
            <input id="dueMinutesThursday" class="form-control" type="number" name="dueMinutesThursday" list="defaultMinutes" step="1" min="0" max="59" required value="<?=esc($due_time_thursday['minute'])?>">
        </div>
        <div id="fridayDueTimeInput" class="form-group grid-hour-input grid-input-center">
        <input id="dueHoursFriday" class="form-control" type="number" name="dueHoursFriday" list="defaultHours" step="1" min="0" max="10" required value="<?=esc($due_time_friday['hour'])?>">
            <span>:</span>
            <input id="dueMinutesFriday" class="form-control" type="number" name="dueMinutesFriday" list="defaultMinutes" step="1" min="0" max="59" required value="<?=esc($due_time_friday['minute'])?>">
        </div>
        <div id="mondayOfferedTimeInput" class="form-group grid-hour-input grid-input-center">
            <input id="offeredHoursMonday" class="form-control" type="number" name="offeredHoursMonday" list="defaultHours" step="1" min="0" max="10" required value="<?=esc($offered_time_monday['hour'])?>">
            <span>:</span>
            <input id="offeredMinutesMonday" class="form-control" type="number" name="offeredMinutesMonday" list="defaultMinutes" step="1" min="0" max="59" required value="<?=esc($offered_time_monday['minute'])?>">
        </div>
        <div id="tuesdayOfferedTimeInput" class="form-group grid-hour-input grid-input-center">
            <input id="offeredHoursTuesday" class="form-control" type="number" name="offeredHoursTuesday" list="defaultHours" step="1" min="0" max="10" required value="<?=esc($offered_time_tuesday['hour'])?>">
            <span>:</span>
            <input id="offeredMinutesTuesday" class="form-control" type="number" name="offeredMinutesTuesday" list="defaultMinutes" step="1" min="0" max="59" required value="<?=esc($offered_time_tuesday['minute'])?>">
        </div>
        <div id="wednesdayOfferedTimeInput" class="form-group grid-hour-input grid-input-center">
            <input id="offeredHoursWednesday" class="form-control" type="number" name="offeredHoursWednesday" list="defaultHours" step="1" min="0" max="10" required value="<?=esc($offered_time_wednesday['hour'])?>">
            <span>:</span>
            <input id="offeredMinutesWednesday" class="form-control" type="number" name="offeredMinutesWednesday" list="defaultMinutes" step="1" min="0" max="59" required value="<?=esc($offered_time_wednesday['minute'])?>">
        </div>
        <div id="thursdayOfferedTimeInput" class="form-group grid-hour-input grid-input-center">
            <input id="offeredHoursThursday" class="form-control" type="number" name="offeredHoursThursday" list="defaultHours" step="1" min="0" max="10" required value="<?=esc($offered_time_thursday['hour'])?>">
            <span>:</span>
            <input id="offeredMinutesThursday" class="form-control" type="number" name="offeredMinutesThursday" list="defaultMinutes" step="1" min="0" max="59" required value="<?=esc($offered_time_thursday['minute'])?>">
        </div>
        <div id="fridayOfferedTimeInput" class="form-group grid-hour-input grid-input-center">
            <input id="offeredHoursFriday" class="form-control" type="number" name="offeredHoursFriday" list="defaultHours" step="1" min="0" max="10" required value="<?=esc($offered_time_friday['hour'])?>">
            <span>:</span>
            <input id="offeredMinutesFriday" class="form-control" type="number" name="offeredMinutesFriday" list="defaultMinutes" step="1" min="0" max="59" required value="<?=esc($offered_time_friday['minute'])?>">
        </div>
        <div id="buttonsSpace" class="form-group">
        <a class="btn btn-secondary" href="<?=esc($cancelLink)?>"><?= esc($labels['cancel'])?></a>
        <input class="btn btn-primary" type="submit" value=<?= esc($labels['save'])?>>
        </div>

        <datalist id="defaultHours">
          <option value="0">
          <option value="1">
          <option value="2">
          <option value="4">
          <option value="7">
          <option value="8">
        </datalist>

        <datalist id="defaultMinutes">
          <option value="0">
          <option value="6">
          <option value="12">
          <option value="15">
          <option value="30">
          <option value="45">
        </datalist>
        <input type="hidden" name="planningId" value="<?=esc($planningId ?? null)?>">
        <input type="hidden" name="timUserId" value="<?=esc($timUserId ?? null)?>">

    </form>
    <details>
        <summary><?= esc(ucfirst(lang('tim_lang.help'))) ?></summary>
        <?= lang('tim_lang.planningExplanation1') ?>
        <ul>
            <?= lang('tim_lang.planningExplanation2') ?>
        </ul>
    </details>
</section>

