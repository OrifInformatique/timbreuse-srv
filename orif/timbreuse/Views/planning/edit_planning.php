<style>

.grid-hour-input {
    display: grid;
    grid-template-columns: auto auto auto;
    grid-template-rows: auto;
}

.grid-container {
    display: grid;
    /* this template when big windows*/
    grid-template-columns: auto auto auto auto auto auto;
    grid-template-rows: auto auto auto auto;
    gap: 10px;
    padding: 10px;
}

#item1 { grid-area: 1 / 2 / span 1 / span 1 ; }
#item2 { grid-area: 1 / 3 / span 1 / span 1 ; }
#item3 { grid-area: 1 / 4 / span 1 / span 1; }
#item4 { grid-area: 1 / 5 / span 1 / span 1; }
#item5 { grid-area: 1 / 6 / span 1 / span 1; }
#item6 { grid-area: 2 / 1 / span 1 / span 1; }
#item7 { grid-area: 3 / 1 / span 1 / span 1; }
#item8 { grid-area: 2 / 2 / span 1 / span 1; }
#item9 { grid-area: 2 / 3 / span 1 / span 1; }
#item10 { grid-area: 2 / 4 / span 1 / span 1; }
#item11 { grid-area: 2 / 5 / span 1 / span 1; }
#item12 { grid-area: 2 / 6 / span 1 / span 1; }
#item13 { grid-area: 3 / 2 / span 1 / span 1; }
#item14 { grid-area: 3 / 3 / span 1 / span 1; }
#item15 { grid-area: 3 / 4 / span 1 / span 1; }
#item16 { grid-area: 3 / 5 / span 1 / span 1; }
#item17 { grid-area: 3 / 6 / span 1 / span 1; }
#item18 {
    grid-area: 4 / 1 / span 1 / span 6; 
    text-align: right;
}

@media only screen and (max-width: 992px) {
    .grid-container {
        display: grid;
        /* this template when small windows*/
        grid-template-columns: auto auto auto;
        grid-template-rows: auto auto auto auto auto auto auto auto auto auto auto auto;
        gap: 10px;
        padding: 10px;
    }

    #item1 { grid-area: 2 / 2 / span 1 / span 1 ; }
    #item2 { grid-area: 4 / 2 / span 1 / span 1 ; }
    #item3 { grid-area: 6 / 2 / span 1 / span 1; }
    #item4 { grid-area: 8 / 2 / span 1 / span 1; }
    #item5 { grid-area: 10 / 2 / span 1 / span 1; }
    #item6 { grid-area: 1 / 1 / span 1 / span 1; }
    #item7 { grid-area: 1 / 3 / span 1 / span 1; }
    #item8 { grid-area: 3 / 1 / span 1 / span 1; }
    #item9 { grid-area: 5 / 1 / span 1 / span 1; }
    #item10 { grid-area: 7 / 1 / span 1 / span 1; }
    #item11 { grid-area: 9 / 1 / span 1 / span 1; }
    #item12 { grid-area: 11 / 1 / span 1 / span 1; }
    #item13 { grid-area: 3 / 3 / span 1 / span 1; }
    #item14 { grid-area: 5 / 3 / span 1 / span 1; }
    #item15 { grid-area: 7 / 3 / span 1 / span 1; }
    #item16 { grid-area: 9 / 3 / span 1 / span 1; }
    #item17 { grid-area: 11 / 3 / span 1 / span 1; }
    #item18 {
        grid-area: 12 / 1 / span 1 / span 3;
        text-align: right;
    }
}

