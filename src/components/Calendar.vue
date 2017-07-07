// Requires datejs
// https://github.com/datejs/Datejs

<template>
	<div class="calendarContainer">
		<a @click="decMonth" class="calendar--nav--chevron">d</a>
		<a @click="incMonth" class="calendar--nav--chevron">b</a>
		<div class="calendar" v-for="n in 3">
			<nav class="calendar--nav">

				<b>{{ monthArray[getMonth(n-1+increment)] }}</b>
				<b>{{ getYear(n-1+increment)}}</b>

			</nav>
			<div class="labels">
				<span class="calendar--label" v-for="label in dayLabelsFixed">{{ label }} </span>
			</div>
			<div class="calendar--month">
				<div v-for="week in getCalendar(n-1+increment)">
					<span class="calendar--day" v-for="day in week" v-bind:class="[selectedClass(day), dayClass(day)]" @click="select(day)">
						<span v-if="day.date">
							{{ day.date }}
						</span>
					</span>
				</div>
			</div>
		</div>
	</div>
</template>



<script>
module.exports = {
	'data': function(){ 
		return {
			mondayFirst: true,
			dayLabels: [ 'Diu', 'Dill', 'Dima', 'Dime', 'Dijo', 'Dive', 'Diss'],
			increment: 0,
			monthArray: ['Gener', 'Febrer', 'Març', 'Abril', 'Maig', 'Juny', 'Juliol', 'Agost', 'Setembre', 'Octubre', 'Novembre', 'Decembre'],
			selected : {
				day : false,
				month : false,
				year : false
			}
		}
	},
	'computed': {
		dayLabelsFixed: function() {
			if(this.mondayFirst) {
				var b = this.dayLabels.shift();
				this.dayLabels.push(b);
			}
			return this.dayLabels;
		}
	},
	methods: {
		getMonth: function(n) {
			return Date.today().add(n).month().getMonth();
		},
		getYear: function(n) {
			return Date.today().add(n).month().getYear() + 1900;
		},
		getCalendar: function(n) {
			
			var squareIndex =  this.mondayFirst ? 1 : 0;
			var dateOfMonth = 1;
			var weeks = [];
			var month = Date.today().add(n).month().getMonth();
			var year = Date.today().add(n).month().getYear() + 1900;
			
			var firstDate = new Date(year, month, 1);
			var daysInMonth = Date.getDaysInMonth(year, month);
			var firstDayIndex = firstDate.getDay();
			var lastDate = firstDate.getDay() + Date.getDaysInMonth(year, month);
			
			while (squareIndex < (lastDate)) {
				var week = [];
				for (var d = 0; d < 7; d++) {
					if (squareIndex < firstDayIndex || squareIndex >= (lastDate)) {
						week.push({ date : false });
						
					} else {
						week.push({
							date : dateOfMonth, 
							data : {
								day : dateOfMonth,
								month : month,
								year : year
							}
						});
						dateOfMonth++;
					}
					squareIndex++;
				}
				weeks.push(week);
			}
			console.log(weeks);
			return weeks;
			
		},
		incMonth: function() {
			this.increment++;
		},
		decMonth: function() {
			this.increment--;
		},
		select : function(day){
			if(this.isSelected(day)){
				this.selected = {
					day : false,
					month : false,
					year : false 
				};
				return true;
			}
			return this.selected = {
				day : day.data.day,
				month : day.data.month,
				year : day.data.year 
			};
			
		},
		isSelected : function(day){
			if(day.date === false){
				return false;
			}
			if(
				this.selected.year == day.data.year && 
				this.selected.month == day.data.month &&
				this.selected.day == day.data.day){
				return true;
			}
			return false;
		},
		selectedClass : function(day){
			if(this.isSelected(day)){
				return 'selected';
			}
			return '';
		},
		dayClass : function(day){
			if(this.isDay(day)){
				return 'day';
			}
			return '';
		},
		isDay : function(day){
			return day.date !== false;
		}
	}
};
</script>


<style>
.calendarContainer {
	display:flex;
}
.calendar {
	margin: 20px 30px;
}
.calendar--nav,
.calendar--day,
.calendar--label
{
	user-select: none;
}
a.calendar--nav--chevron {
  display: inline-block;
  padding: 10px 15px;
  background: #2ecc71;
  color: #fff;
  vertical-align: middle;
  line-height: 20px;
  height: 40px;
}

span.calendar--label, span.calendar--day {
  display: inline-block;
  width: 40px;
  padding: 10px 5px;
  box-sizing: border-box;
  overflow: hidden;
  text-align: center;
}
span.calendar--label.day, span.calendar--day.day {
  cursor: pointer;
}
span.calendar--day.selected {
  background: #2ecc71;
  color: #fff;
  font-weight: bold;
}

</style>