import "./bootstrap";
import Alpine from "alpinejs";
import "flowbite";
import "flowbite-datepicker";
import DateRangePicker from "flowbite-datepicker/DateRangePicker";
import Datepicker from "flowbite-datepicker/Datepicker";

window.Alpine = Alpine;

Alpine.start();

const dateRangePickerEl = document.getElementById("dateRangePickerId");
new DateRangePicker(dateRangePickerEl, {
    // options
});

const datepickerEl = document.getElementById("datepickerId");
new Datepicker(datepickerEl, {
    // options
});
