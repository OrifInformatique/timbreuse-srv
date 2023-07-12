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

.grid-4-cols-to-1-col {
    display: grid;
    grid-template-columns: repeat(4, auto);
    grid-template-rows: repeat(1, auto);
    text-align: center;
    gap: 20px;
}

#titlePlanningLabel {
    grid-area: 2 / 1 / span 1 / span 1 ;
}

#titlePlanning {
    grid-area: 2 / 2 / span 1 / span 3 ;
}

#dateBeginEnd {
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

#rateSpace {
    grid-area: 5 / 1 / span 1 / span 2; 
    display: flex;
    gap: 20px;
}

#buttonsSpace {
    grid-area: 5 / 5 / span 1 / span 2; 
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

    .grid-4-cols-to-1-col {
        display: flex;
        flex-direction: column;
        text-align: center;
        gap:5px;
    }

    #dateBeginEnd {
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

    #rateSpace {
        grid-area: 16 / 1 / span 1 / span 1; 
    }

    #buttonsSpace {
        grid-area: 16 / 2 / span 1 / span 1;
        text-align: right;
    }
}
</style>
