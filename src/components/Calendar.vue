<template>
	<div class="componentContainer">

		<h1>{{ $t('calendar.events') }}</h1>
		<div class="buttonContainer">
			<ui-icon-button @click="decMonth" icon="chevron_left" type="primary" class="buttonLeft"></ui-icon-button>
			<ui-icon-button @click="incMonth" icon="chevron_right" type="primary" class="buttonRight"></ui-icon-button>
		</div>


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
					<div v-for="week in events[eventos(n-1+increment)]">
						<span class="calendarDay" v-for="day in week" v-bind:class="[selectedClass(day), dayClass(day)]">
							<span v-if="day.date" v-bind:class="day.data.cssClass"  @click="openModal( day )">
								{{ day.date }}
							</span>
						</span>
					</div>
				</div>
			</div>
		</div>
		<ui-modal ref="events" size="normal" title="titol">
            <div slot="header">
                {{selected.day}} - {{selected.month+1}} - {{selected.year}}
                
	
            </div>
			<article v-for="event in modalEvent">
				<h4>{{event.titol}}</h4>
				<p v-html="event.contingut"></p>
			</article>
        </ui-modal>
	</div>
</template>



<script>


module.exports = {
	name: 'Calendar',
  	components: {

  	},
	'data': function(){ 
		return {
			mondayFirst: eval(this.$parent.$i18n.t('calendar.mondayFirst')),
			dayLabels: this.$parent.$i18n.t('calendar.weekShort'),
			increment: 0,
			events: [],
			monthArray: this.$parent.$i18n.t('calendar.months'),
			selected : {
				day : false,
				month : false,
				year : false
			},
			modalEvent: []
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
        openModal: function(day) {
        	this.modalEvent = day.data.events;
        	this.select(day);
            if( this.modalEvent.length>0 ) this.$refs.events.open();
        },
        closeModal: function(ref) {
            this.$refs.events[ref].close();
        },
		eventos: function(n) {
			var month = Date.today().add(n).month().getMonth();
			var year = Date.today().add(n).month().getYear() + 1900;
			var current = year+''+("0" + (month + 1)).slice(-2);
			return current;
		},
		calendar: function(n) {
			var squareIndex =  0;
			var mondayFirst = this.mondayFirst ? 1 : 0;
			var dateOfMonth = 1;
			var weeks = [];
			var month = Date.today().add(n).month().getMonth();
			var year = Date.today().add(n).month().getYear() + 1900;
			
			var firstDate = new Date(year, month, 1);
			var daysInMonth = Date.getDaysInMonth(year, month);
			var firstDayIndex = firstDate.getDay() - mondayFirst < 0 ? 6 : firstDate.getDay() - mondayFirst;
			var lastDate = firstDayIndex + daysInMonth;
			
			var current = year+''+("0" + (month + 1)).slice(-2);
		    var vm = this;
		    
		    if( vm.events[current] ) return vm.events[current];
		    var refIndex = 1;
	        this.$http.get('acte/'+current+'/i/'+this.$i18n.locale)
	        .then(function (response) {
		        while (squareIndex < lastDate) {
					var week = [];
					
					for (var d = 0; d < 7; d++) {
						if (squareIndex < firstDayIndex || squareIndex >= lastDate) {
							week.push({ date : false });
						} else {
							
							var eventsInDay=[];
							for (event in response.data) {
								if(response.data[event].publicacio && response.data[event].publicacio == year+''+("0" + (month + 1)).slice(-2)+''+("0" + (dateOfMonth)).slice(-2)) {
									eventsInDay.push(response.data[event]);
	        					}
							}
							
							var clase = '';
							if( Object.keys(eventsInDay).length > 0 ) {
								clase = 'event';
							}
							if( Date.today().getDate()==dateOfMonth && Date.today().getMonth() == month && (Date.today().getYear()+1900) == year) {
								clase = 'today';
							}

							week.push({
								date : dateOfMonth, 
								data : {
									day : dateOfMonth,
									month : month,
									year : year,
									cssClass : clase,
									events: eventsInDay,
									index: refIndex
								}
							});
							dateOfMonth++;
							refIndex++;
							
						}
						squareIndex++;
						
					}
					weeks.push(week);
					
				}
				vm.events[current] = weeks;
//var ya = Date.now();

//console.log(ya);
				//vm.events.unshift(vm.events.shift()); //aço força el event d'uptate del vue per a q pinte el calendari
				vm.events.push(vm.events.pop());
//console.log(Date.now() - ya);
				//console.log(vm.events);
				return weeks;

	        })
	        .catch(function (error) {
	            console.log(error);
	        });

		},
		getMonth: function(n) {
			return Date.today().add(n).month().getMonth();
		},
		getYear: function(n) {
			return Date.today().add(n).month().getYear() + 1900;
		},
		incMonth: function() {
			this.increment++;
			this.calendar(this.increment + 3);
		},
		decMonth: function() {
			this.increment--;
			this.calendar(this.increment);

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
	},
	mounted: function() {
		this.calendar(0);
		this.calendar(1);
		this.calendar(2);
		this.calendar(3);
	}
};
</script>


<style lang="less">

@import "../assets/less/defines.less";


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
		.ui-modal__container {
			article {
			    border-bottom: 1px solid #e1e1e1;
			    &:last-child {
			    	border-bottom:none;
			    }
			}
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
		  &:nth-last-child(2), &:last-child {
		    color: #ff7676;
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
		  	&.event {
				background-color: #ffeef0;
    			border: 1px dashed #d46b78;
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