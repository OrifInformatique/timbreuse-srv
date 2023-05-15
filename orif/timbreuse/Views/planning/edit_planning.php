<style>

input:invalid {
    border-color: #ae0000;
    border-width: 2px;
}

.grid-input-center {
    text-align: center;
    justify-items: center;
    align-items: center;
}

.grid-hour-input {
    display: grid;
    grid-template-columns: repeat(3, auto);
    grid-template-rows: auto;
}

.grid-container {
    display: grid;
    /* this template when big windows*/
    grid-template-columns: repeat(6, auto);
    grid-template-rows: repeat(5, auto);
    gap: 20px;
    padding: 10px;
}

#dateBeginEnd {
    display: grid;
    grid-template-columns: repeat(4, auto);
    grid-template-rows: repeat(1, auto);
    text-align: center;
    grid-area: 1 / 1 / span 1 / span 6 ;
}

#mondayLabel {
    grid-area: 2 / 2 / span 1 / span 1 ;
    text-align: center;
}

#tuesdayLabel {
    grid-area: 2 / 3 / span 1 / span 1 ;
    text-align: center;
}

#wednesdayLabel {
    grid-area: 2 / 4 / span 1 / span 1;
    text-align: center;
}

#thursdayLabel {
    grid-area: 2 / 5 / span 1 / span 1;
    text-align: center;
}

#fridayLabel {
    grid-area: 2 / 6 / span 1 / span 1;
    text-align: center;
}

#dueTimeLabel { grid-area: 3 / 1 / span 1 / span 1; }

#offeredTimeLabel { grid-area: 4 / 1 / span 1 / span 1; }

#mondayDueTimeInput {
    grid-area: 3 / 2 / span 1 / span 1;
}

#tuesdayDueTimeInput {
    grid-area: 3 / 3 / span 1 / span 1;
}

#wednesdayDueTimeInput {
    grid-area: 3 / 4 / span 1 / span 1;
}

#thursdayDueTimeInput {
    grid-area: 3 / 5 / span 1 / span 1;
}

#fridayDueTimeInput {
    grid-area: 3 / 6 / span 1 / span 1;
}

#mondayOfferedTimeInput {
    grid-area: 4 / 2 / span 1 / span 1;
}

#tuesdayOfferedTimeInput {
    grid-area: 4 / 3 / span 1 / span 1;
}

#wednesdayOfferedTimeInput {
    grid-area: 4 / 4 / span 1 / span 1;
}

#thursdayOfferedTimeInput {
    grid-area: 4 / 5 / span 1 / span 1;
}

#fridayOfferedTimeInput {
    grid-area: 4 / 6 / span 1 / span 1;
}

#buttonsSpace {
    grid-area: 5 / 1 / span 1 / span 6; 
    text-align: right;
}

@media only screen and (max-width: 992px) {
    .grid-container {
        display: grid;
        /* this template when small windows*/
        grid-template-columns: repeat(2, auto);
        grid-template-rows: repeat(12, auto);
        gap: 20px;
        padding: 10px;
    }

    #dateBeginEnd {
        display: grid;
        grid-template-columns: repeat(1, auto);
        grid-template-rows: repeat(4, auto);
        text-align: center;
        grid-area: 1 / 1 / span 4 / span 2 ;
    }
    #mondayLabel { grid-area: 6 / 1 / span 1 / span 2 ; }
    #tuesdayLabel { grid-area: 8 / 1 / span 1 / span 2 ; }
    #wednesdayLabel { grid-area: 10 / 1 / span 1 / span 2; }
    #thursdayLabel { grid-area: 12 / 1 / span 1 / span 2; }
    #fridayLabel { grid-area: 14 / 1 / span 1 / span 2; }
    #dueTimeLabel {
        grid-area: 5 / 1 / span 1 / span 1;
        text-align: center;
    }
    #offeredTimeLabel {
        grid-area: 5 / 2 / span 1 / span 1;
        text-align: center;
    }
    #mondayDueTimeInput {
        grid-area: 7 / 1 / span 1 / span 1;
    }
    #tuesdayDueTimeInput {
        grid-area: 9 / 1 / span 1 / span 1;
    }
    #wednesdayDueTimeInput {
        grid-area: 11 / 1 / span 1 / span 1;
    }
    #thursdayDueTimeInput {
        grid-area: 13 / 1 / span 1 / span 1;
    }
    #fridayDueTimeInput {
        grid-area: 15 / 1 / span 1 / span 1;
    }
    #mondayOfferedTimeInput {
        grid-area: 7 / 2 / span 1 / span 1;
    }
    #tuesdayOfferedTimeInput {
        grid-area: 9 / 2 / span 1 / span 1;
    }
    #wednesdayOfferedTimeInput {
        grid-area: 11 / 2 / span 1 / span 1;
    }
    #thursdayOfferedTimeInput {
        grid-area: 13 / 2 / span 1 / span 1;
    }
    #fridayOfferedTimeInput {
        grid-area: 15 / 2 / span 1 / span 1;
    }
    #buttonsSpace {
        grid-area: 16 / 1 / span 1 / span 2;
        text-align: right;
    }
}

</style>
<section class="container">
    <h3><?= esc($h3title) ?></h3>
    <?php if (! empty(service('validation')->getErrors())) : ?>
        <div class="alert alert-danger" role="alert">
            <?= service('validation')->listErrors() ?>
        </div>
    <?php endif ?>
    <form class="grid-container" method="post">
        <?= csrf_field() ?>
        <div id="dateBeginEnd" class="form-group">
            <?php if (!(isset($defaultPlanning) and $defaultPlanning)): ?>
                <label id="dateBeginLabel" for="dateBegin"><?=$labels['dateBegin']?></label>
                <input id="dateBegin" class="form-control" type="date" name="dateBegin" value="<?=$date_begin?>" required min="1948-04-17">
                <label id="dateBeginLabel" for="dateEnd"><?=$labels['dateEnd']?></label>
                <input id="dateEnd" class="form-control" type="date" name="dateEnd" value="<?=$date_end?>" min="1948-04-17">
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
        <a class="btn btn-link" href=""><?= esc($labels['cancel'])?></a>
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
</section>