</style>
<form class="container grid-container">
    <div id="item1">
        Lundi
    </div>
    <div id="item2">
        Mardi
    </div>
    <div id="item3">
        Mercredi
    </div>
    <div id="item4">
        Jeudi
    </div>
    <div id="item5">
        Vendredi
    </div>
    <div id="item6">
        Temps exigé
    </div>
    <div id="item7">
        Temps non controlé
    </div>
    <div id="item8" class="form-group grid-hour-input" >
        <input id="dueHoursMonday" class="form-control" type="number" name="dueHoursMonday" list="defaultHours" step="1" min="00" max="10" required>
        <span>:</span>
        <input id="dueMinutesMonday" class="form-control" type="number" name="dueMinutesMonday" list="defaultMinutes" step="1" min="0" max="59" required>
        <span class="validity"></span>
    </div>
    <div id="item9" class="form-group grid-hour-input">
        <input id="dueHoursTuesday" class="form-control" type="number" name="dueHoursTuesday" list="defaultHours" step="1" min="00" max="10" required>
        <span>:</span>
        <input id="dueMinutesTuesday" class="form-control" type="number" name="dueMinutesTuesday" list="defaultMinutes" step="1" min="0" max="59" required>
        <span class="validity"></span>
    </div>
    <div id="item10" class="form-group grid-hour-input">
        <input id="dueHoursWednesday" class="form-control" type="number" name="dueHoursWednesday" list="defaultHours" step="1" min="00" max="10" required>
        <span>:</span>
        <input id="dueMinutesWednesday" class="form-control" type="number" name="dueMinutesWednesday" list="defaultMinutes" step="1" min="0" max="59" required>
        <span class="validity"></span>
    </div>
    <div id="item11" class="form-group grid-hour-input">
        <input id="dueHoursThursday" class="form-control" type="number" name="dueHoursThursday" list="defaultHours" step="1" min="00" max="10" required>
        <span>:</span>
        <input id="dueMinutesThursday" class="form-control" type="number" name="dueMinutesThursday" list="defaultMinutes" step="1" min="0" max="59" required>
        <span class="validity"></span>
    </div>
    <div id="item12" class="form-group grid-hour-input">
        <input id="dueHoursFriday" class="form-control" type="number" name="dueHoursFriday" list="defaultHours" step="1" min="00" max="10" required>
        <span>:</span>
        <input id="dueMinutesFriday" class="form-control" type="number" name="dueMinutesFriday" list="defaultMinutes" step="1" min="0" max="59" required>
        <span class="validity"></span>
    </div>
    <div id="item13" class="form-group grid-hour-input">
        <input id="offeredHoursMonday" class="form-control" type="number" name="offeredHoursMonday" list="defaultHours" step="1" min="00" max="10" required>
        <span>:</span>
        <input id="offeredMinutesMonday" class="form-control" type="number" name="offeredMinutesMonday" list="defaultMinutes" step="1" min="0" max="59" required>
        <span class="validity"></span>
    </div>
    <div id="item14" class="form-group grid-hour-input">
        <input id="offeredHoursTuesday" class="form-control" type="number" name="offeredHoursTuesday" list="defaultHours" step="1" min="00" max="10" required>
        <span>:</span>
        <input id="offeredMinutesTuesday" class="form-control" type="number" name="offeredMinutesTuesday" list="defaultMinutes" step="1" min="0" max="59" required>
        <span class="validity"></span>
    </div>
    <div id="item15" class="form-group grid-hour-input">
        <input id="offeredHoursWednesday" class="form-control" type="number" name="offeredHoursWednesday" list="defaultHours" step="1" min="00" max="10" required>
        <span>:</span>
        <input id="offeredMinutesWednesday" class="form-control" type="number" name="offeredMinutesWednesday" list="defaultMinutes" step="1" min="0" max="59" required>
        <span class="validity"></span>
    </div>
    <div id="item16" class="form-group grid-hour-input">
        <input id="offeredHoursThursday" class="form-control" type="number" name="offeredHoursThursday" list="defaultHours" step="1" min="00" max="10" required>
        <span>:</span>
        <input id="offeredMinutesThursday" class="form-control" type="number" name="offeredMinutesThursday" list="defaultMinutes" step="1" min="0" max="59" required>
        <span class="validity"></span>
    </div>
    <div id="item17" class="form-group grid-hour-input">
        <input id="offeredHoursFriday" class="form-control" type="number" name="offeredHoursFriday" list="defaultHours" step="1" min="00" max="10" required>
        <span>:</span>
        <input id="offeredMinutesFriday" class="form-control" type="number" name="offeredMinutesFriday" list="defaultMinutes" step="1" min="0" max="59" required>
        <span class="validity"></span>
    </div>
    <div id="item18" class="form-group">
        <a class="btn btn-link" href="">Annuler</a>
        <input class="btn btn-primary" type="submit" value="Enregistrer" >
    </div>

    <datalist id="defaultHours">
      <option value="04">
      <option value="06">
      <option value="08">
    </datalist>

    <datalist id="defaultMinutes">
      <option value="00">
      <option value="06">
      <option value="12">
      <option value="15">
      <option value="30">
      <option value="45">
    </datalist>

</form>

