<template>
	<div class="componentContainer">
		
		<ui-icon-button @click="decMonth" icon="chevron_left" type="primary" class="buttonLeft"></ui-icon-button>
		<ui-icon-button @click="incMonth" icon="chevron_right" type="primary" class="buttonRight"></ui-icon-button>

		<div class="calendarContainer">
			<div class="calendar" v-for="n in 4">
				<nav class="calendarHead">
	
					<b>{{ monthArray[getMonth(n-1+increment)] }}</b>
					<b>{{ getYear(n-1+increment)}}</b>
	
				</nav>
				<div class="labels">
					<span class="calendarLabel" v-for="label in dayLabelsFixed">{{ label }} </span>
				</div>
				<div class="calendarMonth">
					<div v-for="week in getCalendar(n-1+increment)">
						<span class="calendarDay" v-for="day in week" v-bind:class="[selectedClass(day), dayClass(day)]" @click="select(day)">
							<span v-if="day.date" v-bind:class="day.data.class">
								{{ day.date }}
							</span>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>



<script>

module.exports = {
	name: 'Calendar',
  	components: { },
	'data': function(){ 
		return {
			mondayFirst: eval(this.$parent.$i18n.t('calendar.mondayFirst')),
			dayLabels: this.$parent.$i18n.t('calendar.weekShort'),
			increment: 0,
			monthArray: this.$parent.$i18n.t('calendar.months'),
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
								year : year,
								class : Date.today().getDate()==dateOfMonth && Date.today().getMonth() == month && (Date.today().getYear()+1900) == year ? 'today' : 'normal',
							}
						});
						
						dateOfMonth++;
					}
					squareIndex++;
				}
				weeks.push(week);
			}
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


<style lang="less">

@import "../assets/less/defines.less";

.buttonLeft, .buttonRight {
		margin: 0 15px;
}

.buttonRight {
		float:right;
}	
.calendarContainer {
	display:flex;
	position: relative;
    width: 100%;
    justify-content:space-around;
    

	
	.calendarNav {
		cursor: pointer;
	}
    
    .calendar {
		padding: 0 15px;
		min-height: 240px;
		
		&:nth-child(4) {
			@media(max-width:@screenDesktop) {
				display:none;
			}
		}
		
		&:nth-child(3) {
			@media(max-width:@screenTablet) {
				display:none;
			}
		}
		
		&:nth-child(2) {
			@media(max-width:@screenMobile) {
				display:none;
			}
		}

	}
	.calendarHead,
	.calendarDay,
	.calendarLabel
	{
		user-select: none;
		text-align:center;
	}
	
	.calendarMonth {
		&>div {
		    display: flex;
		    flex-direction: row;
		    justify-content: center;
		}
	}
	
	.labels {
	    display: flex;
	    flex-direction: row;
	    justify-content: center;
	}
	

	
	span.calendarLabel{
	  display: inline-block;
	  box-sizing: border-box;
	  overflow: hidden;
	  text-align: center;
	  font-size:10px;
	  width:32px;
	  padding:5px 0;
	}
	
	span.calendarDay {
		display: inline-block;
		box-sizing: border-box;
		overflow: hidden;
		text-align: center;
		height: 32px;
		width: 32px;
		&.day {
		  cursor: pointer;
		  &:last-child {
		    color: #ff7676;
		    //color: white;
		    box-sizing: content-box;
		  }
		}
		span {
			border-radius: 20px;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 	32px;
			width: 32px;
		  	&.today {
			    background-color: yellow;
			    border: 2px dotted yellow;
			    color:black;
			}
		}
		
		
		&.selected {
			span {
				background: #2ecc71;
			    color: #fff;
			    font-weight: bold;
			}
		}
	
	}

}


</style>